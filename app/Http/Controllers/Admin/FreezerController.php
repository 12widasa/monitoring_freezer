<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Freezers\CreateFreezerAction;
use App\Actions\Freezers\UpdateFreezerAction;
use App\Actions\ServiceIntakes\VerifyServiceIntakeAction;
use App\Actions\Repairs\GetCreateRepairFormDataAction;
use App\Http\Requests\Admin\VerifyServiceIntakeRequest;
use App\Enums\RepairStatus;
use App\Enums\VerificationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFreezerRequest;
use App\Http\Requests\Admin\UpdateFreezerRequest;
use App\Models\Customer;
use App\Models\Freezer;
use App\Models\Repair;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class FreezerController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $selectedRepairStatus = (string) $request->query('repair_status', '');
        $selectedVerificationStatus = (string) $request->query('verification_status', '');
        $selectedSort = (string) $request->query('sort', 'latest');
        $perPage = (int) $request->integer('per_page', 10);

        $allowedRepairStatuses = array_column(
            RepairStatus::cases(),
            'value',
        );

        $allowedVerificationStatuses = array_column(
            VerificationStatus::cases(),
            'value',
        );

        $allowedSorts = [
            'latest',
            'oldest',
            'updated',
            'owner_asc',
        ];

        $allowedPerPage = [
            10,
            25,
            50,
        ];

        if (! in_array($selectedRepairStatus, $allowedRepairStatuses, true)) {
            $selectedRepairStatus = '';
        }

        if (! in_array($selectedVerificationStatus, $allowedVerificationStatuses, true)) {
            $selectedVerificationStatus = '';
        }

        if (! in_array($selectedSort, $allowedSorts, true)) {
            $selectedSort = 'latest';
        }

        if (! in_array($perPage, $allowedPerPage, true)) {
            $perPage = 10;
        }

        $freezersQuery = Freezer::query()
            ->with([
                'customer:id,company_name',
                'latestIntake.repair',
            ])
            ->when(
                $search !== '',
                function (Builder $query) use ($search): void {
                    $query->where(function (Builder $query) use ($search): void {
                        $query
                            ->where('freezer_code', 'like', "%{$search}%")
                            ->orWhere('serial_number', 'like', "%{$search}%")
                            ->orWhere('brand', 'like', "%{$search}%")
                            ->orWhere('model', 'like', "%{$search}%")
                            ->orWhereHas(
                                'customer',
                                fn(Builder $customerQuery) => $customerQuery
                                    ->where('company_name', 'like', "%{$search}%"),
                            );
                    });
                },
            )
            ->when(
                $selectedRepairStatus !== '',
                fn(Builder $query) => $query->whereHas(
                    'latestIntake.repair',
                    fn(Builder $repairQuery) => $repairQuery
                        ->where('status', $selectedRepairStatus),
                ),
            )
            ->when(
                $selectedVerificationStatus !== '',
                fn(Builder $query) => $query->whereHas(
                    'latestIntake',
                    fn(Builder $intakeQuery) => $intakeQuery
                        ->where(
                            'status_verifikasi',
                            $selectedVerificationStatus,
                        ),
                ),
            );

        match ($selectedSort) {
            'oldest' => $freezersQuery->oldest(),
            'updated' => $freezersQuery->latest('updated_at'),
            'owner_asc' => $freezersQuery->orderBy(
                Customer::query()
                    ->select('company_name')
                    ->whereColumn('customers.id', 'freezers.customer_id')
                    ->limit(1),
            ),
            default => $freezersQuery->latest(),
        };

        $freezers = $freezersQuery
            ->paginate($perPage)
            ->withQueryString();

        $customers = Customer::query()
            ->orderBy('company_name')
            ->get([
                'id',
                'company_name',
            ]);

        $summary = [
            'total' => Freezer::query()->count(),

            'added_this_month' => Freezer::query()
                ->whereBetween('created_at', [
                    now()->startOfMonth(),
                    now()->endOfMonth(),
                ])
                ->count(),

            'queued' => Freezer::query()
                ->whereHas(
                    'latestIntake.repair',
                    fn(Builder $query) => $query
                        ->where('status', RepairStatus::QUEUED),
                )
                ->count(),

            'queued_today' => Repair::query()
                ->where('status', RepairStatus::QUEUED)
                ->whereDate('created_at', today())
                ->count(),

            'repairing' => Freezer::query()
                ->whereHas(
                    'latestIntake.repair',
                    fn(Builder $query) => $query
                        ->where('status', RepairStatus::REPAIRING),
                )
                ->count(),

            'repairing_updated_today' => Repair::query()
                ->where('status', RepairStatus::REPAIRING)
                ->whereDate('updated_at', today())
                ->count(),

            'completed_this_month' => Repair::query()
                ->where('status', RepairStatus::COMPLETED)
                ->whereBetween('updated_at', [
                    now()->startOfMonth(),
                    now()->endOfMonth(),
                ])
                ->count(),

            'completed_this_week' => Repair::query()
                ->where('status', RepairStatus::COMPLETED)
                ->whereBetween('updated_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek(),
                ])
                ->count(),
        ];

        return view('admin.freezers.index', compact(
            'customers',
            'freezers',
            'summary',
            'search',
            'selectedRepairStatus',
            'selectedVerificationStatus',
            'selectedSort',
            'perPage',
        ));
    }

    public function show(
        Freezer $freezer,
        GetCreateRepairFormDataAction $getCreateRepairFormData,
    ): View {
        $freezer->load([
            'customer:id,company_name,address,phone',

            'latestIntake.verifier:id,name',
            'latestIntake.receiver:id,name',
            'latestIntake.repair.technician:id,name',
            'latestIntake.repair.admin:id,name',

            'serviceIntakes' => fn($query) => $query
                ->latest('received_at')
                ->latest('id'),

            'serviceIntakes.receiver:id,name',
            'serviceIntakes.verifier:id,name',
            'serviceIntakes.repair.technician:id,name',

            'repairs' => fn($query) => $query
                ->latest('created_at')
                ->latest('id'),

            'repairs.technician:id,name',
            'repairs.admin:id,name',
            'repairs.serviceIntake:id,intake_code',
        ]);

        $formData = $getCreateRepairFormData->execute(
            freezer: $freezer,
        );

        return view(
            'admin.freezers.show',
            [
                'freezer' => $freezer,
                ...$formData,
            ],
        );
    }

    public function store(
        StoreFreezerRequest $request,
        CreateFreezerAction $createFreezer,
    ): RedirectResponse {
        $createFreezer->execute(
            data: $request->validated(),
            creator: $request->user(),
        );

        return redirect()
            ->route(
                'admin.freezers.index',
                $this->indexQuery($request),
            )
            ->with('success', 'Freezer berhasil ditambahkan.');
    }

    public function update(
        UpdateFreezerRequest $request,
        Freezer $freezer,
        UpdateFreezerAction $updateFreezer,
    ): RedirectResponse {
        $updateFreezer->execute(
            freezer: $freezer,
            data: $request->validated(),
        );

        return redirect()
            ->route(
                'admin.freezers.index',
                $this->indexQuery($request),
            )
            ->with('success', 'Freezer berhasil diperbarui.');
    }

    public function verify(
        VerifyServiceIntakeRequest $request,
        Freezer $freezer,
        VerifyServiceIntakeAction $verifyServiceIntake,
    ): RedirectResponse {
        $intake = $verifyServiceIntake->execute(
            freezer: $freezer,
            data: $request->validated(),
            verifier: $request->user(),
        );

        $message = match ($intake->status_verifikasi) {
            VerificationStatus::VERIFIED =>
            'Freezer berhasil diverifikasi.',

            VerificationStatus::REJECTED =>
            'Verifikasi freezer ditolak.',

            default =>
            'Status verifikasi freezer berhasil diperbarui.',
        };

        return redirect()
            ->back()
            ->with('success', $message);
    }

    private function indexQuery(Request $request): array
    {
        return $request->only([
            'search',
            'repair_status',
            'verification_status',
            'sort',
            'per_page',
            'page',
        ]);
    }
}
