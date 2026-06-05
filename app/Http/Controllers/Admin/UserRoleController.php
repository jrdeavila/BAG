<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserRoleController extends Controller
{
    private const ASSIGNABLE_ROLES = ['activity-user', 'activity-manager'];

    public function index(Request $request)
    {
        $search = trim((string) $request->get('q', ''));

        $users = User::query()
            ->withActiveEmployee()
            ->with(['employee:id,nombres,apellidos,noDocumento,estado'])
            ->when($search !== '', function ($query) use ($search) {
                $like = '%' . $search . '%';
                $query->where(function ($q) use ($like) {
                    $q->where('correo', 'like', $like)
                      ->orWhereHas('employee', function ($eq) use ($like) {
                          $eq->where('nombres', 'like', $like)
                             ->orWhere('apellidos', 'like', $like)
                             ->orWhere('noDocumento', 'like', $like);
                      });
                });
            })
            ->orderBy('correo')
            ->paginate(25)
            ->withQueryString();

        $userRoles = $this->loadAssignableRoles($users->getCollection()->pluck('id')->all());

        return view('pages.admin.user-roles.index', [
            'users' => $users,
            'userRoles' => $userRoles,
            'assignableRoles' => self::ASSIGNABLE_ROLES,
            'search' => $search,
        ]);
    }

    public function toggle(Request $request, User $user)
    {
        $data = $request->validate([
            'role' => 'required|string|in:' . implode(',', self::ASSIGNABLE_ROLES),
            'action' => 'required|string|in:assign,revoke',
        ]);

        try {
            if ($data['action'] === 'assign') {
                $user->assignRole($data['role']);
                $message = "Rol {$data['role']} asignado a {$user->correo}.";
            } else {
                $user->removeRole($data['role']);
                $message = "Rol {$data['role']} retirado de {$user->correo}.";
            }
            return redirect()->back()->with('success', $message);
        } catch (\Throwable $e) {
            Log::error($e);
            return redirect()->back()->with('warning', 'No se pudo actualizar el rol.');
        }
    }

    private function loadAssignableRoles(array $userIds): array
    {
        if (empty($userIds)) {
            return [];
        }
        $map = [];
        $users = User::query()->whereIn('id', $userIds)->get();
        foreach ($users as $u) {
            $map[$u->id] = $u->getRoleNames()
                ->intersect(self::ASSIGNABLE_ROLES)
                ->values()
                ->all();
        }
        return $map;
    }
}
