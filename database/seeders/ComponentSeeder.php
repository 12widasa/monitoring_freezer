<?php

namespace Database\Seeders;

use App\Models\Component;
use App\Models\Repair;
use App\Models\RepairComponent;
use App\Models\RepairComponentLog;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ComponentSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $admin = User::query()
                ->where('username', 'admin')
                ->first();

            $budi = User::query()
                ->where('username', 'teknisi.budi')
                ->first();

            $rizky = User::query()
                ->where('username', 'teknisi.rizky')
                ->first();

            if (
                $admin === null
                || $budi === null
                || $rizky === null
            ) {
                throw new RuntimeException(
                    'User prerequisite komponen belum lengkap.',
                );
            }

            $components = [
                [
                    'name' => 'Thermostat Freezer',
                    'part_number' => 'THM-FRZ-001',
                    'unit' => 'pcs',
                ],
                [
                    'name' => 'Kompresor Freezer',
                    'part_number' => 'KMP-FRZ-001',
                    'unit' => 'unit',
                ],
                [
                    'name' => 'Filter Dryer',
                    'part_number' => 'FLT-DRY-001',
                    'unit' => 'pcs',
                ],
                [
                    'name' => 'Relay Kompresor',
                    'part_number' => 'RLY-KMP-001',
                    'unit' => 'pcs',
                ],
                [
                    'name' => 'Kapasitor',
                    'part_number' => 'KPS-FRZ-001',
                    'unit' => 'pcs',
                ],
                [
                    'name' => 'Fan Motor Evaporator',
                    'part_number' => 'FAN-EVP-001',
                    'unit' => 'unit',
                ],
                [
                    'name' => 'Refrigeran R134a',
                    'part_number' => 'RFG-R134A',
                    'unit' => 'gram',
                ],
            ];

            foreach ($components as $data) {
                Component::query()->updateOrCreate(
                    [
                        'part_number' => $data['part_number'],
                    ],
                    $data,
                );
            }

            $completedRepair = Repair::query()
                ->whereHas('freezer', function ($query): void {
                    $query->where(
                        'serial_number',
                        'MDN-450-COMPLETED-001',
                    );
                })
                ->first();

            $repairingRepair = Repair::query()
                ->whereHas('freezer', function ($query): void {
                    $query->where(
                        'serial_number',
                        'SHP-300-1005',
                    );
                })
                ->first();

            if (
                $completedRepair === null
                || $repairingRepair === null
            ) {
                throw new RuntimeException(
                    'Repair prerequisite komponen belum lengkap.',
                );
            }

            $thermostat = Component::query()
                ->where('part_number', 'THM-FRZ-001')
                ->firstOrFail();

            $filterDryer = Component::query()
                ->where('part_number', 'FLT-DRY-001')
                ->firstOrFail();

            $refrigerant = Component::query()
                ->where('part_number', 'RFG-R134A')
                ->firstOrFail();

            $installedAt = now()->subDays(5);

            $installedThermostat = RepairComponent::query()
                ->updateOrCreate(
                    [
                        'repair_id' => $completedRepair->id,
                        'component_id' => $thermostat->id,
                    ],
                    [
                        'quantity' => 1,
                        'status' => 'installed',
                        'note' =>
                        'Thermostat lama rusak dan telah diganti.',
                        'added_by' => $budi->id,
                        'installed_by' => $budi->id,
                        'installed_at' => $installedAt,
                    ],
                );

            RepairComponentLog::query()->updateOrCreate(
                [
                    'repair_component_id' =>
                    $installedThermostat->id,
                    'status' => 'installed',
                ],
                [
                    'quantity' => 1,
                    'note' =>
                    'Thermostat berhasil dipasang dan diuji.',
                    'updated_by' => $budi->id,
                    'created_at' => $installedAt,
                ],
            );

            $requestedFilter = RepairComponent::query()
                ->updateOrCreate(
                    [
                        'repair_id' => $repairingRepair->id,
                        'component_id' => $filterDryer->id,
                    ],
                    [
                        'quantity' => 1,
                        'status' => 'requested',
                        'note' =>
                        'Filter dryer diperlukan untuk penggantian.',
                        'added_by' => $rizky->id,
                        'installed_by' => null,
                        'installed_at' => null,
                    ],
                );

            RepairComponentLog::query()->updateOrCreate(
                [
                    'repair_component_id' =>
                    $requestedFilter->id,
                    'status' => 'requested',
                ],
                [
                    'quantity' => 1,
                    'note' =>
                    'Permintaan filter dryer diajukan.',
                    'updated_by' => $rizky->id,
                    'created_at' => now()->subHours(2),
                ],
            );

            $requestedRefrigerant = RepairComponent::query()
                ->updateOrCreate(
                    [
                        'repair_id' => $repairingRepair->id,
                        'component_id' => $refrigerant->id,
                    ],
                    [
                        'quantity' => 500,
                        'status' => 'requested',
                        'note' =>
                        'Refrigeran diperlukan untuk pengisian ulang.',
                        'added_by' => $rizky->id,
                        'installed_by' => null,
                        'installed_at' => null,
                    ],
                );

            RepairComponentLog::query()->updateOrCreate(
                [
                    'repair_component_id' =>
                    $requestedRefrigerant->id,
                    'status' => 'requested',
                ],
                [
                    'quantity' => 500,
                    'note' =>
                    'Permintaan refrigeran R134a diajukan.',
                    'updated_by' => $rizky->id,
                    'created_at' => now()->subHour(),
                ],
            );
        });
    }
}
