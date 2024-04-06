<?php

use App\Domain\Auth\Entities\Permission;
use App\Domain\Auth\Entities\Role;
use App\Domain\Auth\Entities\User;
use App\Domain\Inventory\Entities\InventoryDetail;
use App\Domain\Shared\Defaults;
use App\Domain\Shared\Entities\Company;
use App\Domain\Shared\Entities\Profile;
use App\Domain\Shared\Lists;
use App\Domain\Shared\Repositories\AnnouncementRepository;
use App\Exceptions\Handler;
use App\Exceptions\MediaManagerException;
use App\Models\GeneralSetting;
use App\Services\Tenancy\Tenancy;
use BeyondCode\ServerTiming\ServerTiming;
use Hyn\Tenancy\Environment;
use Hyn\Tenancy\Models\Website;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;

function get_labels_by($getBy) {
    $labels = ['week' =>['Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab', 'Dom']];
    return $labels[$getBy];
}
function is_array_fill($array)
{
    return is_array($array) && count($array);
}

function parse_formatted_time($timeData)
{
    if (preg_match('/^\d{1,3}(:\d{1,2})?$/', $timeData) !== 1) {
        return 0;
    }
    $parts = explode(':', $timeData);

    return count($parts) > 1
        ? $parts[0] * 60 + $parts[1]
        : $parts[0] * 60;
}

function parse_duration_to_time($duration)
{
    $minutes = $duration % 60;
    $hours   = intdiv($duration, 60);

    return $minutes === 0
        ? $hours
        : str_pad($hours, 2, '0', STR_PAD_LEFT) . ':' .
        str_pad($minutes, 2, '0', STR_PAD_LEFT);
}

function keys_for_cell_values($row, $childrenData)
{
    $firstElement = (array) $row->first();
    if (!is_array($firstElement) || empty($firstElement)) {
        return [];
    }
    $ignoredKeys = collect(array_keys($firstElement))
        ->filter(function ($key) {
            return Str::startsWith($key, '_');
        })
        ->values()
        ->toArray();

    return array_diff_key($firstElement, $childrenData + array_combine($ignoredKeys, $ignoredKeys));
}

/**
 * Replace in a string the properties of an entity that match with the keys entered on replacements array and
 * was found as placeholders in target string, if entity is null, just make a simple replace_string
 *
 * Examples:
 *
 * $replaced = get_replacements_by_vars($workOrder, ['number' => 'number', 'work_performed' => 'job.denomination'],
 *   'The Work Order {{ number }} with Work Performed: {{ work_performed }} was created successfully!'
 * );
 *
 * $replaced = get_replacements_by_vars($workOrder, [
 *    'contact_name' => 'customer.defaultContact.name',
 *    'sn'           => 'part_serial_number',
 *    'description'  => 'part_description',
 * ], [
 *   'Dear {{ contactName }} your Work Order was created successfully!',
 *   'The Serial Number is {{ sn }} and the Description: {{ description }}',
 * ]);
 *
 *
 * @param mixed           $entity       The entity where to find the replacements placeholder
 * @param array           $replacements The array of search and replace data
 * @param string|string[] $target       The string where to find the placeholders
 *
 * @return array|string|string[]
 */
function get_replacements_by_vars($entity, array $replacements, $target)
{
    $captures     = [];
    $castedTarget = implode(' ', Arr::wrap($target));

    // Capture all var with the form {{ varName }}, {{varname}}, {{ var_name }}, {{var_name}}, {{VARNAME}}, and so far
    preg_match_all('#\{{2}[^\}]+\}{2}#', $castedTarget, $captures);
    if (empty($captures) || empty($captures[0])) {
        return $target;
    }

    $replaced = [];
    foreach ($captures[0] as $capture) {
        $var = Str::snake(strtolower(trim($capture, '{} ')));
        if (empty($replacements[$var]) || in_array($replacements[$var], $replaced)) {
            continue;
        }
        $replaced[]             = $replacements[$var];
        $replacements[$capture] = !empty($entity)
            ? data_get($entity, $replacements[$var], $replacements[$var])
            : $replacements[$var];
        unset($replacements[$var]);
    }

    return is_array($target)
        ? collect($target)->map(function ($item) use ($replacements, $target) {
            return str_replace(array_keys($replacements), array_values($replacements), $item);
        })->toArray()
        : str_replace(array_keys($replacements), array_values($replacements), $target);
}

function tracking_link($number, $shippingService, $default = null)
{
    $default = empty($number) ? $default : $number;

    return !empty($number) && !empty($shippingService->tracking_url)
        ? sprintf(
            '<a href="%s" target="_blank">%s</a>',
            str_replace('{tracking_number}', str_replace(' ', '', $number), $shippingService->tracking_url),
            $number,
        )
        : $default;
}

function clean_dropdown_fields($fields, $dropdownFields)
{
    foreach ($dropdownFields as $dropdownField) {
        if (array_key_exists($dropdownField, $fields) && !((int) $fields[$dropdownField] > 0)) {
            $fields[$dropdownField] = null;
        }
    }

    return $fields;
}

