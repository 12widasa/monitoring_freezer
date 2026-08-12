<?php

namespace Database\Seeders;

use App\Enums\RepairStatus;
use App\Enums\UserRole;
use App\Enums\VerificationStatus;
use App\Models\Customer;
use App\Models\Freezer;
use App\Models\Repair;
use App\Models\RepairAssignment;
use App\Models\RepairLog;
use App\Models\ServiceIntake;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CompletedRepairSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $admin = User::query()
                ->where('username', 'admin')
                ->first();

            $technician = User::query()
                ->where('username', 'teknisi.budi')
                ->where('role', UserRole::TECHNICIAN->value)
                ->where('is_active', true)
                ->first();

            $customer = Customer::query()
                ->whereHas('user', function ($query): void {
                    $query->where('username', 'customer.jaya');
                })
                ->first();

            if (
                $admin === null
                || $technician === null
                || $customer === null
            ) {
                throw new RuntimeException(
                    'Data prerequisite completed repair belum lengkap.',
                );
            }

            $receivedAt = now()->subDays(7);
            $verifiedAt = $receivedAt->copy()->addHour();
            $assignedAt = $verifiedAt->copy()->addHour();
            $inspectedAt = $assignedAt->copy()->addHours(3);
            $repairingAt = $inspectedAt->copy()->addHours(5);
            $completedAt = $repairingAt->copy()->addDay();

            $freezer = Freezer::query()->updateOrCreate(
                [
                    'serial_number' => 'MDN-450-COMPLETED-001',
                ],
                [
                    'customer_id' => $customer->id,
                    'brand' => 'Modena',
                    'model' => 'MD-450',
                    'capacity_liter' => 450,
                    'estimated_age' => '1-3 tahun',
                    'photo_path' => null,
                    'created_by' => $admin->id,
                ],
            );

            $freezer->forceFill([
                'freezer_code' => sprintf(
                    'FZ-%05d',
                    $freezer->id,
                ),
            ])->save();

            $intake = ServiceIntake::query()->updateOrCreate(
                [
                    'freezer_id' => $freezer->id,
                ],
                [
                    'intake_code' =>
                    'TMP-' . bin2hex(random_bytes(8)),
                    'complaint_note' =>
                    'Freezer menyala tetapi suhu tidak turun.',
                    'condition_note' =>
                    'Unit diterima lengkap dengan kondisi fisik baik.',
                    'status_verifikasi' =>
                    VerificationStatus::VERIFIED->value,
                    'rejection_reason' => null,
                    'received_by' => $admin->id,
                    'received_at' => $receivedAt,
                    'verified_by' => $admin->id,
                    'verified_at' => $verifiedAt,
                    'completed_at' => null,
                ],
            );

            $intake->forceFill([
                'intake_code' => sprintf(
                    'IN-%05d',
                    $intake->id,
                ),
            ])->save();

            $repair = Repair::query()->updateOrCreate(
                [
                    'service_intake_id' => $intake->id,
                ],
                [
                    'freezer_id' => $freezer->id,
                    'technician_id' => $technician->id,
                    'admin_id' => $admin->id,
                    'status' => RepairStatus::COMPLETED->value,
                    'initial_analysis' =>
                    'Thermostat tidak bekerja dan memutus proses pendinginan.',
                ],
            );

            $repair->forceFill([
                'created_at' => $assignedAt,
                'updated_at' => $completedAt,
            ])->save();

            RepairAssignment::query()->updateOrCreate(
                [
                    'repair_id' => $repair->id,
                    'technician_id' => $technician->id,
                ],
                [
                    'assigned_by' => $admin->id,
                    'assigned_at' => $assignedAt,
                    'ended_at' => $completedAt,
                    'ended_by' => $admin->id,
                    'end_reason' =>
                    'Perbaikan telah selesai.',
                ],
            );

            $logs = [
                [
                    'status' => RepairStatus::INSPECTING,
                    'description' =>
                    'Pemeriksaan awal sistem pendingin dilakukan.',
                    'created_at' => $inspectedAt,
                ],
                [
                    'status' => RepairStatus::REPAIRING,
                    'description' =>
                    'Thermostat rusak dilepas dan dilakukan penggantian.',
                    'created_at' => $repairingAt,
                ],
                [
                    'status' => RepairStatus::COMPLETED,
                    'description' =>
                    'Penggantian thermostat selesai dan unit telah lulus pengujian suhu.',
                    'created_at' => $completedAt,
                ],
            ];

            foreach ($logs as $log) {
                RepairLog::query()->updateOrCreate(
                    [
                        'repair_id' => $repair->id,
                        'status' => $log['status']->value,
                    ],
                    [
                        'description' => $log['description'],
                        'updated_by' => $technician->id,
                        'created_at' => $log['created_at'],
                    ],
                );
            }
        });
    }
}
