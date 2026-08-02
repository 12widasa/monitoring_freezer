<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Users\CreateUserAction;
use App\Actions\Users\ToggleUserStatusAction;
use App\Actions\Users\UpdateUserAction;
use App\Enums\RepairStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\RepairLog;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $perPage = (int) $request->integer('per_page', 10);

        if (!in_array($perPage, [10, 25, 50], true)) {
            $perPage = 10;
        }

        $managedRoles = [
            UserRole::TECHNICIAN->value,
            UserRole::CUSTOMER->value,
        ];

        $selectedRole = $request->string('role')->toString();

        if (!in_array($selectedRole, $managedRoles, true)) {
            $selectedRole = null;
        }

        $search = trim($request->string('search')->toString());
        $selectedStatus = $request->string('status')->toString();
        $selectedSort = $request->string('sort', 'newest')->toString();

        if (!in_array($selectedStatus, ['active', 'inactive'], true)) {
            $selectedStatus = null;
        }

        if (!in_array($selectedSort, ['newest', 'oldest', 'name_asc', 'name_desc'], true)) {
            $selectedSort = 'newest';
        }

        $technicianCount = User::query()
            ->where('role', UserRole::TECHNICIAN->value)
            ->count();

        $customerCount = User::query()
            ->where('role', UserRole::CUSTOMER->value)
            ->count();

        $totalUserCount = $technicianCount + $customerCount;

        $inactiveUserCount = User::query()
            ->whereIn('role', $managedRoles)
            ->where('is_active', false)
            ->count();

        $users = User::query()
            ->whereIn('role', $managedRoles)
            ->when(
                $selectedRole !== null,
                fn($query) => $query->where('role', $selectedRole),
            )
            ->when(
                $search !== '',
                function ($query) use ($search): void {
                    $query->where(function ($query) use ($search): void {
                        $query
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('username', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%")
                            ->orWhereHas('customer', function ($query) use ($search): void {
                                $query->where('company_name', 'like', "%{$search}%");
                            });
                    });
                },
            )
            ->when(
                $selectedStatus === 'active',
                fn($query) => $query->where('is_active', true),
            )
            ->when(
                $selectedStatus === 'inactive',
                fn($query) => $query->where('is_active', false),
            )
            ->with([
                'customer' => function ($query): void {
                    $query
                        ->withCount('freezers')
                        ->withCount([
                            'repairs as active_repairs_count' => function ($query): void {
                                $query->whereIn('status', [
                                    RepairStatus::QUEUED->value,
                                    RepairStatus::INSPECTING->value,
                                    RepairStatus::REPAIRING->value,
                                ]);
                            },
                        ]);
                },
            ])->withCount([
                'assignedRepairs as active_repairs_count' => function ($query): void {
                    $query->whereIn('status', [
                        RepairStatus::QUEUED->value,
                        RepairStatus::INSPECTING->value,
                        RepairStatus::REPAIRING->value,
                    ]);
                },
                'assignedRepairs as completed_repairs_count' => function ($query): void {
                    $query->where(
                        'status',
                        RepairStatus::COMPLETED->value,
                    );
                },
            ])
            ->when(
                $selectedSort === 'newest',
                fn($query) => $query->latest(),
            )
            ->when(
                $selectedSort === 'oldest',
                fn($query) => $query->oldest(),
            )
            ->when(
                $selectedSort === 'name_asc',
                fn($query) => $query->orderBy('name'),
            )
            ->when(
                $selectedSort === 'name_desc',
                fn($query) => $query->orderByDesc('name'),
            )
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.users.index', compact(
            'users',
            'perPage',
            'search',
            'selectedRole',
            'selectedStatus',
            'selectedSort',
            'totalUserCount',
            'technicianCount',
            'customerCount',
            'inactiveUserCount',
        ));
    }

    public function show(User $user): View
    {
        abort_unless(
            in_array($user->role, [
                UserRole::TECHNICIAN,
                UserRole::CUSTOMER,
            ], true),
            404,
        );

        $activeStatuses = [
            RepairStatus::QUEUED->value,
            RepairStatus::INSPECTING->value,
            RepairStatus::REPAIRING->value,
        ];

        if ($user->role === UserRole::TECHNICIAN) {
            $user->loadCount([
                'assignedRepairs as active_repairs_count' => function ($query) use ($activeStatuses): void {
                    $query->whereIn('status', $activeStatuses);
                },
                'assignedRepairs as completed_repairs_count' => function ($query): void {
                    $query->where(
                        'status',
                        RepairStatus::COMPLETED->value,
                    );
                },
            ]);

            $activities = RepairLog::query()
                ->where('updated_by', $user->id)
                ->with([
                    'repair.freezer:id,serial_number,brand,model',
                ])
                ->latest('created_at')
                ->limit(3)
                ->get();
        } else {
            $user->load([
                'customer' => function ($query) use ($activeStatuses): void {
                    $query
                        ->withCount('freezers')
                        ->withCount([
                            'repairs as active_repairs_count' => function ($query) use ($activeStatuses): void {
                                $query->whereIn('status', $activeStatuses);
                            },
                            'repairs as completed_repairs_count' => function ($query): void {
                                $query->where(
                                    'status',
                                    RepairStatus::COMPLETED->value,
                                );
                            },
                        ]);
                },
            ]);

            $customerId = $user->customer?->id;

            $activities = RepairLog::query()
                ->when(
                    $customerId !== null,
                    function ($query) use ($customerId): void {
                        $query->whereHas(
                            'repair.freezer',
                            fn($query) => $query->where(
                                'customer_id',
                                $customerId,
                            ),
                        );
                    },
                    fn($query) => $query->whereRaw('1 = 0'),
                )
                ->with([
                    'repair.freezer:id,serial_number,brand,model',
                    'updater:id,name,role',
                ])
                ->latest('created_at')
                ->limit(3)
                ->get();
        }

        return view('admin.users.show', compact(
            'user',
            'activities',
        ));
    }

    public function store(
        StoreUserRequest $request,
        CreateUserAction $createUser
    ): JsonResponse|RedirectResponse {
        $user = $createUser->execute($request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Pengguna berhasil ditambahkan.',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role' => $user->role->value,
                ],
                'redirect_url' => route('admin.users.index'),
            ], 201);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function update(
        UpdateUserRequest $request,
        User $user,
        UpdateUserAction $updateUser
    ): JsonResponse|RedirectResponse {
        $updatedUser = $updateUser->execute(
            $user,
            $request->validated(),
        );

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Pengguna berhasil diperbarui.',
                'user' => [
                    'id' => $updatedUser->id,
                    'name' => $updatedUser->name,
                    'role' => $updatedUser->role->value,
                ],
                'redirect_url' => route('admin.users.index'),
            ]);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function updateStatus(
        User $user,
        ToggleUserStatusAction $toggleUserStatus
    ): RedirectResponse {
        $updatedUser = $toggleUserStatus->execute($user);

        $message = $updatedUser->is_active
            ? 'Akun pengguna berhasil diaktifkan.'
            : 'Akun pengguna berhasil dinonaktifkan.';

        return redirect()
            ->back()
            ->with('success', $message);
    }
}
