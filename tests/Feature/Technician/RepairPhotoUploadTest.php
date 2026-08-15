<?php

namespace Tests\Feature\Technician;

use App\Enums\RepairStatus;
use App\Enums\UserRole;
use App\Models\Customer;
use App\Models\Freezer;
use App\Models\Repair;
use App\Models\ServiceIntake;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RepairPhotoUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_technician_can_upload_inspection_photos(): void
    {
        Storage::fake('public');
        [$technician, $repair] = $this->createRepair(RepairStatus::INSPECTING);
        $photos = [
            UploadedFile::fake()->image('inspection-one.jpg'),
            UploadedFile::fake()->image('inspection-two.png'),
        ];

        $response = $this->actingAs($technician)->post(
            route('technician.repairs.inspection.store', $repair),
            [
                'description' => 'Tekanan refrigeran rendah dan relay kompresor panas.',
                'photos' => $photos,
            ],
        );

        $response
            ->assertRedirect(route('technician.repairs.show', $repair))
            ->assertSessionHasNoErrors();

        $log = $repair->refresh()->logs()->latest('id')->firstOrFail();

        $this->assertSame(RepairStatus::REPAIRING, $repair->status);
        $this->assertCount(2, $log->photos);

        foreach ($log->photos as $photo) {
            Storage::disk('public')->assertExists($photo->photo_path);
        }
    }

    public function test_technician_can_upload_completion_photos(): void
    {
        Storage::fake('public');
        [$technician, $repair] = $this->createRepair(RepairStatus::REPAIRING);
        $photo = UploadedFile::fake()->image('completed.jpg');

        $response = $this->actingAs($technician)->post(
            route('technician.repairs.progress.store', $repair),
            [
                'description' => 'Kompresor telah diganti dan freezer kembali dingin.',
                'photos' => [$photo],
            ],
        );

        $response
            ->assertRedirect(route('technician.repairs.show', $repair))
            ->assertSessionHasNoErrors();

        $log = $repair->refresh()->logs()->latest('id')->firstOrFail();

        $this->assertSame(RepairStatus::COMPLETED, $repair->status);
        $this->assertCount(1, $log->photos);
        Storage::disk('public')->assertExists($log->photos->first()->photo_path);
    }

    /** @return array{User, Repair} */
    private function createRepair(RepairStatus $status): array
    {
        $admin = $this->createUser(UserRole::ADMIN);
        $technician = $this->createUser(UserRole::TECHNICIAN);
        $customerUser = $this->createUser(UserRole::CUSTOMER);
        $customer = Customer::query()->create([
            'user_id' => $customerUser->id,
            'company_name' => 'PT Pengujian Foto',
            'address' => 'Jl. Pengujian No. 1',
            'phone' => '081234567890',
        ]);
        $freezer = Freezer::query()->create([
            'customer_id' => $customer->id,
            'brand' => 'Karyatama',
            'model' => 'FR-100',
            'serial_number' => 'PHOTO-TEST-'.fake()->unique()->numerify('####'),
            'created_by' => $admin->id,
        ]);
        $intake = ServiceIntake::query()->create([
            'freezer_id' => $freezer->id,
            'intake_code' => 'IN-'.fake()->unique()->numerify('######'),
            'received_by' => $admin->id,
            'received_at' => now(),
        ]);
        $repair = Repair::query()->create([
            'freezer_id' => $freezer->id,
            'service_intake_id' => $intake->id,
            'technician_id' => $technician->id,
            'admin_id' => $admin->id,
            'status' => $status->value,
        ]);

        return [$technician, $repair];
    }

    private function createUser(UserRole $role): User
    {
        $suffix = fake()->unique()->numerify('######');

        return User::query()->create([
            'username' => "photo_user_{$suffix}",
            'email' => "photo_user_{$suffix}@example.com",
            'password' => 'password',
            'role' => $role,
            'name' => "User Foto {$suffix}",
            'phone' => "0812{$suffix}",
            'is_active' => true,
        ]);
    }
}
