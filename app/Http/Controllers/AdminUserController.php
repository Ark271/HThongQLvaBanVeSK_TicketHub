<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('id', 'desc')->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:user,organizer,admin',
        ]);

        // Không cho admin tự hạ quyền chính mình
        if (auth()->id() === $user->id && $request->role !== 'admin') {
            return back()->with('error', 'Bạn không thể tự hạ quyền admin của chính mình.');
        }

        $user->role = $request->role;
        $user->save();

        return back()->with('success', 'Cập nhật quyền tài khoản thành công.');
    }

    public function toggleActive(User $user)
    {
        // Không cho admin tự khóa chính mình
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Bạn không thể tự khóa tài khoản của chính mình.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        return back()->with('success', 'Cập nhật trạng thái tài khoản thành công.');
    }

    public function destroy(User $user)
    {
        // Không cho admin tự xóa chính mình
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Bạn không thể tự xóa tài khoản của chính mình.');
        }

        $user->delete();

        return back()->with('success', 'Xóa tài khoản thành công.');
    }
}
