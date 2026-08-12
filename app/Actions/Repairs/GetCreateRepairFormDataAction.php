<?php

namespace App\Actions\Repairs;

use App\Enums\RepairStatus;
use App\Enums\UserRole;
use App\Enums\VerificationStatus;
use App\Models\Freezer;
use App\Models\ServiceIntake;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class GetCreateRepairFormDataAction
{
    /**
     * @return array{
     *     eligibleIntakes: Collection<int, ServiceIntake>,
     *     technicians: Collection<int, User>,
     *     preselectedIntakeId: int|null
     * }
     */
    public function execute(
        ?Freezer $freezer = null,
    ): array {
        $eligibleIntakes = ServiceIntake::query()
            ->with([
                'freezer:id,customer_id,freezer_code,brand,model',
                'freezer.customer:id,company_name',
            ])
            ->where(
                'status_verifikasi',
                VerificationStatus::VERIFIED,
            )
            ->whereDoesntHave('repair')
            ->whereNotExists(function ($query): void {
                $query
                    ->selectRaw('1')
                    ->from('service_intakes as newer_intakes')
                    ->whereColumn(
                        'newer_intakes.freezer_id',
                        'service_intakes.freezer_id',
                    )
                    ->where(function ($query): void {
                        $query
                            ->whereColumn(
                                'newer_intakes.received_at',
                                '>',
                                'service_intakes.received_at',
                            )
                            ->orWhere(function ($query): void {
                                $query
                                    ->whereColumn(
                                        'newer_intakes.received_at',
                                        'service_intakes.received_at',
                                    )
                                    ->whereColumn(
                                        'newer_intakes.id',
                                        '>',
                                        'service_intakes.id',
                                    );
                            });
                    });
            })
            ->when(
                $freezer !== null,
                fn(Builder $query) => $query->where(
                    'freezer_id',
                    $freezer->id,
                ),
            )
            ->latest('received_at')
            ->latest('id')
            ->get();

        $technicians = $this->technicians();

        return [
            'eligibleIntakes' => $eligibleIntakes,
            'technicians' => $technicians,
            'preselectedIntakeId' => $freezer !== null
                ? $eligibleIntakes->first()?->id
                : null,
        ];
    }
    /**
     * @return Collection<int, User>
     */
    public function technicians(): Collection
    {
        return User::query()
            ->where('role', UserRole::TECHNICIAN)
            ->where('is_active', true)
            ->withCount([
                'assignedRepairs as active_repairs_count' =>
                fn(Builder $query) => $query->where(
                    'status',
                    '!=',
                    RepairStatus::COMPLETED,
                ),
            ])
            ->orderBy('name')
            ->get([
                'id',
                'name',
            ]);
    }
}
