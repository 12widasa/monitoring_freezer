<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Enums\VerificationStatus;
use App\Models\Customer;
use App\Models\Freezer;
use App\Models\ServiceIntake;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceIntakeTimestampTest extends TestCase
{
    use RefreshDatabase;

    public function test_received_at_does_not_change_when_intake_is_updated(): void
    {
        $admin = $this->createUser([
            'name' => 'Administrator Timestamp',
            'role' => UserRole::ADMIN,
        ]);

        $customerUser = $this->createUser([
            'name' => 'Pelanggan Timestamp',
            'role' => UserRole::CUSTOMER,
        ]);

        $customer = Customer::create([
            'user_id' => $customerUser->id,
            'company_name' => 'PT Timestamp Test',
            'phone' => '0241234588',
            'address' => 'Jl. Timestamp No. 1',
        ]);

        $freezer = Freezer::create([
            'customer_id' => $customer->id,
            'brand' => 'Modena',
            'model' => 'MD-320',
            'serial_number' => 'SERIAL-TIMESTAMP-001',
            'capacity_liter' => 320,
            'estimated_age' => '1-3 tahun',
            'photo_path' => null,
            'status_verifikasi' => VerificationStatus::VERIFIED,
            'rejection_reason' => null,
            'complaint_note' => 'Freezer tidak dingin.',
            'created_by' => $admin->id,
            'verified_by' => $admin->id,
            'verified_at' => now(),
        ]);

        $freezer->forceFill([
            'freezer_code' => sprintf(
                'FZ-%05d',
                $freezer->id,
            ),
        ])->save();

        $receivedAt = now()
            ->subDays(2)
            ->startOfSecond();

        $intake = ServiceIntake::create([
            'freezer_id' => $freezer->id,
            'intake_code' => 'TEMP-TIMESTAMP',
            'complaint_note' => 'Freezer tidak dingin.',
            'condition_note' => null,
            'status_verifikasi' =>
            VerificationStatus::PENDING_ARRIVAL,
            'rejection_reason' => null,
            'received_by' => $admin->id,
            'received_at' => $receivedAt,
            'verified_by' => null,
            'verified_at' => null,
            'completed_at' => null,
        ]);

        $intake->forceFill([
            'intake_code' => sprintf(
                'IN-%05d',
                $intake->id,
            ),
        ])->save();

        $intake->update([
            'condition_note' =>
            'Kondisi fisik sudah diperiksa.',
        ]);

        $this->assertSame(
            $receivedAt->format('Y-m-d H:i:s'),
            $intake
                ->refresh()
                ->received_at
                ->format('Y-m-d H:i:s'),
        );
    }

    /**
     * @param array<string, mixed> $overrides
     */
    private function createUser(
        array $overrides = [],
    ): User {
        return User::create([
            'name' => 'User Timestamp Test',
            'username' =>
            'timestamp_' . bin2hex(random_bytes(4)),
            'email' =>
            bin2hex(random_bytes(4)) . '@example.com',
            'phone' =>
            '08' . random_int(1000000000, 9999999999),
            'password' => 'password',
            'role' => UserRole::ADMIN,
            'is_active' => true,
            ...$overrides,
        ]);
    }
}
