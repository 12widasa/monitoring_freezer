<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Enums\VerificationStatus;
use App\Models\Customer;
use App\Models\Freezer;
use App\Models\ServiceIntake;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CreateFreezerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Administrator Test',
            'username' => 'admin_freezer_test',
            'email' => 'admin.freezer@example.com',
            'phone' => '081234567890',
            'password' => 'admin-password',
            'role' => UserRole::ADMIN,
            'is_active' => true,
        ]);

        $customerUser = User::create([
            'name' => 'Pelanggan Freezer Test',
            'username' => 'customer_freezer_test',
            'email' => 'customer.freezer@example.com',
            'phone' => '081234567891',
            'password' => 'customer-password',
            'role' => UserRole::CUSTOMER,
            'is_active' => true,
        ]);

        $this->customer = Customer::create([
            'user_id' => $customerUser->id,
            'company_name' => 'PT Pelanggan Freezer Test',
            'phone' => '0241234567',
            'address' => 'Jl. Pengujian No. 1',
        ]);
    }

    public function test_admin_can_create_a_freezer(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->post(route('admin.freezers.store'), [
                'customer_id' => $this->customer->id,
                'brand' => 'Modena',
                'model' => 'MD-320',
                'serial_number' => 'MDN-320-TEST-001',
                'capacity_liter' => 320,
                'estimated_age' => '1-3 tahun',
                'complaint_note' => 'Freezer tidak dapat mencapai suhu dingin.',
            ]);

        $response
            ->assertRedirect(route('admin.freezers.index'))
            ->assertSessionHas(
                'success',
                'Freezer berhasil ditambahkan.',
            );

        $freezer = Freezer::query()->sole();

        $this->assertSame(
            $this->customer->id,
            $freezer->customer_id,
        );

        $this->assertSame('Modena', $freezer->brand);
        $this->assertSame('MD-320', $freezer->model);
        $this->assertSame('MDN-320-TEST-001', $freezer->serial_number);
        $this->assertSame(320, $freezer->capacity_liter);
        $this->assertSame('1-3 tahun', $freezer->estimated_age);

        $this->assertSame(
            $this->admin->id,
            $freezer->created_by,
        );

        $intake = ServiceIntake::query()->sole();

        $this->assertSame(
            $freezer->id,
            $intake->freezer_id,
        );

        $this->assertSame(
            sprintf('IN-%05d', $intake->id),
            $intake->intake_code,
        );

        $this->assertSame(
            'Freezer tidak dapat mencapai suhu dingin.',
            $intake->complaint_note,
        );

        $this->assertSame(
            VerificationStatus::PENDING_ARRIVAL,
            $intake->status_verifikasi,
        );

        $this->assertSame(
            $this->admin->id,
            $intake->received_by,
        );

        $this->assertNotNull($intake->received_at);
        $this->assertNull($intake->verified_by);
        $this->assertNull($intake->verified_at);
        $this->assertNull($intake->completed_at);
        $this->assertNull($intake->rejection_reason);
    }

    public function test_freezer_code_is_generated_from_the_freezer_id(): void
    {
        $this
            ->actingAs($this->admin)
            ->post(route('admin.freezers.store'), [
                'customer_id' => $this->customer->id,
                'brand' => 'Polytron',
                'model' => 'SCN-200',
            ])
            ->assertRedirect(route('admin.freezers.index'));

        $freezer = Freezer::query()->sole();

        $this->assertSame(
            sprintf('FZ-%05d', $freezer->id),
            $freezer->freezer_code,
        );

        $this->assertDatabaseHas('freezers', [
            'id' => $freezer->id,
            'freezer_code' => sprintf(
                'FZ-%05d',
                $freezer->id,
            ),
        ]);
    }

    public function test_first_service_intake_is_created_for_the_new_freezer(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->post(route('admin.freezers.store'), [
                'customer_id' => $this->customer->id,
                'brand' => 'GEA',
                'model' => 'AB-506',
                'complaint_note' => 'Freezer tidak menyala.',
            ]);

        $response
            ->assertRedirect(route('admin.freezers.index'))
            ->assertSessionHasNoErrors();

        $freezer = Freezer::query()
            ->with('latestIntake')
            ->sole();

        $this->assertNotNull(
            $freezer->latestIntake,
        );

        $this->assertSame(
            $freezer->id,
            $freezer->latestIntake->freezer_id,
        );

        $this->assertSame(
            sprintf(
                'IN-%05d',
                $freezer->latestIntake->id,
            ),
            $freezer->latestIntake->intake_code,
        );

        $this->assertSame(
            'Freezer tidak menyala.',
            $freezer->latestIntake->complaint_note,
        );

        $this->assertSame(
            VerificationStatus::PENDING_ARRIVAL,
            $freezer->latestIntake->status_verifikasi,
        );

        $this->assertSame(
            $this->admin->id,
            $freezer->latestIntake->received_by,
        );

        $this->assertNull(
            $freezer->latestIntake->verified_by,
        );

        $this->assertNull(
            $freezer->latestIntake->verified_at,
        );

        $this->assertNull(
            $freezer->latestIntake->completed_at,
        );
    }

    public function test_serial_number_is_optional(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->post(route('admin.freezers.store'), [
                'customer_id' => $this->customer->id,
                'brand' => 'GEA',
                'model' => 'AB-506',
                'serial_number' => '',
            ]);

        $response
            ->assertRedirect(route('admin.freezers.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('freezers', [
            'customer_id' => $this->customer->id,
            'brand' => 'GEA',
            'model' => 'AB-506',
            'serial_number' => null,
        ]);
    }

    public function test_customer_brand_and_model_are_required(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->from(route('admin.freezers.index'))
            ->post(route('admin.freezers.store'), []);

        $response
            ->assertRedirect(route('admin.freezers.index'))
            ->assertSessionHasErrors(
                [
                    'customer_id',
                    'brand',
                    'model',
                ],
                null,
                'createFreezer',
            );

        $this->assertDatabaseCount('freezers', 0);

        $this->assertDatabaseCount('service_intakes', 0);
    }

    public function test_duplicate_serial_number_is_rejected(): void
    {
        $this
            ->actingAs($this->admin)
            ->post(route('admin.freezers.store'), [
                'customer_id' => $this->customer->id,
                'brand' => 'Modena',
                'model' => 'MD-320',
                'serial_number' => 'SERIAL-DUPLICATE-001',
            ])
            ->assertRedirect(route('admin.freezers.index'));

        $response = $this
            ->actingAs($this->admin)
            ->from(route('admin.freezers.index'))
            ->post(route('admin.freezers.store'), [
                'customer_id' => $this->customer->id,
                'brand' => 'Polytron',
                'model' => 'SCN-200',
                'serial_number' => 'SERIAL-DUPLICATE-001',
            ]);

        $response
            ->assertRedirect(route('admin.freezers.index'))
            ->assertSessionHasErrors(
                ['serial_number'],
                null,
                'createFreezer',
            );

        $this->assertDatabaseCount('freezers', 1);

        $this->assertDatabaseCount('service_intakes', 1);
    }

    public function test_admin_can_create_a_freezer_with_a_photo(): void
    {
        Storage::fake('public');

        $photo = UploadedFile::fake()->image(
            'freezer-test.jpg',
            1200,
            800,
        );

        $response = $this
            ->actingAs($this->admin)
            ->post(route('admin.freezers.store'), [
                'customer_id' => $this->customer->id,
                'brand' => 'Modena',
                'model' => 'MD-320',
                'photo' => $photo,
            ]);

        $response
            ->assertRedirect(route('admin.freezers.index'))
            ->assertSessionHasNoErrors();

        $freezer = Freezer::query()->sole();

        $this->assertNotNull($freezer->photo_path);

        $this->assertStringStartsWith(
            'freezers/',
            $freezer->photo_path,
        );

        Storage::disk('public')->assertExists(
            $freezer->photo_path,
        );
    }

    public function test_non_image_file_is_rejected_as_a_photo(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create(
            'freezer-document.pdf',
            100,
            'application/pdf',
        );

        $response = $this
            ->actingAs($this->admin)
            ->from(route('admin.freezers.index'))
            ->post(route('admin.freezers.store'), [
                'customer_id' => $this->customer->id,
                'brand' => 'Modena',
                'model' => 'MD-320',
                'photo' => $file,
            ]);

        $response
            ->assertRedirect(route('admin.freezers.index'))
            ->assertSessionHasErrors(
                ['photo'],
                null,
                'createFreezer',
            );

        $this->assertDatabaseCount('freezers', 0);

        $this->assertDatabaseCount('service_intakes', 0);

        Storage::disk('public')->assertDirectoryEmpty(
            'freezers',
        );
    }

    public function test_photo_larger_than_two_megabytes_is_rejected(): void
    {
        Storage::fake('public');

        $photo = UploadedFile::fake()
            ->image('large-freezer.jpg')
            ->size(2049);

        $response = $this
            ->actingAs($this->admin)
            ->from(route('admin.freezers.index'))
            ->post(route('admin.freezers.store'), [
                'customer_id' => $this->customer->id,
                'brand' => 'Modena',
                'model' => 'MD-320',
                'photo' => $photo,
            ]);

        $response
            ->assertRedirect(route('admin.freezers.index'))
            ->assertSessionHasErrors(
                ['photo'],
                null,
                'createFreezer',
            );

        $this->assertDatabaseCount('freezers', 0);

        $this->assertDatabaseCount('service_intakes', 0);
    }

    public function test_guest_cannot_create_a_freezer(): void
    {
        $response = $this->post(
            route('admin.freezers.store'),
            [
                'customer_id' => $this->customer->id,
                'brand' => 'Modena',
                'model' => 'MD-320',
            ],
        );

        $response->assertRedirect(route('login'));

        $this->assertDatabaseCount('freezers', 0);

        $this->assertDatabaseCount('service_intakes', 0);
    }
}
