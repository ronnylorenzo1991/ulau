<?php

namespace App\Http\Controllers;

use App\Models\Nav;
use Spatie\Permission\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Models\WorkOrderReviewStatus;
use App\Models\WorkOrderStatus;
use App\Repositories\AnomalyClassification\AnomalyClassificationRepository;
use App\Repositories\AnomalyLocation\AnomalyLocationRepository;
use App\Repositories\AnomalyStatus\AnomalyStatusRepository;
use App\Repositories\AnomalyType\AnomalyTypeRepository;
use App\Repositories\Area\AreaRepository;
use App\Repositories\Cage\CageRepository;
use App\Repositories\Center\CenterRepository;
use App\Repositories\Company\CompanyRepository;
use App\Repositories\Customer\CustomerRepository;
use App\Repositories\Deficiency\DeficiencyRepository;
use App\Repositories\Module\ModuleRepository;
use App\Repositories\Orientation\OrientationRepository;
use App\Repositories\Part\PartRepository;
use App\Repositories\RcaReason\RcaReasonRepository;
use App\Repositories\Rov\RovRepository;
use App\Repositories\Service\ServiceRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DefaultController extends Controller
{
    private AreaRepository $areaRepository;
    private CompanyRepository $companyRepository;
    private CenterRepository $centerRepository;
    private ModuleRepository $modulesRepository;
    private CageRepository $cageRepository;
    private PartRepository $partRepository;
    private ServiceRepository $serviceRepository;
    private OrientationRepository $orientationRepository;
    private CustomerRepository $customerRepository;
    private RcaReasonRepository $rcaReasonRepository;
    private RovRepository $rovsRepository;
    private AnomalyStatusRepository $anomalyStatusRepository;
    private AnomalyClassificationRepository $anomalyClassificationRepository;
    private AnomalyTypeRepository $anomalyTypeRepository;
    private AnomalyLocationRepository $anomalyLocationRepository;
    private DeficiencyRepository $deficiencyRepository;

    public function __construct(
        AreaRepository $areaRepository,
        CompanyRepository $companyRepository,
        CenterRepository $centerRepository,
        ModuleRepository $modulesRepository,
        CageRepository $cageRepository,
        PartRepository $partRepository,
        ServiceRepository $serviceRepository,
        OrientationRepository $orientationRepository,
        CustomerRepository $customerRepository,
        RcaReasonRepository $rcaReasonRepository,
        RovRepository $rovsRepository,
        AnomalyStatusRepository $anomalyStatusRepository,
        AnomalyClassificationRepository $anomalyClassificationRepository,
        AnomalyTypeRepository $anomalyTypeRepository,
        AnomalyLocationRepository $anomalyLocationRepository,
        DeficiencyRepository $deficiencyRepository,
    ) {
        $this->areaRepository                  = $areaRepository;
        $this->companyRepository               = $companyRepository;
        $this->centerRepository                = $centerRepository;
        $this->modulesRepository               = $modulesRepository;
        $this->cageRepository                  = $cageRepository;
        $this->partRepository                  = $partRepository;
        $this->serviceRepository               = $serviceRepository;
        $this->orientationRepository           = $orientationRepository;
        $this->customerRepository              = $customerRepository;
        $this->rcaReasonRepository             = $rcaReasonRepository;
        $this->rovsRepository                  = $rovsRepository;
        $this->anomalyStatusRepository         = $anomalyStatusRepository;
        $this->anomalyClassificationRepository = $anomalyClassificationRepository;
        $this->anomalyTypeRepository           = $anomalyTypeRepository;
        $this->anomalyLocationRepository       = $anomalyLocationRepository;
        $this->deficiencyRepository            = $deficiencyRepository;
    }

    public function getLists(Request $request)
    {
        $request->validate([
            'lists' => 'required',
        ]);


        $results = [];

        foreach (json_decode($request->get('lists')) as $item) {
            switch ($item) {
                case 'roles':
                    $results['roles'] = Role::all()->pluck('name', 'id');
                    break;
                case 'navs':
                    $results['navs'] = Nav::all()->whereNull('nav_id')->pluck('name', 'id');
                    break;
                case 'companies':
                    $results['companies'] = $this->companyRepository->getList()->pluck('name', 'id');
                    break;
                case 'areas':
                    $results['areas'] = $this->areaRepository->getList()->pluck('name', 'id');
                    break;
                case 'centers':
                    $results['centers'] = $this->centerRepository->getList()->pluck('name', 'id');
                    break;
                case 'modules':
                    $results['modules'] = $this->modulesRepository->getList()->pluck('name', 'id');
                    break;
                case 'customers':
                    $results['customers'] = $this->customerRepository->getList()->pluck('name', 'id');
                    break;
                case 'cages':
                    $results['cages'] = $this->cageRepository->getList()->pluck('number', 'id');
                    break;
                case 'parts':
                    $results['parts'] = $this->partRepository->getList()->pluck('name', 'id');
                    break;
                case 'services':
                    $results['services'] = $this->serviceRepository->getList()->pluck('name', 'id');
                    break;
                case 'orientations':
                    $results['orientations'] = $this->orientationRepository->getList()->pluck('name', 'id');
                    break;
                case 'rca_reasons':
                    $results['rca_reasons'] = $this->rcaReasonRepository->getList()->pluck('name', 'id');
                    break;
                case 'rovs':
                    $results['rovs'] = $this->rovsRepository->getList()->pluck('name', 'id');
                    break;
                case 'anomalyTypes':
                    $results['anomalyTypes'] = $this->anomalyTypeRepository->getList()->pluck('name', 'id');
                    break;
                case 'anomalyStatuses':
                    $results['anomalyStatuses'] = $this->anomalyStatusRepository->getList()->pluck('name', 'id');
                    break;
                case 'anomalyClassifications':
                    $results['anomalyClassifications'] = $this->anomalyClassificationRepository->getList()->pluck('name', 'id');
                    break;
                case 'anomalyLocations':
                    $results['anomalyLocations'] = $this->anomalyLocationRepository->getList()->pluck('name', 'id');
                    break;
                case 'pilots':
                    $results['pilots'] = User::whereHas("roles", function ($q) {
                        $q->where("name", "operario");
                        if (!Auth::user()->hasAnyRole(['supervisor', 'gerencia', 'administrador', 'desarrollo'])) {
                            $q->where('center_id', get_user_center_id());
                        }
                    })->get()->pluck('name', 'id');
                    break;
                case 'centerBosses':
                    $results['centerBosses'] = User::whereHas("roles", function ($q) {
                        $q->where("name", "jefe-centro");
                        if (!Auth::user()->hasAnyRole(['supervisor', 'gerencia', 'administrador', 'desarrollo'])) {
                            $q->where('center_id', get_user_center_id());
                        }
                    })->get()->pluck('name', 'id');
                    break;
                case 'work_order_statuses':
                    $results['work_order_statuses'] = WorkOrderStatus::all()->pluck('name', 'id');
                    break;
                case 'work_order_review_statuses':
                    $results['work_order_review_statuses'] = WorkOrderReviewStatus::all()->pluck('name', 'id');
                    break;
                case 'permissions':
                    $results['permissions'] = Permission::all();
                    break;
                case 'permissionList':
                    $results['permissionList'] = Permission::all()->pluck('name', 'id');
                    break;
                case 'deficiencies':
                    $results['deficiencies'] = $this->deficiencyRepository->getList()->pluck('name', 'id');
                    break;
            }
        }

        return json_encode(['lists' => $results]);
    }
}
