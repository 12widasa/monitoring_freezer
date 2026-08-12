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

class VerifyServiceIntakeTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Customer $customer;

    private Freezer $freezer;

    private ServiceIntake $intake;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Administrator Verifikasi',
            'username' => 'admin_verification_test',
            'email' => 'admin.verification@example.com',
            'phone' => '081234567890',
            'password' => 'admin-password',
            'role' => UserRole::ADMIN,
            'is_active' => true,
        ]);

        $customerUser = User::create([
            'name' => 'Pelanggan Verifikasi',
            'username' => 'customer_verification_test',
            'email' => 'customer.verification@example.com',
            'phone' => '081234567891',
            'password' => 'customer-password',
            'role' => UserRole::CUSTOMER,
            'is_active' => true,
        ]);

        $this->customer = Customer::create([
            'user_id' => $customerUser->id,
            'company_name' => 'PT Pelanggan Verifikasi',
            'phone' => '0241234567',
            'address' => 'Jl. Pengujian Verifikasi No. 1',
        ]);

        $this->freezer = $this->createFreezer();

        $this->intake = $this->createIntake(
            freezer: $this->freezer,
        );
    }

    public function test_admin_can_verify_a_pending_service_intake(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->from(route('admin.freezers.show', $this->freezer))
            ->patch(
                route(
                    'admin.freezers.verification.update',
                    $this->freezer,
                ),
                [
                    'verification_freezer_id' => $this->freezer->id,
                    'verification_result' =>
                    VerificationStatus::VERIFIED->value,
                    'rejection_reason' => '',
                ],
            );

        $response
            ->assertRedirect(
                route('admin.freezers.show', $this->freezer),
            )
            ->assertSessionHas(
                'success',
                'Freezer berhasil diverifikasi.',
            )
            ->assertSessionHasNoErrors();

        $this->intake->refresh();

        $this->assertSame(
            VerificationStatus::VERIFIED,
            $this->intake->status_verifikasi,
        );

        $this->assertSame(
            $this->admin->id,
            $this->intake->verified_by,
        );

        $this->assertNotNull(
            $this->intake->verified_at,
        );

        $this->assertNull(
            $this->intake->rejection_reason,
        );

        $this->assertNull(
            $this->intake->completed_at,
        );
    }

    public function test_admin_can_reject_a_pending_service_intake(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->from(route('admin.freezers.index'))
            ->patch(
                route(
                    'admin.freezers.verification.update',
                    $this->freezer,
                ),
                [
                    'verification_freezer_id' => $this->freezer->id,
                    'verification_result' =>
                    VerificationStatus::REJECTED->value,
                    'rejection_reason' =>
                    'Kondisi fisik unit tidak sesuai dengan data.',
                ],
            );

        $response
            ->assertRedirect(route('admin.freezers.index'))
            ->assertSessionHas(
                'success',
                'Verifikasi freezer ditolak.',
            )
            ->assertSessionHasNoErrors();

        $this->intake->refresh();

        $this->assertSame(
            VerificationStatus::REJECTED,
            $this->intake->status_verifikasi,
        );

        $this->assertSame(
            'Kondisi fisik unit tidak sesuai dengan data.',
            $this->intake->rejection_reason,
        );

        $this->assertSame(
            $this->admin->id,
            $this->intake->verified_by,
        );

        $this->assertNotNull(
            $this->intake->verified_at,
        );

        $this->assertNotNull(
            $this->intake->completed_at,
        );
    }

    public function test_rejection_reason_is_required_when_intake_is_rejected(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->from(route('admin.freezers.show', $this->freezer))
            ->patch(
                route(
                    'admin.freezers.verification.update',
                    $this->freezer,
                ),
                [
                    'verification_freezer_id' => $this->freezer->id,
                    'verification_result' =>
                    VerificationStatus::REJECTED->value,
                    'rejection_reason' => '',
                ],
            );

        $response
            ->assertRedirect(
                route('admin.freezers.show', $this->freezer),
            )
            ->assertSessionHasErrors(
                ['rejection_reason'],
                null,
                'verifyServiceIntake',
            );

        $this->intake->refresh();

        $this->assertSame(
            VerificationStatus::PENDING_ARRIVAL,
            $this->intake->status_verifikasi,
        );

        $this->assertNull(
            $this->intake->verified_by,
        );

        $this->assertNull(
            $this->intake->verified_at,
        );

        $this->assertNull(
            $this->intake->completed_at,
        );
    }

    public function test_rejection_reason_is_not_required_when_intake_is_verified(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->patch(
                route(
                    'admin.freezers.verification.update',
                    $this->freezer,
                ),
                [
                    'verification_freezer_id' => $this->freezer->id,
                    'verification_result' =>
                    VerificationStatus::VERIFIED->value,
                ],
            );

        $response
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('service_intakes', [
            'id' => $this->intake->id,
            'status_verifikasi' =>
            VerificationStatus::VERIFIED->value,
            'rejection_reason' => null,
            'verified_by' => $this->admin->id,
        ]);
    }

    public function test_verification_result_is_required(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->from(route('admin.freezers.show', $this->freezer))
            ->patch(
                route(
                    'admin.freezers.verification.update',
                    $this->freezer,
                ),
                [
                    'verification_freezer_id' => $this->freezer->id,
                    'verification_result' => '',
                ],
            );

        $response
            ->assertRedirect(
                route('admin.freezers.show', $this->freezer),
            )
            ->assertSessionHasErrors(
                ['verification_result'],
                null,
                'verifyServiceIntake',
            );

        $this->assertDatabaseHas('service_intakes', [
            'id' => $this->intake->id,
            'status_verifikasi' =>
            VerificationStatus::PENDING_ARRIVAL->value,
            'verified_by' => null,
            'verified_at' => null,
        ]);
    }

    public function test_invalid_verification_result_is_rejected(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->from(route('admin.freezers.show', $this->freezer))
            ->patch(
                route(
                    'admin.freezers.verification.update',
                    $this->freezer,
                ),
                [
                    'verification_freezer_id' => $this->freezer->id,
                    'verification_result' => 'invalid-status',
                ],
            );

        $response
            ->assertRedirect(
                route('admin.freezers.show', $this->freezer),
            )
            ->assertSessionHasErrors(
                ['verification_result'],
                null,
                'verifyServiceIntake',
            );

        $this->assertDatabaseHas('service_intakes', [
            'id' => $this->intake->id,
            'status_verifikasi' =>
            VerificationStatus::PENDING_ARRIVAL->value,
        ]);
    }

    public function test_freezer_id_in_form_must_match_route_freezer(): void
    {
        $otherFreezer = $this->createFreezer([
            'brand' => 'Polytron',
            'model' => 'SCN-200',
            'serial_number' => 'OTHER-SERIAL-001',
        ]);

        $this->createIntake(
            freezer: $otherFreezer,
        );

        $response = $this
            ->actingAs($this->admin)
            ->from(route('admin.freezers.show', $this->freezer))
            ->patch(
                route(
                    'admin.freezers.verification.update',
                    $this->freezer,
                ),
                [
                    'verification_freezer_id' => $otherFreezer->id,
                    'verification_result' =>
                    VerificationStatus::VERIFIED->value,
                ],
            );

        $response
            ->assertRedirect(
                route('admin.freezers.show', $this->freezer),
            )
            ->assertSessionHasErrors(
                ['verification_result'],
                null,
                'verifyServiceIntake',
            );

        $this->assertDatabaseHas('service_intakes', [
            'id' => $this->intake->id,
            'status_verifikasi' =>
            VerificationStatus::PENDING_ARRIVAL->value,
        ]);
    }

    public function test_service_intake_cannot_be_verified_twice(): void
    {
        $this->intake->update([
            'status_verifikasi' =>
            VerificationStatus::VERIFIED,
            'verified_by' => $this->admin->id,
            'verified_at' => now(),
        ]);

        $originalVerifiedAt = $this->intake
            ->fresh()
            ->verified_at;

        $response = $this
            ->actingAs($this->admin)
            ->from(route('admin.freezers.show', $this->freezer))
            ->patch(
                route(
                    'admin.freezers.verification.update',
                    $this->freezer,
                ),
                [
                    'verification_freezer_id' => $this->freezer->id,
                    'verification_result' =>
                    VerificationStatus::REJECTED->value,
                    'rejection_reason' =>
                    'Percobaan mengubah hasil verifikasi.',
                ],
            );

        $response
            ->assertRedirect(
                route('admin.freezers.show', $this->freezer),
            )
            ->assertSessionHasErrors(
                ['verification_result'],
                null,
                'verifyServiceIntake',
            );

        $this->intake->refresh();

        $this->assertSame(
            VerificationStatus::VERIFIED,
            $this->intake->status_verifikasi,
        );

        $this->assertNull(
            $this->intake->rejection_reason,
        );

        $this->assertTrue(
            $this->intake->verified_at->equalTo(
                $originalVerifiedAt,
            ),
        );
    }

    public function test_freezer_without_service_intake_cannot_be_verified(): void
    {
        $this->intake->delete();

        $response = $this
            ->actingAs($this->admin)
            ->from(route('admin.freezers.show', $this->freezer))
            ->patch(
                route(
                    'admin.freezers.verification.update',
                    $this->freezer,
                ),
                [
                    'verification_freezer_id' => $this->freezer->id,
                    'verification_result' =>
                    VerificationStatus::VERIFIED->value,
                ],
            );

        $response
            ->assertRedirect(
                route('admin.freezers.show', $this->freezer),
            )
            ->assertSessionHasErrors(
                ['verification_result'],
                null,
                'verifyServiceIntake',
            );

        $this->assertDatabaseMissing('service_intakes', [
            'freezer_id' => $this->freezer->id,
        ]);
    }

    public function test_verification_redirects_back_to_the_filtered_index(): void
    {
        $indexUrl = route('admin.freezers.index', [
            'search' => 'modena',
            'verification_status' =>
            VerificationStatus::PENDING_ARRIVAL->value,
            'sort' => 'latest',
            'per_page' => 25,
            'page' => 2,
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->from($indexUrl)
            ->patch(
                route(
                    'admin.freezers.verification.update',
                    $this->freezer,
                ),
                [
                    'verification_freezer_id' => $this->freezer->id,
                    'verification_result' =>
                    VerificationStatus::VERIFIED->value,
                ],
            );

        $response->assertRedirect($indexUrl);
    }

    public function test_guest_cannot_verify_a_service_intake(): void
    {
        $response = $this->patch(
            route(
                'admin.freezers.verification.update',
                $this->freezer,
            ),
            [
                'verification_freezer_id' => $this->freezer->id,
                'verification_result' =>
                VerificationStatus::VERIFIED->value,
            ],
        );

        $response->assertRedirect(route('login'));

        $this->assertDatabaseHas('service_intakes', [
            'id' => $this->intake->id,
            'status_verifikasi' =>
            VerificationStatus::PENDING_ARRIVAL->value,
            'verified_by' => null,
            'verified_at' => null,
        ]);
    }

    public function test_condition_note_is_saved_when_intake_is_verified(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->patch(
                route(
                    'admin.freezers.verification.update',
                    $this->freezer,
                ),
                [
                    'verification_freezer_id' => $this->freezer->id,
                    'verification_result' =>
                    VerificationStatus::VERIFIED->value,
                    'condition_note' =>
                    '  Unit kotor dan terdapat penyok pada sisi kanan.  ',
                ],
            );

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('service_intakes', [
            'id' => $this->intake->id,
            'status_verifikasi' =>
            VerificationStatus::VERIFIED->value,
            'condition_note' =>
            'Unit kotor dan terdapat penyok pada sisi kanan.',
        ]);
    }

    public function test_condition_note_is_saved_when_intake_is_rejected(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->patch(
                route(
                    'admin.freezers.verification.update',
                    $this->freezer,
                ),
                [
                    'verification_freezer_id' => $this->freezer->id,
                    'verification_result' =>
                    VerificationStatus::REJECTED->value,
                    'condition_note' =>
                    'Pintu tidak rapat dan kabel terkelupas.',
                    'rejection_reason' =>
                    'Nomor seri unit tidak sesuai dengan data.',
                ],
            );

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('service_intakes', [
            'id' => $this->intake->id,
            'status_verifikasi' =>
            VerificationStatus::REJECTED->value,
            'condition_note' =>
            'Pintu tidak rapat dan kabel terkelupas.',
            'rejection_reason' =>
            'Nomor seri unit tidak sesuai dengan data.',
        ]);
    }

    public function test_condition_note_is_optional(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->patch(
                route(
                    'admin.freezers.verification.update',
                    $this->freezer,
                ),
                [
                    'verification_freezer_id' => $this->freezer->id,
                    'verification_result' =>
                    VerificationStatus::VERIFIED->value,
                    'condition_note' => '',
                ],
            );

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('service_intakes', [
            'id' => $this->intake->id,
            'status_verifikasi' =>
            VerificationStatus::VERIFIED->value,
            'condition_note' => null,
        ]);
    }

    public function test_condition_note_cannot_exceed_five_thousand_characters(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->from(
                route(
                    'admin.freezers.show',
                    $this->freezer,
                ),
            )
            ->patch(
                route(
                    'admin.freezers.verification.update',
                    $this->freezer,
                ),
                [
                    'verification_freezer_id' => $this->freezer->id,
                    'verification_result' =>
                    VerificationStatus::VERIFIED->value,
                    'condition_note' => str_repeat('A', 5001),
                ],
            );

        $response
            ->assertRedirect(
                route(
                    'admin.freezers.show',
                    $this->freezer,
                ),
            )
            ->assertSessionHasErrors(
                ['condition_note'],
                null,
                'verifyServiceIntake',
            );

        $this->intake->refresh();

        $this->assertSame(
            VerificationStatus::PENDING_ARRIVAL,
            $this->intake->status_verifikasi,
        );

        $this->assertNull(
            $this->intake->condition_note,
        );
    }


    /**
     * @param array<string, mixed> $overrides
     */
    private function createFreezer(
        array $overrides = [],
    ): Freezer {
        $freezer = Freezer::create([
            'customer_id' => $this->customer->id,
            'brand' => 'Modena',
            'model' => 'MD-320',
            'serial_number' => 'SERIAL-VERIFY-001',
            'capacity_liter' => 320,
            'estimated_age' => '1-3 tahun',
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

    private function createIntake(
        Freezer $freezer,
    ): ServiceIntake {
        $intake = ServiceIntake::create([
            'freezer_id' => $freezer->id,
            'intake_code' => sprintf(
                'TEMP-%d-%s',
                $freezer->id,
                bin2hex(random_bytes(4)),
            ),
            'complaint_note' => 'Freezer tidak dingin.',
            'condition_note' => null,
            'status_verifikasi' =>
            VerificationStatus::PENDING_ARRIVAL,
            'rejection_reason' => null,
            'received_by' => $this->admin->id,
            'received_at' => now(),
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

        return $intake->refresh();
    }
}
