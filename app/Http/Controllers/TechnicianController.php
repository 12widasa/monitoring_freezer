<?php

namespace App\Http\Controllers;

use App\Actions\Repairs\StartInspectionAction;
use App\Actions\Repairs\SubmitInspectionAction;
use App\Actions\Repairs\UpdateRepairProgressAction;
use App\Enums\RepairStatus;
use App\Models\Component;
use App\Models\Repair;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TechnicianController extends Controller
{
    public function dashboard(Request $request): View
    {
        $technicianId = Auth::id();

        $search = trim((string) $request->query('search', ''));
        $selectedStatus = (string) $request->query('status', '');
        $selectedSort = (string) $request->query('sort', 'updated');
        $perPage = (int) $request->integer('per_page', 8);

        $allowedStatuses = array_column(RepairStatus::cases(), 'value');
        $allowedSorts = ['updated', 'newest', 'oldest'];
        $allowedPerPage = [8, 12, 24];

        if (! in_array($selectedStatus, $allowedStatuses, true)) {
            $selectedStatus = '';
        }

        if (! in_array($selectedSort, $allowedSorts, true)) {
            $selectedSort = 'updated';
        }

        if (! in_array($perPage, $allowedPerPage, true)) {
            $perPage = 8;
        }

        $activeStatuses = [
            RepairStatus::QUEUED->value,
            RepairStatus::INSPECTING->value,
            RepairStatus::REPAIRING->value,
        ];

        $baseQuery = Repair::query()->where('technician_id', $technicianId);

        $totalActiveCount = (clone $baseQuery)
            ->whereIn('status', $activeStatuses)
            ->count();

        $waitingCount = (clone $baseQuery)
            ->where('status', RepairStatus::QUEUED->value)
            ->count();

        $inProgressCount = (clone $baseQuery)
            ->whereIn('status', [
                RepairStatus::INSPECTING->value,
                RepairStatus::REPAIRING->value,
            ])
            ->count();

        $repairs = Repair::query()
            ->where('technician_id', $technicianId)
            ->with([
                'freezer',
                'latestLog',
                'activeAssignment',
            ])
            ->when(
                $search !== '',
                function (Builder $query) use ($search): void {
                    $query->whereHas(
                        'freezer',
                        function (Builder $freezerQuery) use ($search): void {
                            $freezerQuery
                                ->where('serial_number', 'like', "%{$search}%")
                                ->orWhere('brand', 'like', "%{$search}%")
                                ->orWhere('model', 'like', "%{$search}%");
                        },
                    );
                },
            )
            ->when(
                $selectedStatus !== '',
                fn (Builder $query) => $query->where('status', $selectedStatus),
                fn (Builder $query) => $query->whereIn('status', $activeStatuses),
            )
            ->when(
                $selectedSort === 'newest',
                fn (Builder $query) => $query->latest('created_at'),
            )
            ->when(
                $selectedSort === 'oldest',
                fn (Builder $query) => $query->oldest('created_at'),
            )
            ->when(
                $selectedSort === 'updated',
                fn (Builder $query) => $query->latest('updated_at'),
            )
            ->paginate($perPage)
            ->withQueryString();

        return view('technician.dashboard', compact(
            'repairs',
            'totalActiveCount',
            'waitingCount',
            'inProgressCount',
            'search',
            'selectedStatus',
            'selectedSort',
            'perPage',
        ));
    }

    public function showRepair(Repair $repair): View
    {
        abort_unless($repair->technician_id === Auth::id(), 404);

        $repair->load([
            'freezer.customer',
            'serviceIntake',
            'logs' => function ($query): void {
                $query->with('photos')->oldest('created_at');
            },
            'components' => function ($query): void {
                $query->with('component');
            },
            'activeAssignment.assignedBy',
            'admin',
        ]);

        $components = Component::query()->orderBy('name')->get();

        return view('technician.repairs.show', compact('repair', 'components'));
    }

    public function startInspection(
        Request $request,
        Repair $repair,
        StartInspectionAction $startInspection,
    ): RedirectResponse {
        abort_unless($repair->technician_id === Auth::id(), 404);

        $startInspection->execute($repair, $request->user());

        return redirect()
            ->route('technician.repairs.show', $repair->id)
            ->with('success', 'Pemeriksaan dimulai.');
    }

    public function storeInspection(
        Request $request,
        Repair $repair,
        SubmitInspectionAction $submitInspection,
    ): RedirectResponse {
        abort_unless($repair->technician_id === Auth::id(), 404);

        $data = $request->validate([
            'description' => ['required', 'string', 'max:2000'],
            'photos' => ['nullable', 'array', 'max:3'],
            'photos.*' => ['image', 'max:5120'],
            'components' => ['nullable', 'array'],
            'components.*.component_id' => ['required_with:components', 'exists:components,id'],
            'components.*.quantity' => ['required_with:components', 'integer', 'min:1'],
            'components.*.note' => ['nullable', 'string', 'max:255'],
        ]);

        $submitInspection->execute($repair, $data, $request->user());

        return redirect()
            ->route('technician.repairs.show', $repair->id)
            ->with('success', 'Hasil pemeriksaan berhasil disimpan. Perbaikan dimulai.');
    }

    public function storeProgress(
        Request $request,
        Repair $repair,
        UpdateRepairProgressAction $updateRepairProgress,
    ): RedirectResponse {
        abort_unless($repair->technician_id === Auth::id(), 404);

        $data = $request->validate([
            'description' => ['required', 'string', 'max:2000'],
            'photos' => ['nullable', 'array', 'max:3'],
            'photos.*' => ['image', 'max:5120'],
            'installed_component_ids' => ['nullable', 'array'],
            'installed_component_ids.*' => ['integer', 'exists:repair_components,id'],
        ]);

        $updateRepairProgress->execute($repair, $data, $request->user());

        return redirect()
            ->route('technician.repairs.show', $repair->id)
            ->with('success', 'Reparasi berhasil diselesaikan.');
    }
}