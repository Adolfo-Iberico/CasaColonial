<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StaffRole;
use App\Http\Controllers\Controller;
use App\Models\Buy;
use App\Models\BuyDetails;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
        ]);
        $search = trim($validated['search'] ?? '');

        $query = User::query()
            ->orderByDesc('is_admin')
            ->orderBy('name');

        if ($search !== '') {
            $query->where(function ($query) use ($search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(12)->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'totalUsers' => User::count(),
            'search' => $search,
            'roles' => StaffRole::options(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
            'role' => ['required', 'string', 'in:'.implode(',', array_keys(StaffRole::options()))],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => $validated['role'],
            'is_admin' => $validated['role'] === StaffRole::Administrator->value,
        ]);
        $user->forceFill(['email_verified_at' => now()])->save();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'El usuario fue creado correctamente.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'string', 'in:'.implode(',', array_keys(StaffRole::options()))],
        ]);
        $newRole = $validated['role'];

        if ($user->is(auth()->user()) && $newRole !== StaffRole::Administrator->value) {
            return back()->withErrors([
                'role' => 'No puedes quitarte el acceso de administrador desde tu propia cuenta.',
            ]);
        }

        $administratorCount = User::where('is_admin', true)
            ->orWhere('role', StaffRole::Administrator->value)
            ->count();

        if ($user->isAdmin() && $newRole !== StaffRole::Administrator->value && $administratorCount <= 1) {
            return back()->withErrors([
                'role' => 'No se puede cambiar el puesto del último administrador.',
            ]);
        }

        $user->update([
            'role' => $newRole,
            'is_admin' => $newRole === StaffRole::Administrator->value,
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'El puesto del usuario fue actualizado correctamente.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->is(auth()->user())) {
            return back()->withErrors([
                'user' => 'No puedes eliminar la cuenta con la que has iniciado sesión.',
            ]);
        }

        $administratorCount = User::where('is_admin', true)
            ->orWhere('role', StaffRole::Administrator->value)
            ->count();

        if ($user->isAdmin() && $administratorCount <= 1) {
            return back()->withErrors([
                'user' => 'No se puede eliminar al último administrador.',
            ]);
        }

        DB::transaction(function () use ($user): void {
            $buyIds = Buy::where('user_id', $user->id)->pluck('id');
            BuyDetails::whereIn('buy_id', $buyIds)->delete();
            $user->delete();
        });

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'El usuario fue eliminado correctamente.');
    }
}
