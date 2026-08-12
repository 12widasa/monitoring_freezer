<?php

namespace Database\Seeders;

use App\Enums\VerificationStatus;
use App\Models\Customer;
use App\Models\Freezer;
use App\Models\ServiceIntake;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class FreezerIntakeSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $admin = User::query()
                ->where('username', 'admin')
                ->first();

            if ($admin === null) {
                throw new RuntimeException(
                    'User admin belum tersedia. Jalankan UserSeeder terlebih dahulu.',
                );
            }

            $customers = Customer::query()
                ->whereHas('user', function ($query): void {
                    $query->where('is_active', true);
                })
                ->orderBy('id')
                ->get();

            if ($customers->count() < 3) {
                throw new RuntimeException(
                    'Minimal tiga customer diperlukan. Jalankan CustomerSeeder terlebih dahulu.',
                );
            }

            $records = [
                [
                    'customer_id' => $customers[0]->id,
                    'brand' => 'Modena',
                    'model' => 'MD-320',
                    'serial_number' => 'MDN-320-1001',
                    'capacity_liter' => 320,
                    'estimated_age' => '1-3 tahun',
                    'complaint_note' =>
                    'Freezer tidak dapat mencapai suhu dingin.',
                    'condition_note' => null,
                    'status' =>
                    VerificationStatus::PENDING_ARRIVAL,
                    'rejection_reason' => null,
                    'received_at' => now()->subHours(2),
                ],
                [
                    'customer_id' => $customers[1]->id,
                    'brand' => 'GEA',
                    'model' => 'AB-600',
                    'serial_number' => 'GEA-600-1002',
                    'capacity_liter' => 600,
                    'estimated_age' => '>3 tahun',
                    'complaint_note' =>
                    'Mesin mati dan tidak mengeluarkan suara.',
                    'condition_note' => null,
                    'status' =>
                    VerificationStatus::PENDING_ARRIVAL,
                    'rejection_reason' => null,
                    'received_at' => now()->subHours(5),
                ],
                [
                    'customer_id' => $customers[0]->id,
                    'brand' => 'Polytron',
                    'model' => 'SCN-200',
                    'serial_number' => 'PLT-200-1003',
                    'capacity_liter' => 200,
                    'estimated_age' => '<1 tahun',
                    'complaint_note' =>
                    'Suhu tidak stabil dan sering naik.',
                    'condition_note' =>
                    'Kondisi fisik baik, kompresor menyala.',
                    'status' => VerificationStatus::VERIFIED,
                    'rejection_reason' => null,
                    'received_at' => now()->subDays(1),
                ],
                [
                    'customer_id' => $customers[1]->id,
                    'brand' => 'Aqua',
                    'model' => 'AQF-400',
                    'serial_number' => 'AQA-400-1004',
                    'capacity_liter' => 400,
                    'estimated_age' => '1-3 tahun',
                    'complaint_note' =>
                    'Bunga es terlalu tebal pada evaporator.',
                    'condition_note' =>
                    'Unit diterima lengkap dan siap diperiksa.',
                    'status' => VerificationStatus::VERIFIED,
                    'rejection_reason' => null,
                    'received_at' => now()->subDays(2),
                ],
                [
                    'customer_id' => $customers[2]->id,
                    'brand' => 'Sharp',
                    'model' => 'FRV-300',
                    'serial_number' => 'SHP-300-1005',
                    'capacity_liter' => 300,
                    'estimated_age' => '>3 tahun',
                    'complaint_note' =>
                    'Kompresor hidup tetapi freezer tidak dingin.',
                    'condition_note' =>
                    'Body lecet ringan, kelistrikan menyala.',
                    'status' => VerificationStatus::VERIFIED,
                    'rejection_reason' => null,
                    'received_at' => now()->subDays(3),
                ],
                [
                    'customer_id' => $customers[1]->id,
                    'brand' => 'GEA',
                    'model' => 'AB-450',
                    'serial_number' => 'RSA-500-1007',
                    'capacity_liter' => 450,
                    'estimated_age' => '1-3 tahun',
                    'complaint_note' =>
                    'Freezer tidak dingin secara konsisten.',
                    'condition_note' =>
                    'Unit diterima lengkap dan telah diverifikasi.',
                    'status' => VerificationStatus::VERIFIED,
                    'rejection_reason' => null,
                    'received_at' => now()->subHours(10),
                ],
                [
                    'customer_id' => $customers[2]->id,
                    'brand' => 'RSA',
                    'model' => 'CF-500',
                    'serial_number' => 'RSA-500-1006',
                    'capacity_liter' => 500,
                    'estimated_age' => '>3 tahun',
                    'complaint_note' =>
                    'Unit tidak menyala setelah terkena air.',
                    'condition_note' =>
                    'Kerusakan fisik berat dan data unit tidak sesuai.',
                    'status' => VerificationStatus::REJECTED,
                    'rejection_reason' =>
                    'Kondisi unit tidak sesuai data penerimaan dan tidak aman untuk diproses.',
                    'received_at' => now()->subDays(4),
                ],
            ];

            foreach ($records as $record) {
                $freezer = Freezer::query()->updateOrCreate(
                    [
                        'serial_number' => $record['serial_number'],
                    ],
                    [
                        'customer_id' => $record['customer_id'],
                        'brand' => $record['brand'],
                        'model' => $record['model'],
                        'capacity_liter' =>
                        $record['capacity_liter'],
                        'estimated_age' =>
                        $record['estimated_age'],
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

                $verified = $record['status']
                    !== VerificationStatus::PENDING_ARRIVAL;

                $completed = $record['status']
                    === VerificationStatus::REJECTED;

                $intake = ServiceIntake::query()->updateOrCreate(
                    [
                        'freezer_id' => $freezer->id,
                        'received_at' => $record['received_at'],
                    ],
                    [
                        'intake_code' =>
                        'TMP-' . bin2hex(random_bytes(8)),
                        'complaint_note' =>
                        $record['complaint_note'],
                        'condition_note' =>
                        $record['condition_note'],
                        'status_verifikasi' =>
                        $record['status']->value,
                        'rejection_reason' =>
                        $record['rejection_reason'],
                        'received_by' => $admin->id,
                        'verified_by' =>
                        $verified ? $admin->id : null,
                        'verified_at' =>
                        $verified
                            ? $record['received_at']
                            ->copy()
                            ->addHour()
                            : null,
                        'completed_at' =>
                        $completed
                            ? $record['received_at']
                            ->copy()
                            ->addHour()
                            : null,
                    ],
                );

                $intake->forceFill([
                    'intake_code' => sprintf(
                        'IN-%05d',
                        $intake->id,
                    ),
                ])->save();
            }
        });
    }
}