function prepareCCFromRequest($delimiter, $str)
{
    return $str ? standardizeEmails(explode($delimiter, str_replace(' ', '', $str))) : [];
}

function standardizeEmails($emails)
{
    if (is_array($emails)) {
        return collect($emails)
            ->transform(fn($email) => trim(Str::lower($email)))
            ->toArray();
    }

    return trim(Str::lower($emails));
}

function is_money_data($childName)
{
    $names = [
        'price',
        'estimated_value',
        'cost',
        'freight',
        'task',
        'misc',
        'total_due',
        'total',
        'balance',
        'amount',
        'order_total',
        'total_order',
        'profit',
        'sum_parts',
        'sum_labor',
    ];

    foreach ($names as $priceName) {
        if (strpos(strtolower($childName), $priceName) !== false) {
            return true;
        }
    }

    return false;
}

function has_children($data, $childData)
{
    $nulls = 0;
    foreach ($childData as $childName => $childLabel) {
        if (empty($data->$childName)) {
            $nulls++;
        }
    }

    return $nulls < count($childData);
}

/**
 * @param Profile|null $profile
 *
 * @return string
 */
function get_initials_name_by_profile($profile)
{
    if ($profile === null || (empty($profile->first_name) && empty($profile->last_name))) {
        return '';
    }
    $firstName        = $profile->first_name ?: '';
    $lastName         = $profile->last_name ?: '';
    $initialDelimeter = $lastName && $firstName ? '. ' : '';
    $firstNameInitial = $firstName && $lastName ? substr($firstName, 0, 1) . $initialDelimeter : $firstName;

    return $lastName && !$firstName ? $lastName : $firstNameInitial . $lastName;
}

function clean_date_fields($fields, $dateFields)
{
    foreach ($dateFields as $dateField) {
        if (
            array_key_exists($dateField, $fields)
            && (trim($fields[$dateField]) === '' || $fields[$dateField] === '0000-00-00')
        ) {
            $fields[$dateField] = null;
        }
    }

    return $fields;
}

function review_checked_fields($fields, $checkedFields)
{
    foreach ($checkedFields as $checkedField) {
        $fields[$checkedField] = array_key_exists($checkedField, $fields) ? (int) $fields[$checkedField] : 0;
    }

    return $fields;
}

/**
 * Returns an array with the differences between $array1 and $array2
 *
 * @param array $array1
 * @param array $array2
 *
 * @return array
 */
function array_compare($array1, $array2)
{
    $result = [];

    foreach ($array1 as $key => $value) {
        if (!is_array($array2) || !array_key_exists($key, $array2)) {
            $result[$key] = $value;
            continue;
        }

        if (is_array($value)) {
            $recursiveArrayDiff = array_compare($value, $array2[$key]);

            if (count($recursiveArrayDiff)) {
                $result[$key] = $recursiveArrayDiff;
            }

            continue;
        }

        if ($value !== $array2[$key]) {
            $result[$key] = $value;
        }
    }

    return $result;
}

function array_organize($array, $key, $value = null)
{
    $newArray = [];

    foreach ($array as $row) {
        $newArray[$row[$key]] = $value != '' ? $row[$value] : $row;
    }

    return $newArray;
}

function get_user_center_id()
{
    return Auth::user()->center->id;
}

function today_date($format = 'm/d/Y')
{
    return Carbon::now()->format($format);
}

/**
 * @param null $format
 *
 * @return Carbon|string
 */
function today_now($format = null)
{
    return !$format ? Carbon::now() : Carbon::now()->format($format);
}

function convert_us_date_to_carbon($date, $format = 'm/d/Y'): Carbon
{
    if ($date instanceof \Carbon\Carbon || $date instanceof Carbon) {
        Log::error(
            'Converting an already Carbon instance: ' . json_encode(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS))
        );

        return $date;
    }

    return Carbon::createFromFormat($format, $date);
}

function convert_us_date_to_db($date, string $format = 'm/d/Y', string $newFormat = 'Y-m-d'): string
{
    return Carbon::createFromFormat($format, $date)->format($newFormat);
}

function convert_date_format($date, string $format = 'm/d/Y', string $newFormat = 'Y-m-d'): string
{
    return Carbon::createFromFormat($format, $date)->format($newFormat);
}

function format_date($date, $format = 'Y-m-d'): string
{
    return Carbon::create($date)->format($format);
}

function current_diff_in_days($date, $absolute = false)
{
    return Carbon::parse($date)->diffInDays(today_now(), $absolute);
}

function is_uri_segment($uris, $uriToCheck = 1)
{
    return in_array(Request::segment($uriToCheck), Arr::wrap($uris));
}

function active_if_uri($uris)
{
    foreach (Arr::wrap($uris) as $uri) {
        if (strpos(Request::path(), $uri) === 0) {
            return 'active';
        }
    }
}

