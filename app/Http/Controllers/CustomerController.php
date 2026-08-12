<?php

namespace App\Http\Controllers;

use App\Enums\RepairStatus;
use App\Models\Freezer;
use App\Models\Repair;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function monitoring(): View
    {
        $customer = Auth::user()->customer;

        abort_unless($customer !== null, 404);

        $activeStatuses = [
            RepairStatus::QUEUED->value,
            RepairStatus::INSPECTING->value,
            RepairStatus::REPAIRING->value,
        ];

        $freezers = Freezer::query()
            ->where('customer_id', $customer->id)
            ->with([
                'latestRepair' => function ($query): void {
                    $query->with('latestLog');
                },
            ])
            ->latest()
            ->get();

        $totalFreezerCount = $freezers->count();

        $activeRepairCount = $freezers
            ->filter(fn (Freezer $freezer) => $freezer->latestRepair !== null
                && in_array($freezer->latestRepair->status->value, $activeStatuses, true))
            ->count();

        $completedRepairCount = $freezers
            ->filter(fn (Freezer $freezer) => $freezer->latestRepair !== null
                && $freezer->latestRepair->status === RepairStatus::COMPLETED)
            ->count();

        return view('customer.monitoring', compact(
            'freezers',
            'totalFreezerCount',
            'activeRepairCount',
            'completedRepairCount',
        ));
    }

    public function showRepair(Repair $repair): View
    {
        $customer = Auth::user()->customer;

        abort_unless($customer !== null, 404);

        $repair->load('freezer');

        abort_unless(
            $repair->freezer->customer_id === $customer->id,
            404,
        );

        $repair->load([
            'freezer',
            'technician',
            'logs' => function ($query): void {
                $query->with('photos')->oldest('created_at');
            },
            'components' => function ($query): void {
                $query->with('component');
            },
        ]);

        return view('customer.repairs.show', compact('repair'));
    }
}