<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Admin\AdminUserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function __construct(protected AdminUserRepositoryInterface $adminUserRepo) {}

    public function index(Request $request)
    {
        return response()->json($this->adminUserRepo->paginate($request->input('per_page', 20)));
    }

    public function profile()
    {
        return response()->json(['status' => 'OK', 'user' => Auth::guard('admin')->user()]);
    }

    public function updateProfile(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        if (! $admin) {
            return response()->json(['status' => 'ERROR', 'message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:admins,email,'.$admin->id,
            'phone' => 'nullable|string|max:50',
        ]);

        $this->adminUserRepo->update($admin->id, $validated);

        return response()->json(['status' => 'OK', 'message' => 'Profile updated successfully.']);
    }

    public function getByUserType($userType)
    {
        return response()->json($this->adminUserRepo->getByUserType($userType));
    }

    public function changePassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $this->adminUserRepo->update($id, ['password' => Hash::make($request->password)]);

        return response()->json(['status' => 'OK', 'message' => 'Password updated.']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|string|min:8',
            'role' => 'nullable|string',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $user = $this->adminUserRepo->create($validated);

        return response()->json(['status' => 'OK', 'data' => $user]);
    }

    public function show($id)
    {
        return response()->json(['status' => 'OK', 'data' => $this->adminUserRepo->findOrFail($id)]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:admins,email,'.$id,
            'role' => 'nullable|string',
        ]);

        $user = $this->adminUserRepo->update($id, $validated);

        return response()->json(['status' => 'OK', 'data' => $user]);
    }

    public function destroy($id)
    {
        $this->adminUserRepo->delete($id);

        return response()->json(['status' => 'OK', 'message' => 'User deleted.']);
    }
}