function active_if_uri_segment($uris, $uriToCheck = 1)
{
    return is_uri_segment($uris, $uriToCheck) ? 'active' : null;
}

/**
 * Return 'active' if $routes is the same than the current one.
 *
 * @param mixed $routes
 * @param null  $totalRouteSegments
 *
 * @return null|string
 */
function active_if_route($routes, $totalRouteSegments = null)
{
    $routes       = Arr::wrap($routes);
    $routeName    = Request::route()->getName() ?? str_replace('/', '.', Request::decodedPath());
    $currentRoute = $routeName;

    if ($totalRouteSegments > 0) {
        $currentRoute = '';
        $partsRoute   = explode('.', $routeName);
        foreach ($partsRoute as $key => $partRoute) {
            if ($key + 1 <= $totalRouteSegments) {
                $currentRoute .= $partRoute . '.';
            }
        }
        $currentRoute = trim($currentRoute, '.');
    }

    $matches = array_filter($routes, function ($element) use ($currentRoute) {
        return preg_match(sprintf('/%s/', str_replace(['.', '*'], ['\.', '.*'], $element)), $currentRoute);
    });

    return !empty($matches) ? 'active' : null;
}

function route_start_with($routes)
{
    $routes           = is_array($routes) ? $routes : [$routes];
    $currentRouteName = Route::getCurrentRoute()->getName();

    foreach ($routes as $route) {
        if (Str::is($route . '*', $currentRouteName)) {
            return true;
        }
    }

    return false;
}

function url_start_with($urls)
{
    $urls       = is_array($urls) ? $urls : [$urls];
    $currentUri = Request::getPathInfo();

    foreach ($urls as $url) {
        if (Str::is('/' . $url . '*', $currentUri)) {
            return true;
        }
    }

    return false;
}

function toJsJson($var)
{
    return addslashes(json_encode($var));
}

function reportException($e)
{
    if (app()->environment('local')) {
        throw $e;
    }

    app(Handler::class)->report($e);
}

function reports_view_pdf($view, array $data = [], $returnView = false)
{
    $customReport = 'reports.customs.' . company()->slug_override . '.' . $view;
    $viewPath     = View::exists($customReport) ? $customReport : 'reports.' . $view;

    if ($returnView) {
        return response()->view($viewPath, $data);
    }

    $view = View::make($viewPath, $data)->render();
    $view = preg_replace('/>\s+</', '><', $view);

    $pdf = App::make('dompdf.wrapper');
    if (app()->environment(['local', 'testing'])) {
        $dompdf = $pdf->getDomPDF();
        $dompdf->setHttpContext(stream_context_create([
            'ssl' => [
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true,
            ],
        ]));
    }
    $pdf->loadHTML($view);

    return $pdf;
}

function reports_view($view, $data)
{
    return reports_view_pdf($view, $data)
        ->stream();
}

function reports_view_landscape($view, $data)
{
    return reports_view_pdf($view, $data)
        ->setPaper('a4', 'landscape')
        ->stream();
}

function reports_view_portrait($view, $data)
{
    return reports_view_pdf($view, $data)
        ->setPaper('a4', 'portrait')
        ->stream();
}

function format_money($value)
{
    return number_format($value ?? 0, 2, '.', ',');
}

function format_money_with_sign($value)
{
    $sign = company()->currency->sign;

    return isset($value) ? ($value < 0 ? '- ' . $sign . format_money($value * -1) : $sign . format_money($value)) : null;
}

function FormCheckbox($name, $value = 1, $checked = null, array $options = [])
{
    $options['id']    = empty($options['id']) ? uniqid('checkbox_', true) : $options['id'];
    $options['class'] = ($options['class'] ?? '') . ' form-check-input' . (empty($options['label']) ? ' position-static' : null);
    $options['label'] = $options['label'] ?? null;

    $checkBoxHtml = Form::checkbox($name, $value, $checked, $options);
    $labelHtml    = '<label class="form-check-label" for="' . $options['id'] . '">' . $options['label'] . '</label>';

    return '<div class="form-check abc-checkbox">' . $checkBoxHtml . $labelHtml . '</div>';
}

/**
 * @return \Illuminate\Contracts\Auth\Authenticatable|User
 */
function user()
{
    return Auth::user();
}

function company(): ?Company
{
    return app(\App\Services\Company::class)->getCompany();
}

/**
 * @param User|null $user
 *
 * @return Collection
 */
function permissions(User $user = null)
{
    $collection = collect();
    $user       = $user ?? user();

    if (!$user) {
        return $collection;
    }

    $user->roles()->get()->each(function (Role $rol) use ($collection) {
        return $rol->permissions->each(function (Permission $permission) use ($collection) {
            return $collection->push($permission->name);
        });
    });

    return $collection;
}

function roles(User $user = null)
{
    $user = $user ?? user();

    if (!$user) {
        return collect();
    }

    return $user->roles->pluck('name');
}

function sub_words($text, $length)
{
    return (strlen($text) > $length) ? substr($text, 0, strpos($text, ' ', $length)) : '';
}

