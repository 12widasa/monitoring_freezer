<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Models\Customer;
use App\Models\Freezer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UpdateFreezerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Customer $customer;

    private Freezer $freezer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Administrator Test',
            'username' => 'admin_update_freezer',
            'email' => 'admin.update.freezer@example.com',
            'phone' => '081234567890',
            'password' => 'admin-password',
            'role' => UserRole::ADMIN,
            'is_active' => true,
        ]);

        $customerUser = User::create([
            'name' => 'Pelanggan Freezer',
            'username' => 'customer_update_freezer',
            'email' => 'customer.update.freezer@example.com',
            'phone' => '081234567891',
            'password' => 'customer-password',
            'role' => UserRole::CUSTOMER,
            'is_active' => true,
        ]);

        $this->customer = Customer::create([
            'user_id' => $customerUser->id,
            'company_name' => 'PT Pelanggan Freezer',
            'phone' => '0241234567',
            'address' => 'Jl. Pengujian No. 1',
        ]);

        $this->freezer = $this->createFreezer([
            'customer_id' => $this->customer->id,
            'brand' => 'Modena',
            'model' => 'MD-320',
            'serial_number' => 'SERIAL-UPDATE-001',
            'capacity_liter' => 320,
            'estimated_age' => '1-3 tahun',
        ]);
    }

    public function test_admin_can_update_a_freezer(): void
    {
        $newCustomer = $this->createCustomer(
            username: 'new_customer',
            email: 'new.customer@example.com',
            companyName: 'PT Pemilik Baru',
        );

        $response = $this
            ->actingAs($this->admin)
            ->patch(
                route('admin.freezers.update', $this->freezer),
                [
                    'customer_id' => $newCustomer->id,
                    'brand' => 'Polytron',
                    'model' => 'SCN-200',
                    'serial_number' => 'SERIAL-UPDATE-002',
                    'capacity_liter' => 200,
                    'estimated_age' => '>3 tahun',

                    // Field legacy ini sengaja dikirim untuk memastikan
                    // flow update freezer tidak mengubah keluhan intake.
                    'complaint_note' => 'Keluhan sudah diperbarui.',
                ],
            );

        $response
            ->assertRedirect(route('admin.freezers.index'))
            ->assertSessionHas(
                'success',
                'Freezer berhasil diperbarui.',
            )
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('freezers', [
            'id' => $this->freezer->id,
            'customer_id' => $newCustomer->id,
            'brand' => 'Polytron',
            'model' => 'SCN-200',
            'serial_number' => 'SERIAL-UPDATE-002',
            'capacity_liter' => 200,
            'estimated_age' => '>3 tahun',
        ]);
    }

    public function test_freezer_can_keep_its_existing_serial_number(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->patch(
                route('admin.freezers.update', $this->freezer),
                $this->validPayload([
                    'brand' => 'Modena Updated',
                    'serial_number' => 'SERIAL-UPDATE-001',
                ]),
            );

        $response
            ->assertRedirect(route('admin.freezers.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('freezers', [
            'id' => $this->freezer->id,
            'brand' => 'Modena Updated',
            'serial_number' => 'SERIAL-UPDATE-001',
        ]);
    }

    public function test_serial_number_used_by_another_freezer_is_rejected(): void
    {
        $otherFreezer = $this->createFreezer([
            'customer_id' => $this->customer->id,
            'brand' => 'GEA',
            'model' => 'AB-506',
            'serial_number' => 'SERIAL-OTHER-001',
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->from(route('admin.freezers.index'))
            ->patch(
                route('admin.freezers.update', $this->freezer),
                $this->validPayload([
                    'serial_number' => $otherFreezer->serial_number,
                ]),
            );

        $response
            ->assertRedirect(route('admin.freezers.index'))
            ->assertSessionHasErrors(
                ['serial_number'],
                null,
                'updateFreezer',
            );

        $this->assertDatabaseHas('freezers', [
            'id' => $this->freezer->id,
            'serial_number' => 'SERIAL-UPDATE-001',
        ]);
    }

    public function test_serial_number_can_be_cleared(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->patch(
                route('admin.freezers.update', $this->freezer),
                $this->validPayload([
                    'serial_number' => '',
                ]),
            );

        $response
            ->assertRedirect(route('admin.freezers.index'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('freezers', [
            'id' => $this->freezer->id,
            'serial_number' => null,
        ]);
    }

    public function test_required_fields_are_validated(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->from(route('admin.freezers.index'))
            ->patch(
                route('admin.freezers.update', $this->freezer),
                [],
            );

        $response
            ->assertRedirect(route('admin.freezers.index'))
            ->assertSessionHasErrors(
                [
                    'customer_id',
                    'brand',
                    'model',
                ],
                null,
                'updateFreezer',
            );

        $this->assertDatabaseHas('freezers', [
            'id' => $this->freezer->id,
            'brand' => 'Modena',
            'model' => 'MD-320',
        ]);
    }

    public function test_existing_photo_is_preserved_when_no_new_photo_is_uploaded(): void
    {
        Storage::fake('public');

        $oldPhotoPath = 'freezers/old-freezer.jpg';

        Storage::disk('public')->put(
            $oldPhotoPath,
            'old-photo-content',
        );

        $this->freezer->forceFill([
            'photo_path' => $oldPhotoPath,
        ])->save();

        $response = $this
            ->actingAs($this->admin)
            ->patch(
                route('admin.freezers.update', $this->freezer),
                $this->validPayload([
                    'brand' => 'Modena Baru',
                ]),
            );

        $response
            ->assertRedirect(route('admin.freezers.index'))
            ->assertSessionHasNoErrors();

        $this->freezer->refresh();

        $this->assertSame(
            $oldPhotoPath,
            $this->freezer->photo_path,
        );

        Storage::disk('public')->assertExists(
            $oldPhotoPath,
        );
    }

    public function test_new_photo_replaces_and_deletes_the_old_photo(): void
    {
        Storage::fake('public');

        $oldPhotoPath = 'freezers/old-freezer.jpg';

        Storage::disk('public')->put(
            $oldPhotoPath,
            'old-photo-content',
        );

        $this->freezer->forceFill([
            'photo_path' => $oldPhotoPath,
        ])->save();

        $newPhoto = UploadedFile::fake()->image(
            'new-freezer.webp',
            1200,
            800,
        );

        $response = $this
            ->actingAs($this->admin)
            ->patch(
                route('admin.freezers.update', $this->freezer),
                $this->validPayload([
                    'photo' => $newPhoto,
                ]),
            );

        $response
            ->assertRedirect(route('admin.freezers.index'))
            ->assertSessionHasNoErrors();

        $this->freezer->refresh();

        $this->assertNotNull($this->freezer->photo_path);

        $this->assertNotSame(
            $oldPhotoPath,
            $this->freezer->photo_path,
        );

        Storage::disk('public')->assertExists(
            $this->freezer->photo_path,
        );

        Storage::disk('public')->assertMissing(
            $oldPhotoPath,
        );
    }

    public function test_non_image_file_is_rejected(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create(
            'freezer.pdf',
            100,
            'application/pdf',
        );

        $response = $this
            ->actingAs($this->admin)
            ->from(route('admin.freezers.index'))
            ->patch(
                route('admin.freezers.update', $this->freezer),
                $this->validPayload([
                    'photo' => $file,
                ]),
            );

        $response
            ->assertRedirect(route('admin.freezers.index'))
            ->assertSessionHasErrors(
                ['photo'],
                null,
                'updateFreezer',
            );

        $this->assertDatabaseHas('freezers', [
            'id' => $this->freezer->id,
            'photo_path' => null,
        ]);
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
            ->patch(
                route('admin.freezers.update', $this->freezer),
                $this->validPayload([
                    'photo' => $photo,
                ]),
            );

        $response
            ->assertRedirect(route('admin.freezers.index'))
            ->assertSessionHasErrors(
                ['photo'],
                null,
                'updateFreezer',
            );

        $this->assertDatabaseHas('freezers', [
            'id' => $this->freezer->id,
            'photo_path' => null,
        ]);
    }

    public function test_protected_fields_cannot_be_changed_from_the_update_form(): void
    {
        $originalFreezerCode = $this->freezer->freezer_code;
        $originalCreatedBy = $this->freezer->created_by;

        $response = $this
            ->actingAs($this->admin)
            ->patch(
                route('admin.freezers.update', $this->freezer),
                [
                    ...$this->validPayload([
                        'brand' => 'Updated Brand',
                    ]),
                    'freezer_code' => 'FZ-99999',
                    'created_by' => 99999,
                ],
            );

        $response
            ->assertRedirect(route('admin.freezers.index'))
            ->assertSessionHasNoErrors();

        $this->freezer->refresh();

        $this->assertSame(
            $originalFreezerCode,
            $this->freezer->freezer_code,
        );

        $this->assertSame(
            $originalCreatedBy,
            $this->freezer->created_by,
        );

        $this->assertSame(
            'Updated Brand',
            $this->freezer->brand,
        );
    }

    public function test_filter_query_is_preserved_after_update(): void
    {
        $query = [
            'search' => 'modena',
            'repair_status' => 'queued',
            'verification_status' => 'pending_arrival',
            'sort' => 'updated',
            'per_page' => 25,
            'page' => 2,
        ];

        $response = $this
            ->actingAs($this->admin)
            ->patch(
                route(
                    'admin.freezers.update',
                    [
                        'freezer' => $this->freezer,
                        ...$query,
                    ],
                ),
                $this->validPayload([
                    'brand' => 'Modena Filtered',
                ]),
            );

        $response->assertRedirect(
            route('admin.freezers.index', $query),
        );

        $this->assertDatabaseHas('freezers', [
            'id' => $this->freezer->id,
            'brand' => 'Modena Filtered',
        ]);
    }

    public function test_guest_cannot_update_a_freezer(): void
    {
        $response = $this->patch(
            route('admin.freezers.update', $this->freezer),
            $this->validPayload(),
        );

        $response->assertRedirect(route('login'));

        $this->assertDatabaseHas('freezers', [
            'id' => $this->freezer->id,
            'brand' => 'Modena',
            'model' => 'MD-320',
        ]);
    }

    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return [
            'customer_id' => $this->customer->id,
            'brand' => 'Modena',
            'model' => 'MD-320',
            'serial_number' => 'SERIAL-UPDATE-001',
            'capacity_liter' => 320,
            'estimated_age' => '1-3 tahun',
            ...$overrides,
        ];
    }

    /**
     * @param array<string, mixed> $overrides
     */
    private function createFreezer(array $overrides = []): Freezer
    {
        $freezer = Freezer::create([
            'customer_id' => $this->customer->id,
            'brand' => 'Modena',
            'model' => 'MD-320',
            'serial_number' => null,
            'capacity_liter' => null,
            'estimated_age' => null,
            'photo_path' => null,
            'created_by' => $this->admin->id,
            ...$overrides,
        ]);

        $freezer->forceFill([
            'freezer_code' => sprintf(
                'FZ-%05d',
                $freezer->id,
            ),
        ])->save();

        return $freezer->refresh();
    }

    private function createCustomer(
        string $username,
        string $email,
        string $companyName,
    ): Customer {
        $user = User::create([
            'name' => $companyName,
            'username' => $username,
            'email' => $email,
            'phone' => '081234567899',
            'password' => 'customer-password',
            'role' => UserRole::CUSTOMER,
            'is_active' => true,
        ]);

        return Customer::create([
            'user_id' => $user->id,
            'company_name' => $companyName,
            'phone' => '0247654321',
            'address' => 'Jl. Customer Baru No. 2',
        ]);
    }
}
