<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Repairs\AssignRepairTechnicianAction;
use App\Actions\Repairs\CreateRepairAction;
use App\Actions\Repairs\GetCreateRepairFormDataAction;
use App\Enums\RepairStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRepairRequest;
use App\Http\Requests\Admin\UpdateRepairTechnicianRequest;
use App\Models\Customer;
use App\Models\Repair;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RepairController extends Controller
{
    public function index(
        Request $request,
        GetCreateRepairFormDataAction $getCreateRepairFormData,
    ): View {
        $formData = $getCreateRepairFormData->execute();

        $search = trim((string) $request->query('search', ''));
        $selectedStatus = (string) $request->query('status', '');
        $selectedTechnicianId = (int) $request->integer('technician_id');
        $selectedCustomerId = (int) $request->integer('customer_id');
        $perPage = (int) $request->integer('per_page', 10);

        $allowedStatuses = array_column(
            RepairStatus::cases(),
            'value',
        );

        $allowedPerPage = [
            10,
            25,
            50,
        ];

        if (! in_array($selectedStatus, $allowedStatuses, true)) {
            $selectedStatus = '';
        }

        if (! in_array($perPage, $allowedPerPage, true)) {
            $perPage = 10;
        }

        $repairIdSearch = null;

        if (ctype_digit($search)) {
            $repairIdSearch = (int) $search;
        } elseif (
            preg_match(
                '/^TR-\d{4}-(\d+)$/i',
                $search,
                $matches,
            ) === 1
        ) {
            $repairIdSearch = (int) $matches[1];
        }

        $repairs = Repair::query()
            ->with([
                'freezer.customer:id,company_name',
                'technician:id,name',
                'latestLog',
            ])
            ->when(
                $search !== '',
                function (Builder $query) use (
                    $search,
                    $repairIdSearch,
                ): void {
                    $query->where(
                        function (Builder $query) use (
                            $search,
                            $repairIdSearch,
                        ): void {
                            if ($repairIdSearch !== null) {
                                $query->orWhere(
                                    'id',
                                    $repairIdSearch,
                                );
                            }

                            $query->orWhereHas(
                                'freezer',
                                function (Builder $freezerQuery) use ($search): void {
                                    $freezerQuery
                                        ->where(
                                            'freezer_code',
                                            'like',
                                            "%{$search}%",
                                        )
                                        ->orWhere(
                                            'serial_number',
                                            'like',
                                            "%{$search}%",
                                        )
                                        ->orWhere(
                                            'brand',
                                            'like',
                                            "%{$search}%",
                                        )
                                        ->orWhere(
                                            'model',
                                            'like',
                                            "%{$search}%",
                                        )
                                        ->orWhereHas(
                                            'customer',
                                            fn(Builder $customerQuery) => $customerQuery
                                                ->where(
                                                    'company_name',
                                                    'like',
                                                    "%{$search}%",
                                                ),
                                        );
                                },
                            );
                        },
                    );
                },
            )
            ->when(
                $selectedStatus !== '',
                fn(Builder $query) => $query->where(
                    'status',
                    $selectedStatus,
                ),
            )
            ->when(
                $selectedTechnicianId > 0,
                fn(Builder $query) => $query->where(
                    'technician_id',
                    $selectedTechnicianId,
                ),
            )
            ->when(
                $selectedCustomerId > 0,
                fn(Builder $query) => $query->whereHas(
                    'freezer',
                    fn(Builder $freezerQuery) => $freezerQuery
                        ->where(
                            'customer_id',
                            $selectedCustomerId,
                        ),
                ),
            )
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        $customers = Customer::query()
            ->orderBy('company_name')
            ->get([
                'id',
                'company_name',
            ]);

        $repairSummary = [
            'active' => Repair::query()
                ->where('status', '!=', RepairStatus::COMPLETED->value)
                ->count(),

            'unassigned' => Repair::query()
                ->where('status', '!=', RepairStatus::COMPLETED->value)
                ->whereNull('technician_id')
                ->count(),

            'queued' => Repair::query()
                ->where('status', RepairStatus::QUEUED->value)
                ->whereNotNull('technician_id')
                ->count(),

            'inspecting' => Repair::query()
                ->where('status', RepairStatus::INSPECTING->value)
                ->count(),

            'repairing' => Repair::query()
                ->where('status', RepairStatus::REPAIRING->value)
                ->count(),
        ];

        return view(
            'admin.repairs.index',
            [
                ...$formData,
                'repairs' => $repairs,
                'customers' => $customers,
                'repairSummary' => $repairSummary,
                'filters' => [
                    'search' => $search,
                    'status' => $selectedStatus,
                    'technician_id' => $selectedTechnicianId,
                    'customer_id' => $selectedCustomerId,
                ],
                'perPage' => $perPage,
            ],
        );
    }

    public function show(
        Repair $repair,
        GetCreateRepairFormDataAction $getCreateRepairFormData,
    ): View {
        $repair->load([
            'freezer.customer:id,company_name',
            'serviceIntake',
            'technician:id,name',
            'admin:id,name',

            'latestLog' => fn($query) => $query
                ->with([
                    'updater:id,name',

                    'photos' => fn($query) => $query
                        ->orderBy('sort_order')
                        ->orderBy('id'),
                ]),

            'logs' => fn($query) => $query
                ->with([
                    'updater:id,name',

                    'photos' => fn($query) => $query
                        ->orderBy('sort_order')
                        ->orderBy('id'),
                ])
                ->latest('created_at'),

            'components' => fn($query) => $query
                ->with([
                    'component:id,name,part_number,unit',
                    'addedBy:id,name',
                    'installedBy:id,name',
                ])
                ->latest(),
        ]);

        $technicians = $getCreateRepairFormData->technicians();

        return view(
            'admin.repairs.show',
            compact(
                'repair',
                'technicians',
            ),
        );
    }

    public function store(
        StoreRepairRequest $request,
        CreateRepairAction $createRepair,
    ): RedirectResponse {
        $repair = $createRepair->execute(
            data: $request->validated(),
            admin: $request->user(),
        );

        return redirect()
            ->route('admin.repairs.show', $repair)
            ->with(
                'success',
                'Tugas reparasi berhasil dibuat.',
            );
    }

    public function updateTechnician(
        UpdateRepairTechnicianRequest $request,
        Repair $repair,
        AssignRepairTechnicianAction $assignRepairTechnician,
    ): RedirectResponse {
        $hadTechnician = $repair->technician_id !== null;

        $updatedRepair = $assignRepairTechnician->execute(
            repair: $repair,
            data: $request->validated(),
            admin: $request->user(),
        );

        return redirect()
            ->route('admin.repairs.show', $updatedRepair)
            ->with(
                'success',
                $hadTechnician
                    ? 'Teknisi berhasil diganti.'
                    : 'Teknisi berhasil ditetapkan.',
            );
    }
}