function str_to_slug($title, $separator = '-', $language = 'en')
{
    $title = str_replace('/', $separator, $title ?: '');

    return Str::slug($title, $separator, $language);
}

function isAnyEmpty($item, $fields)
{
    foreach ($fields as $field) {
        if (empty($item[$field])) {
            return true;
        }
    }

    return false;
}

/**
 * @param $path
 *
 * @return false|string
 */
function embed_public_file($path)
{
    return file_get_contents(public_path($path));
}

function embed_public_file_as_base64($path): string
{
    return base64_encode(file_get_contents(public_path($path)));
}

/**
 * @param string $name
 * @param mixed  $default
 *
 * @return Defaults
 */
function defaults($name = null, $default = null)
{
    if (!is_null($name)) {
        return app(Defaults::class)->get($name, $default);
    }

    return app(Defaults::class);
}

/** @return Lists */
function lists()
{
    return app(Lists::class);
}

function array_replace_keys(array $data, array $keyPairs): array
{
    $new_array = $data;
    $keys      = array_keys($data);
    $changed   = false;
    foreach ($keyPairs as $old => $new) {
        $index = array_search($old, $keys);
        if ($index !== false) {
            $keys[$index] = $new;
            $changed      = true;
        }
    }

    return !$changed ? $new_array : array_combine($keys, $new_array);
}

function unnested_levels($array, string $separator = '_')
{
    $relationElements = array_filter($array, function ($item) {
        return is_array($item);
    });

    foreach ($relationElements as $itemKey => $item) {
        unset($array[$itemKey]);

        foreach ($item as $key => $value) {
            $array[$itemKey . $separator . $key] = $value;
        }
    }

    if (!count($relationElements)) {
        return $array;
    }

    return unnested_levels($array, $separator);
}

function get_export_data(array $fields, $model, $exporter = null)
{
    $result = [];
    foreach ($fields as $field) {
        if (strpos($field, '.') === false) {
            /*
             * If field name start with $ char means that call a function with camelcase format and start with get
             * prefix else is a attribute
             */
            $result[] = strpos($field, '$') === 0 ? $exporter->{Str::camel(
                'get_' . trim(
                    $field,
                    '$',
                )
            )}($model) : $model->{$field};
        } else {
            $parts     = explode('.', $field);
            $table     = array_shift($parts);
            $sub_table = $model->{$table};
            foreach ($parts as $part) {
                $sub_table = strpos($part, '$') === 0 ?
                    $exporter->{Str::camel('get_' . trim($part, '$'))}($model) :
                    ($sub_table->{$part} ?? '');
            }
            $result[] = $sub_table;
        }
    }

    return $result;
}

function str_after_last($str, $char)
{
    return substr($str, strrpos($str, $char) + 1);
}

function get_root_domain($domain)
{
    $domainParts = explode('.', parse_url($domain, PHP_URL_HOST));

    return $domainParts[count($domainParts) - 2] . '.' . $domainParts[count($domainParts) - 1];
}

function get_subdomain($domain): string
{
    $domainParts = explode('.', parse_url($domain, PHP_URL_HOST));

    return $domainParts[0];
}

function website()
{
    return app(Environment::class)->tenant();
}

function remove_extra_spaces($str)
{
    return trim(preg_replace('/\s+/', ' ', $str));
}

if (!function_exists('mix_cdn')) {
    /**
     * Get the path to a versioned Mix file.
     *
     * @param string $path
     * @param string $manifestDirectory
     *
     * @return HtmlString|string
     *
     * @throws Exception
     */
    function mix_cdn($path, $manifestDirectory = '')
    {
        $cdnUrl  = config('app.cdn_url');
        $mixPath = mix($path, $manifestDirectory);

        if (empty($cdnUrl) || app()->environment(['testing'])) {
            return asset($mixPath);
        }

        $fullUrl = $cdnUrl . '/' . config('app.stage') . $mixPath;

        if (app()->environment('dev')) {
            return $fullUrl . '?id=' . get_commit_hash();
        }

        return $fullUrl;
    }
}

function asset_version($path): string
{
    $cdnUrl     = config('app.cdn_url');
    $hash       = get_commit_hash();
    $hashSuffix = empty($hash) ? '' : '?' . $hash;

    if (empty($cdnUrl)) {
        return asset($path) . $hashSuffix;
    }

    return $cdnUrl . '/' . config('app.stage') . '/' . $path . $hashSuffix;
}

if (!function_exists('get_commit_hash')) {
    /**
     * Checks to see if we have a .commit_hash file or .git repo and return the hash if we do.
     *
     * @return null|string
     */
    function get_commit_hash(): ?string
    {
        return config('release.github_commit_sha');
    }
}

