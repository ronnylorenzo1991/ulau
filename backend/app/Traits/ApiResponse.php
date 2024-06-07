<?php
namespace App\Traits;

use Illuminate\Http\Request;

trait ApiResponse
{
    protected function filter($model, Request $request, $fields)
    {
        //dd($request->all());
        foreach ($request->all() as $key => $value) {
            if ($key != 'limit' && $key != 'page') {
                $f = $fields->where('column', $key)->first();
                //dd($f['type']);
                if ($f['type'] == 'string') {
                    $model = $model->where($key, 'like', "%{$value}%");
                }

                if ($f['type'] == 'integer') {
                    $model = $model->where($key, $value);
                }
            }
            //dd($f->type);
        }
        $limit = 10;
        if ($request->has('limit')) {
            $limit = $request->limit;
        }
        return $model->paginate($limit)->withQueryString();

    }

    protected function filterAll($model, Request $request, $types, $table = null)
    {
        foreach ($request->all() as $key => $value) {
            if ($key != 'page') {
                $t = $types[$key];
                if ($key === 'suitable') {
                    if ($request->get('suitable') == '1') {
                        $model = $model->where('records.tvn','<', '50')->where('records.ph_reception','<', '4')
                            ->where('records.type_record_id','=',(int) $request->get('type_record_id'));
                    }
                    if ($request->get('suitable') === '0') {
                        $model = $model->where('records.type_record_id','=',(int) $request->get('type_record_id'))
                            ->where(function ($query) {
                                $query->where('records.tvn','>=', '50')->orWhere('records.ph_reception','>=', '4');
                            });
                    }
                }
                if ($table != null)
                    $key = "{$table}.{$key}";
                if ($t == 'foreign_key' || $t == 'integer') {
                    $model = $model->where($key, $value);
                }
                if ($t == 'string') {
                    $model = $model->where($key, 'like', "%{$value}%");
                }
                if ($t == 'date' || $t == 'datetime') {
                    $dates = explode(",", $value);
                    //dd($dates[0],$dates[1]);
                    $model = $model->whereBetween($key, [$dates[0], $dates[1]]);
                }
            }



            // dd($key,$value,$t,$model->toArray());

            // if($key != 'limit' && $key != 'page'){
            //     $f = $fields->where('column',$key)->first();
            //     //dd($f['type']);
            //     if($f['type'] == 'string'){
            //         $model = $model->where($key,'like',"%{$value}%");
            //     }

            //     if($f['type'] == 'integer'){
            //         $model = $model->where($key,$value);
            //     }
            // }
            //dd($f->type);
        }
        //$model = $model->get();

        //dd($model);
        return $model;
        // $limit = 10;
        // if($request->has('limit')){
        //     $limit = $request->limit;
        // }
        // return $model->paginate($limit)->withQueryString();

    }

    protected function normalize_errors($errors)
    {
        $arr = [];
        foreach ($errors->messages() as $key => $messages) {
            foreach ($messages as $key => $m) {
                $arr[] = $m;
            }
        }
        return $arr;
    }
}
