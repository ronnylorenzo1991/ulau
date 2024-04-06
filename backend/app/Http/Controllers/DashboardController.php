<?php

namespace App\Http\Controllers;

use App\Models\Module;
use App\Repositories\Anomaly\AnomalyRepository;
use App\Repositories\AnomalyStatus\AnomalyStatusRepository;
use App\Repositories\AnomalyType\AnomalyTypeRepository;
use App\Repositories\Area\AreaRepository;
use App\Repositories\Cage\CageRepository;
use App\Repositories\Center\CenterRepository;
use App\Repositories\Company\CompanyRepository;
use App\Repositories\Customer\CustomerRepository;
use App\Repositories\Module\ModuleRepository;
use App\Repositories\Orientation\OrientationRepository;
use App\Repositories\Part\PartRepository;
use App\Repositories\RcaReason\RcaReasonRepository;
use App\Repositories\Rov\RovRepository;
use App\Repositories\Service\ServiceRepository;
use App\Repositories\WorkOrder\WorkOrderRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private AnomalyRepository $anomalyRepository;
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
    private AnomalyTypeRepository $anomalyTypeRepository;
    private WorkOrderRepository $workOrderRepository;

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
        AnomalyTypeRepository $anomalyTypeRepository,
        WorkOrderRepository $workOrderRepository,
        AnomalyRepository $anomalyRepository,
    ) {
        $this->anomalyRepository       = $anomalyRepository;
        $this->areaRepository          = $areaRepository;
        $this->companyRepository       = $companyRepository;
        $this->centerRepository        = $centerRepository;
        $this->modulesRepository       = $modulesRepository;
        $this->cageRepository          = $cageRepository;
        $this->partRepository          = $partRepository;
        $this->serviceRepository       = $serviceRepository;
        $this->orientationRepository   = $orientationRepository;
        $this->customerRepository      = $customerRepository;
        $this->rcaReasonRepository     = $rcaReasonRepository;
        $this->rovsRepository          = $rovsRepository;
        $this->anomalyStatusRepository = $anomalyStatusRepository;
        $this->anomalyTypeRepository   = $anomalyTypeRepository;
        $this->workOrderRepository     = $workOrderRepository;
    }

    public function anomaliesByParts(Request $request)
    {
        try {
            $filters = $request->only([
                'center',
            ]);

            $anomalies  = $this->anomalyRepository->getAnomaliesByParts($filters);
            $labels     = [];
            $quantities = [];

            foreach ($anomalies as $anomaly) {
                $labels[]     = $anomaly->name;
                $quantities[] = $anomaly->quantity;
            }

            return response()->json([
                'success'    => true,
                'labels'     => $labels,
                'quantities' => $quantities,
                'message'    => 'Datos cargados con éxito',
            ], 200);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());

            return response()->json([
                'message' => 'Hubo un problema al cargar los datos',
            ], 422);
        }
    }

    public function anomaliesByCages(Request $request)
    {
        try {
            $filters = $request->only([
                'center',
            ]);

            $anomalies  = $this->anomalyRepository->getAnomaliesByCages($filters);
            $labels     = [];
            $quantities = [];

            foreach ($anomalies as $anomaly) {
                $labels[]     = $anomaly->number;
                $quantities[] = $anomaly->quantity;
            }

            return response()->json([
                'success'    => true,
                'labels'     => $labels,
                'quantities' => $quantities,
                'message'    => 'Datos cargados con éxito',
            ], 200);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());

            return response()->json([
                'message' => 'Hubo un problema al cargar los datos',
            ], 422);
        }
    }

    public function anomaliesByType(Request $request)
    {
        try {
            $filters = $request->only([
                'center',
            ]);

            $anomalies  = $this->anomalyRepository->getAnomaliesByField('anomaly_classification_id', $filters);
            $labels     = [];
            $quantities = [];

            foreach ($anomalies as $anomaly) {
                $labels[]     = $anomaly->name;
                $quantities[] = $anomaly->quantity;
            }

            return response()->json([
                'success'    => true,
                'labels'     => $labels,
                'quantities' => $quantities,
                'message'    => 'Datos cargados con éxito',
            ], 200);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());

            return response()->json([
                'message' => 'Hubo un problema al cargar los datos',
            ], 422);
        }
    }

    public function anomaliesByCategory(Request $request)
    {
        try {
            $filters    = $request->only([
                'center',
            ]);
            $anomalies  = $this->anomalyRepository->getAnomaliesByCategory($filters);
            $labels     = [];
            $quantities = [];

            foreach ($anomalies as $anomaly) {
                $labels[]     = $anomaly->name;
                $quantities[] = $anomaly->quantity;
            }

            return response()->json([
                'success'    => true,
                'labels'     => $labels,
                'quantities' => $quantities,
                'message'    => 'Datos cargados con éxito',
            ], 200);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());

            return response()->json([
                'message' => 'Hubo un problema al cargar los datos',
            ], 422);
        }
    }

    public function anomaliesByCategoryModule(Request $request)
    {
        try {
            $filters  = $request->only([
                'center',
            ]);
            $datasets = [];
            $centerId = !empty ($filters['center']) ? $filters['center'] : get_user_center_id();
            Module::where('center_id', $centerId)->get()->each(function ($module) use (&$datasets, $request, $filters) {
                $anomalies  = $this->anomalyRepository->getAnomaliesCategoriesByModule(
                    $module->id,
                    $request->get('withoutAdherence', false),
                    $request->get('onlyAdherence', false),
                    $filters,
                );
                $labels     = [];
                $quantities = [];

                foreach ($anomalies as $anomaly) {
                    $labels[]     = $anomaly->name;
                    $quantities[] = $anomaly->quantity;
                }

                $datasets[] = [
                    'moduleId'   => $module->id,
                    'labels'     => $labels,
                    'quantities' => $quantities,
                ];
            });


            return response()->json([
                'success'  => true,
                'datasets' => $datasets,
                'message'  => 'Datos cargados con éxito',
            ], 200);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());

            return response()->json([
                'message' => 'Hubo un problema al cargar los datos',
            ], 422);
        }
    }

    public function anomaliesByStatus(Request $request)
    {
        try {
            $filters   = $request->only([
                'center',
            ]);
            $anomalies = $this->anomalyRepository->getAnomaliesByField('anomaly_status_id', $filters);

            return response()->json([
                'success' => true,
                'data'    => $anomalies,
                'message' => 'Datos cargados con éxito',
            ], 200);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());

            return response()->json([
                'message' => 'Hubo un problema al cargar los datos',
            ], 422);
        }
    }

    /**
     * Get classifications by cages.
     */
    public function classificationsByCage(Request $request): JsonResponse
    {
        try {
            $filters   = $request->only([
                'center',
            ]);
            $anomalies = $this->anomalyRepository->getClassificationsByCages(
                $request->get('withoutAdherence', false),
                $request->get('onlyAdherence', false),
                $filters,
            );

            return response()->json([
                'success' => true,
                'data'    => $anomalies,
                'message' => 'Elemento Cargado con éxito',
            ]);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());

            return response()->json([
                'message' => 'Hubo un problema al guardar los datos',
            ], 422);
        }
    }

    public function anomaliesLastThirtyDays(Request $request): JsonResponse
    {
        try {
            $filters    = $request->only([
                'center',
            ]);
            $anomalies  = $this->anomalyRepository->getAnomaliesLastThirtyDays($filters);
            $labels     = [];
            $quantities = [];

            foreach ($anomalies as $anomaly) {
                $labels[]     = format_date($anomaly->date, 'd-m-Y');
                $quantities[] = $anomaly->quantity;
            }

            return response()->json([
                'success'    => true,
                'labels'     => $labels,
                'quantities' => $quantities,
                'message'    => 'Datos cargados con éxito',
            ], 200);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());

            return response()->json([
                'message' => 'Hubo un problema al cargar los datos',
            ], 422);
        }
    }
    public function workOrdersResume(Request $request): JsonResponse
    {
        try {
            $filters       = $request->only([
                'center',
            ]);
            $workOrderData = $this->workOrderRepository->getWorkOrderResume($filters);

            return response()->json([
                'success' => true,
                'data'    => $workOrderData,
                'message' => 'Datos cargados con éxito',
            ]);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());

            return response()->json([
                'message' => 'Hubo un problema al cargar los datos',
            ], 422);
        }
    }

    public function workOrdersPendantsResume(Request $request): JsonResponse
    {
        try {
            $filters       = $request->only([
                'center',
            ]);
            $workOrderData = $this->workOrderRepository->getWorkOrderPendantsResume($filters);

            return response()->json([
                'success' => true,
                'data'    => $workOrderData,
                'message' => 'Datos cargados con éxito',
            ]);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());

            return response()->json([
                'message' => 'Hubo un problema al cargar los datos',
            ], 422);
        }
    }

    public function anomaliesResume(Request $request): JsonResponse
    {
        try {
            $filters       = $request->only([
                'center',
            ]);
            $anomaliesData = $this->anomalyRepository->getAnomalyResume($filters);

            return response()->json([
                'success' => true,
                'data'    => $anomaliesData,
                'message' => 'Datos cargados con éxito',
            ]);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());

            return response()->json([
                'message' => 'Hubo un problema al cargar los datos',
            ], 422);
        }
    }

    public function anomalyPendantsResume(Request $request): JsonResponse
    {
        try {
            $filters       = $request->only([
                'center',
            ]);
            $anomaliesData = $this->anomalyRepository->getAnomalyPendantsResume($filters);

            return response()->json([
                'success' => true,
                'data'    => $anomaliesData,
                'message' => 'Datos cargados con éxito',
            ]);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());

            return response()->json([
                'message' => 'Hubo un problema al cargar los datos',
            ], 422);
        }
    }
}