if (!function_exists('get_commit_data')) {
    /**
     * @return null|object
     */
    function get_commit_data()
    {
        if (!get_commit_hash()) {
            return null;
        }

        return Cache::remember('commit:data:' . get_commit_hash(), 3600, function () {
            $contextOptions = [
                'ssl' => [
                    'verify_peer'      => false,
                    'verify_peer_name' => false,
                ],
            ];

            return json_decode(
                file_get_contents(
                    'https://manage.smart145.io/github/commit/' . get_commit_hash() . '?token=' . config('app.management_retrieve_key'),
                    false,
                    stream_context_create($contextOptions),
                ),
            );
        });
    }
}

if (!function_exists('array_remove_keys')) {
    /**
     * Checks an array and remove the given keys.
     *
     * @param array        $array
     * @param array|string $keys
     *
     * @return array
     */
    function array_forget_keys($array, $keys)
    {
        $keys = Arr::wrap($keys);

        foreach ($keys as $key) {
            $array = array_map(function ($item) use ($key) {
                unset($item[$key]);

                return $item;
            }, $array);
        }

        return $array;
    }
}

if (!function_exists('change_format_day')) {
    /**
     * Convert format m/d/Y to Y-m-d
     *
     * @param $date string
     *
     * @return false|string
     */
    function change_format_day(string $date): string
    {
        return date('Y-m-d', strtotime($date));
    }
}

function report_custom_path($value)
{
    return 'reports.customs.' . company()->slug_override . '.' . $value;
}

function report_css_custom_path()
{
    return 'css/pdf/' . company()->slug_override . '/style.css';
}

/**
 * @param string $requestClass
 * @param string $companySlug
 *
 * @return array
 */
function company_rules(string $requestClass): array
{
    $companyRules = app('loadCompanyRules');

    $ruleKey = Str::snake(
        preg_replace('#Request$#', '', Str::afterLast(basename($requestClass), '\\')),
    );

    $rules    = Arr::get($companyRules, company()->slug_override . '.' . $ruleKey . '.rules', []);
    $messages = Arr::get($companyRules, company()->slug_override . '.' . $ruleKey . '.messages', []);

    return compact('rules', 'messages');
}

if (!function_exists('max')) {
    /**
     * Return the max value between two numbers
     *
     * @param int $firstNumber
     * @param int $secondNumber
     *
     * @return int
     */
    function max(int $firstNumber, int $secondNumber): int
    {
        return $firstNumber > $secondNumber ? $firstNumber : $secondNumber;
    }
}

if (!function_exists('barcode')) {
    function barcode($code): \App\Services\Barcode\Barcode
    {
        return new \App\Services\Barcode\Barcode($code);
    }
}

if (!function_exists('qr_code')) {
    function qr_code($code): \App\Services\Barcode\QRCode
    {
        return new \App\Services\Barcode\QRCode($code);
    }
}

function db_query_log()
{
    return \DB::getQueryLog();
}

function eloquentSqlWithBindings($queryBuilder)
{
    $sql = str_replace('?', '%s', $queryBuilder->toSql());

    $handledBindings = array_map(function ($binding) {
        if (is_numeric($binding)) {
            return $binding;
        }

        if (is_bool($binding)) {
            return ($binding) ? 'true' : 'false';
        }

        return "'{$binding}'";
    }, $queryBuilder->getBindings());

    return vsprintf($sql, $handledBindings);
}

function format_ordinal_number($number)
{
    $ends = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];

    if (($number % 100) >= 11 && ($number % 100) <= 13) {
        return $number . 'th';
    }

    return $number . $ends[$number % 10];
}

function real_empty($value)
{
    if (empty($value)) {
        return true;
    }

    if (
        ($value instanceof Collection || $value instanceof \Illuminate\Database\Eloquent\Collection)
        && $value->isEmpty()
    ) {
        return true;
    }

    return false;
}

function general_setting($name = null, $default = null, $valueKey = null)
{
    $setting = GeneralSetting::where('name', $name)->get()->first();
    if (real_empty($setting)) {
        return $default;
    }

    if (!$setting->active) {
        return $default;
    }

    $value = json_decode($setting->value, true);

    return is_null($valueKey) ? $value : $value[$valueKey];
}


function user_settings($name, $default)
{
    return user()->settings[$name] ?? $default;
}

function record_fullstory()
{
    // Do not use fullstory outside production
    if (!app()->environment('production')) {
        return false;
    }

    return website()->allow_fullstory && !session('engineerLogged') && \Auth::check() && user()->allow_fullstory;
}

function repeat_factory_create($nTimes, $factory)
{
    for ($i = 1; $i <= $nTimes; $i++) {
        if ($factory instanceof Closure) {
            $factory();
            continue;
        }

        $factory->create();
    }
}

function activate_website($slug)
{
    $uuid = 's145_' . $slug . '_' . config('app.stage');

    $website  = Website::with('hostnames.website')->where('uuid', $uuid)->firstOrFail();
    $hostname = $website->hostnames->first();

    app(Environment::class)->tenant($website);
    app(Tenancy::class)->setHostname($hostname);
}

function bytesToHuman($bytes)
{
    $units = ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB'];

    for ($i = 0; $bytes > 1024; $i++) {
        $bytes /= 1024;
    }

    return round($bytes, 2) . ' ' . $units[$i];
}

