<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RepairStatus;
use App\Http\Controllers\Controller;
use App\Models\Freezer;
use App\Models\Repair;
use App\Models\RepairLog;
use Carbon\CarbonImmutable;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $displayTimezone = config(
            'app.display_timezone',
            'Asia/Jakarta',
        );

        $localNow = CarbonImmutable::now($displayTimezone);

        /*
        |--------------------------------------------------------------------------
        | Batas periode lokal yang dikonversi ke UTC
        |--------------------------------------------------------------------------
        |
        | Database menyimpan waktu dengan konvensi UTC. Periode seperti hari
        | ini, minggu ini, dan bulan ini harus dihitung berdasarkan timezone
        | tampilan, kemudian dikonversi ke UTC untuk query database.
        |
        */

        $todayStartUtc = $localNow
            ->startOfDay()
            ->utc();

        $tomorrowStartUtc = $localNow
            ->addDay()
            ->startOfDay()
            ->utc();

        $weekStartUtc = $localNow
            ->startOfWeek()
            ->startOfDay()
            ->utc();

        $nextWeekStartUtc = $localNow
            ->addWeek()
            ->startOfWeek()
            ->startOfDay()
            ->utc();

        $monthStartUtc = $localNow
            ->startOfMonth()
            ->startOfDay()
            ->utc();

        $nextMonthStartUtc = $localNow
            ->addMonth()
            ->startOfMonth()
            ->startOfDay()
            ->utc();

        $summary = [
            'active_repairs' => Repair::query()
                ->where(
                    'status',
                    '!=',
                    RepairStatus::COMPLETED->value,
                )
                ->count(),

            'total_freezers' => Freezer::query()->count(),

            'freezers_created_this_month' => Freezer::query()
                ->where('created_at', '>=', $monthStartUtc)
                ->where('created_at', '<', $nextMonthStartUtc)
                ->count(),

            'queued_repairs' => Repair::query()
                ->where(
                    'status',
                    RepairStatus::QUEUED->value,
                )
                ->count(),

            'queued_created_today' => Repair::query()
                ->where(
                    'status',
                    RepairStatus::QUEUED->value,
                )
                ->where('created_at', '>=', $todayStartUtc)
                ->where('created_at', '<', $tomorrowStartUtc)
                ->count(),

            'repairing_repairs' => Repair::query()
                ->where(
                    'status',
                    RepairStatus::REPAIRING->value,
                )
                ->count(),

            'repairing_updated_today' => Repair::query()
                ->where(
                    'status',
                    RepairStatus::REPAIRING->value,
                )
                ->where('updated_at', '>=', $todayStartUtc)
                ->where('updated_at', '<', $tomorrowStartUtc)
                ->count(),

            /*
             * Sementara menggunakan updated_at karena tabel repairs
             * belum mempunyai kolom completed_at.
             */
            'completed_this_month' => Repair::query()
                ->where(
                    'status',
                    RepairStatus::COMPLETED->value,
                )
                ->where('updated_at', '>=', $monthStartUtc)
                ->where('updated_at', '<', $nextMonthStartUtc)
                ->count(),

            'completed_this_week' => Repair::query()
                ->where(
                    'status',
                    RepairStatus::COMPLETED->value,
                )
                ->where('updated_at', '>=', $weekStartUtc)
                ->where('updated_at', '<', $nextWeekStartUtc)
                ->count(),
        ];

        $latestFreezers = Freezer::query()
            ->with('customer')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(4)
            ->get();

        $latestActivities = RepairLog::query()
            ->with([
                'repair.freezer',
                'updater',
            ])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(4)
            ->get();

        /*
|--------------------------------------------------------------------------
| Repair yang memerlukan perhatian admin
|--------------------------------------------------------------------------
|
| 1. Repair queued tanpa teknisi langsung memerlukan perhatian.
| 2. Repair queued yang masih belum bergerak tiga jam setelah penugasan.
| 3. Repair repairing tanpa pembaruan selama minimal 24 jam.
|
*/

        $queuedAssignmentCutoffUtc = $localNow
            ->subHours(3)
            ->utc();

        $repairingUpdateCutoffUtc = $localNow
            ->subDay()
            ->utc();

        $attentionRepairs = Repair::query()
            ->with([
                'freezer',
                'activeAssignment',
            ])
            ->where(function ($query) use (
                $queuedAssignmentCutoffUtc,
                $repairingUpdateCutoffUtc,
            ): void {
                $query
                    ->where(function ($query): void {
                        $query
                            ->where(
                                'status',
                                RepairStatus::QUEUED->value,
                            )
                            ->whereNull('technician_id');
                    })
                    ->orWhere(function ($query) use (
                        $queuedAssignmentCutoffUtc,
                    ): void {
                        $query
                            ->where(
                                'status',
                                RepairStatus::QUEUED->value,
                            )
                            ->whereNotNull('technician_id')
                            ->whereHas(
                                'activeAssignment',
                                function ($query) use (
                                    $queuedAssignmentCutoffUtc,
                                ): void {
                                    $query->where(
                                        'assigned_at',
                                        '<=',
                                        $queuedAssignmentCutoffUtc,
                                    );
                                },
                            );
                    })
                    ->orWhere(function ($query) use (
                        $repairingUpdateCutoffUtc,
                    ): void {
                        $query
                            ->where(
                                'status',
                                RepairStatus::REPAIRING->value,
                            )
                            ->where(
                                'updated_at',
                                '<=',
                                $repairingUpdateCutoffUtc,
                            );
                    });
            })
            ->get()
            ->map(function (Repair $repair): array {
                if (
                    $repair->status === RepairStatus::QUEUED
                    && $repair->technician_id === null
                ) {
                    return [
                        'repair' => $repair,
                        'type' => 'unassigned',
                        'priority' => 1,
                        'reference_at' => $repair->created_at,
                    ];
                }

                if ($repair->status === RepairStatus::QUEUED) {
                    return [
                        'repair' => $repair,
                        'type' => 'waiting_inspection',
                        'priority' => 2,
                        'reference_at' =>
                        $repair->activeAssignment?->assigned_at
                            ?? $repair->updated_at,
                    ];
                }

                return [
                    'repair' => $repair,
                    'type' => 'stale_repair',
                    'priority' => 3,
                    'reference_at' => $repair->updated_at,
                ];
            })
            ->sortBy(function (array $item): string {
                return sprintf(
                    '%d-%020d',
                    $item['priority'],
                    $item['reference_at']->timestamp,
                );
            })
            ->take(4)
            ->values();

        return view(
            'admin.dashboard',
            compact(
                'summary',
                'latestFreezers',
                'latestActivities',
                'attentionRepairs',
            ),
        );
    }
}