function convert_png_to_jpg($filePath)
{
    $image = imagecreatefrompng($filePath);
    $bg    = imagecreatetruecolor(imagesx($image), imagesy($image));
    imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
    imagealphablending($bg, true);
    imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
    imagedestroy($image);

    $generatedImagePath = stream_get_meta_data(tmpfile())['uri'];

    imagejpeg($bg, $generatedImagePath, 100);
    imagedestroy($bg);

    return $generatedImagePath;
}

/**
 * CROP whitespaces from an image
 *
 * @param $imagePath
 *
 * @return mixed
 */
function crop_image_whitespace($imagePath, $extension = null)
{
    try {
        $parsedPath = parse_url($imagePath)['path'];

        if (Str::endsWith($parsedPath, '.png')) {
            $img     = imagecreatefrompng($imagePath);
            $cropped = imagecropauto($img, IMG_CROP_THRESHOLD, 10, 16777215); // white color
            imagedestroy($img); // Clean up as $img is no longer needed

            if ($cropped !== false) {
                $generatedImagePath = stream_get_meta_data(tmpfile())['uri'];
                imagepng($cropped, $generatedImagePath, 0); // Return the newly cropped image

                return $generatedImagePath;
            }
        }

        if (
            Str::endsWith($parsedPath, '.jpg') ||
            Str::endsWith($parsedPath, '.jpeg') ||
            in_array($extension, ['jpg', 'jpeg'])
        ) {
            $img     = imagecreatefromjpeg($imagePath);
            $cropped = imagecropauto($img, IMG_CROP_THRESHOLD, 10, 16777215);
            imagedestroy($img); // Clean up as $img is no longer needed

            if ($cropped !== false) {
                $generatedImagePath = stream_get_meta_data(tmpfile())['uri'];
                imagejpeg($cropped, $generatedImagePath, 100); // Return the newly cropped image

                return $generatedImagePath;
            }
        }
    } catch (Exception $e) {
        \Log::error($e);
    }

    return $imagePath;
}

function create_html_link($params, $withRoute = true)
{
    $route = $withRoute ? route($params['route'], $params['data']) : $params['route'];
    $extra = !empty($params['extra_params']) ? $params['extra_params'] : null;

    return '<a href="' . $route . '" target="' . $params['target'] . '" ' . $extra . ' >' . $params['value'] . '</a>';
}

function update_cache_prefix($company)
{
    $uuid = 's145_' . $company->slug . '_' . config('app.stage');

    config()->set('cache.prefix', $uuid);
    Cache::setPrefix(config('cache.prefix'));
}

function featureFlag($value)
{
    return config('feature-flag.' . $value);
}

function google_maps_address_link($address)
{
    return 'https://www.google.com/maps/search/' . $address;
}

function is_valid_serial_number($value)
{
    $serialNumberCleaned = trim(strtolower($value));

    return !in_array($serialNumberCleaned, InventoryDetail::SN_NOT_ALLOWED);
}

function floated($val)
{
    return floatval(preg_replace('/[\$,]/', '', $val));
}

function createHtmlBadge($value, $class = '')
{
    return !empty($class)
        ? '<span class="badge badge-' . $class . '">' . $value . '</span>'
        : '<span>' . $value . '</span>';
}

function server_timing()
{
    return app(ServerTiming::class);
}

function run_with_fk_check_disabled($closure)
{
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    $response = $closure();
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    return $response;
}

function wrap_url($url)
{
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return '';
    }

    return Str::startsWith($url, 'http') ? $url : 'http://' . $url;
}

if (!function_exists('human_readable_size')) {
    function human_readable_size($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}

/**
 * Return an image as base64 to be used inline
 *
 * @param $publicPath
 *
 * @return string
 */
function embedded_public_image($publicPath): string
{
    $path = public_path($publicPath);
    $data = file_get_contents($path);

    return 'data:' . mime_content_type($path) . ';base64,' . base64_encode($data);
}

/**
 * @param $module
 * @param $reportIdentifier
 *
 * @return bool
 * @throws Exception
 */
function existReportJasper($module, $reportIdentifier): bool
{
    $path = resource_path('views/jasper_reports/');

    return file_exists($path . '/customs/' . company()->slug_override . '/' . $module . '/' . $reportIdentifier . '.jrxml')
        || file_exists($path . $module . '/' . $reportIdentifier . '.jrxml');
}

function embed_company_logo_by_slug($slug, $format = 'jpg'): string
{
    return embedded_public_image('logos/' . $slug . '.' . $format);
}

function have_special_char($string): bool
{
    $specialChars = ['Ω'];

    foreach ($specialChars as $specialChar) {
        for ($i = 0; $i <= strlen($string); $i++) {
            $haveSpecialChar = strpos(mb_substr($string, $i, 1), $specialChar) !== false;
            if ($haveSpecialChar) {
                return true;
            }
        }
    }

    return false;
}

function temporal_file_path()
{
    $tempFileHandle = tmpfile();

    return stream_get_meta_data($tempFileHandle)['uri'];
}

function file_put_contents_to_temporary_file($content)
{
    $temporaryFilePath = temporal_file_path();

    file_put_contents($temporaryFilePath, $content);

    return $temporaryFilePath;
}

function get_work_order_data_definition()
{
    return settings('work-order.data-definition', []);
}

function is_work_order_data_enabled(): bool
{
    $workOrderDefinitions = get_work_order_data_definition();

    if (!$workOrderDefinitions) {
        return false;
    }

    foreach ($workOrderDefinitions as $workOrderDefinition) {
        if (empty($workOrderDefinition['label']) || empty($workOrderDefinition['fields'])) {
            Log::error('Invalid Work Order Definition');

            return false;
        }
    }

    return true;
}

function flatten_array($values = []): array
{
    foreach ($values as $key => $value) {
        if (is_array($value)) {
            $originalValue = $value;

            foreach ($originalValue as $subKey => $subValue) {
                $values[$key . '_' . $subKey] = $subValue;
            }

            unset($values[$key]);
        }
    }

    return $values;
}

function get_tenant_reader_name(): string
{
    return app()->environment('testing') ? 'tenant' : 'tenant_reader';
}

function remove_keys_from_array($array, $keys): array
{
    $keys = Arr::wrap($keys);

    return collect($array)->map(function ($item) use ($keys) {
        foreach ($keys as $key) {
            unset($item[$key]);
        }

        return $item;
    })->toArray();
}

/**
 * @param       $array
 * @param array $parameters
 * @return array|false
 */
function safe_array_only($array, $parameters = []): array
{
    $arrayTest = array_merge(array_combine($parameters, array_fill(0, count($parameters), null)), $array);

    return Arr::only($arrayTest, $parameters);
}

function execute_callback_n_times($callback, $times)
{
    for ($i = 0; $i < $times; $i++) {
        $callback();
    }
}

/**
 * @param $string
 * @return false|string|null
 */
function escapeUnicodeToHtml($string)
{
    $unicodeExpNumbers = [
        8304 => 0,
        8305 => 1,
        8306 => 2,
        8307 => 3,
        8308 => 4,
        8309 => 5,
        8310 => 6,
        8311 => 7,
        8312 => 8,
        8313 => 9,
    ];

    $string = htmlentities($string);

    return mb_ereg_replace_callback('[\u2070-\u2079]{1,}', function ($match) use ($unicodeExpNumbers) {
        $stringParsed = '';

        for ($i = 0; $i < mb_strlen($match[0], 'UTF-8'); $i++) {
            $stringParsed .= $unicodeExpNumbers[unpack('V', iconv('UTF-8', 'UCS-4LE', mb_substr($match[0], $i, 1)))[1]];
        }

        return '<sup>' . $stringParsed . '</sup>';
    }, $string);
}

/**
 * @throws \Psr\SimpleCache\InvalidArgumentException
 */
function announcements()
{
    if (request()->has('clear-cache')) {
        Cache::tags(['announcements'])->set('announcements', [], -1);
    }

    $announcements = Cache::tags(['announcements'])->remember('announcements', 300, function () {
        return app(AnnouncementRepository::class)->getAll();
    });

    $announcements = $announcements->filter(function ($announcement) {
        if ($announcement->role) {
            return auth()->user()->hasRole(explode(',', $announcement->role));
        }

        if ($announcement->permission) {
            return auth()->user()->can(explode(',', $announcement->permission));
        }

        return true;
    });

    return $announcements->groupBy('type')->toArray();
}

function can_see_chat(): bool
{
    if (Auth::check() && (user()->is_chat_blocked || user()->isCustomer())) {
        return false;
    }

    return website()->allow_freshchat && !website()->allow_hubspot_chat;
}

function can_see_support(): bool
{
    if (Auth::check() && (user()->is_support_blocked || user()->isCustomer())) {
        return false;
    }

    $allowFreshDesk = website()->allow_freshdesk && !website()->allow_hubspot_chat;

    return $allowFreshDesk && (!website()->allow_freshchat || user()->is_chat_blocked);
}

function can_see_intercom(): bool
{
    if (Auth::check() && (user()->is_chat_blocked || user()->isCustomer())) {
        return false;
    }

    return website()->allow_intercom;
}

function human_diff_by_second($seconds)
{
    $countPointsToShow = 0;
    $message           = '';
    $secondsSubtracted = $seconds;

    $points = [
        'day'    => 86400,
        'hour'   => 3600,
        'minute' => 60,
        'second' => 1,
    ];

    foreach ($points as $point => $value) {
        if (($point === 'second' && $secondsSubtracted != $seconds) || $countPointsToShow === 2) {
            break;
        }

        if ((int) ($secondsSubtracted / $value) < 1) {
            continue;
        }

        $usePlural = ($secondsSubtracted / $value) >= 2 ? 's' : '';

        if (($secondsSubtracted / $value) === 1) {
            $time    = (int) ($secondsSubtracted / $value);
            $message .= $time . ' ' . $point . $usePlural;
            break;
        }

        if (($secondsSubtracted / $value) > 1) {
            $time              = (int) ($secondsSubtracted / $value);
            $secondsSubtracted = $secondsSubtracted - ($time * $value);
            $message .= $time . ' ' . $point . $usePlural . ' ';
            $countPointsToShow++;
        }
    }

    return trim($message);
}

/**
 * @param string|null $value
 * @param string      $replacement
 * @return string|null
 */
function replaceNonBrakingSpaces(?string $value, string $replacement = ''): ?string
{
    return preg_replace('/\xc2\xa0/', $replacement, $value);
}

/**
 * Returns the text cut by lines by a specified amount of characters
 *
 * @param string $text                Text to be cut into lines
 * @param int    $numberOfCharacter   Number of characters by which the lines will be cut
 * @param int    $defaultNumberOfLine Number of lines to paint white if the text is not long enough
 * @param string $label               Label that puts at the beginning of the text
 * @param int    $lineLimit           Limit of lines to show
 * @return array
 */
function getTextToShowByLines(
    ?string $text,
    int $numberOfCharacter,
    int $defaultNumberOfLine = 4,
    string $label = '',
    int $lineLimit = null,
): array {
    $lines                     = [];
    $arrayStringsWithJumpLines = explode("\n", $label . $text);

    foreach ($arrayStringsWithJumpLines as $key => $string) {
        if (empty($string) && $key === 0) {
            continue;
        }

        $line         = '';
        $textsByLines = explode(' ', $string);

        foreach ($textsByLines as $word) {
            if ((strlen($line) + strlen($word)) < $numberOfCharacter) {
                $line = $line . ' ' . $word;
                continue;
            }

            $lines[] = ['line' => trim($line)];
            $line    = $word;
        }

        $lines[] = ['line' => trim($line)];
    }

    while (count($lines) < $defaultNumberOfLine) {
        $lines[] = ['line' => ''];
    }

    return $lineLimit ? array_slice($lines, 0, $lineLimit) : $lines;
}

/**
 * @param string $date
 * @return Carbon
 */
function parse_utc_date_to_company_timezone(string $date): Carbon
{
    return Carbon::parse($date)->timezone(company()->timezone);
}

function get_media_content($media)
{
    return file_get_contents(get_temporary_media_url($media));
}

/**
 * @throws MediaManagerException
 * @throws Exception
 */
function verify_media_upload($mediaEntity)
{
    if ($mediaEntity->disk !== 's3client') {
        return true;
    }

    return retry(5, function () use ($mediaEntity) {
        $fileUrl = $mediaEntity->getTemporaryUrl(Carbon::now()->addMinute());
        if (strpos(get_headers($fileUrl)[0], '200') !== false) {
            return true;
        }
        throw new MediaManagerException(
            'An error occurred uploading the file <strong>' . $mediaEntity->file_name . '</strong>'
        );
    }, 500);
}

function get_temporary_media_url($media, $maxAge = 300): string
{
    return $media->disk === 's3client'
        ? $media->getTemporaryUrl(Carbon::now()->addSeconds($maxAge))
        : $media->getFullUrl();
}

function sanitizeLineBreaks($string)
{
    return str_replace(["\r\n", "\r", "\n"], ' ', $string);
}

function retrieveErrorCodeFromEmailException($exception)
{
    $pattern = '/\(code (\d+)\)/';
    preg_match($pattern, $exception->getMessage(), $matches);

    return (int) ($matches[1] ?? $exception->getCode());
}

/**
 * @param      $date
 * @param null $fromFormat
 * @return string
 * @throws \Psr\Container\ContainerExceptionInterface
 * @throws \Psr\Container\NotFoundExceptionInterface
 */
function localized_format_date($date, $fromFormat = null): string
{
    if (empty($date)) {
        return '';
    }

    $format = settings('reporting.friendly_date_format') ?: 'm/d/Y';

    $parsedDate = $date instanceof \Carbon\Carbon
        ? $date
        : (
            $fromFormat && strtotime($date) !== false
            ? Carbon::createFromFormat($fromFormat, $date)
            : (strtotime($date) !== false ? Carbon::parse($date) : null)
        );

    return $parsedDate ? $parsedDate->format($format) : '';
}

function parseTemplate($data, $template)
{
    $pattern = '/{{(.*?)}}/';

    return preg_replace_callback($pattern, function ($matches) use ($data) {
        $key  = trim($matches[1]);
        $keys = explode('.', $key);

        $value = $data;
        foreach ($keys as $k) {
            if (isset($value[$k])) {
                $value = $value[$k];
            } else {
                $value = '';
                break;
            }
        }

        return $value;
    }, $template);
}

function get_file_content_dynamically($file)
{
    if ($file instanceof Response || $file instanceof File) {
        return $file->getContent();
    }

    if ($file instanceof BinaryFileResponse) {
        return $file->getFile()->getContent();
    }

    return $file;
}
