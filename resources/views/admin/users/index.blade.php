@extends('admin.layout')

@section('content')
<div class="container">
    <h3 class="mb-4">Quản lý người dùng</h3>

    @if(session('success'))
        <div id="flash-message" class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div id="flash-message" class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <table class="table table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Tên</th>
                <th>Email</th>
                <th>Quyền</th>
                <th>Trạng thái</th>
                <th width="280">Hành động</th>
            </tr>
        </thead>

        <tbody>
            @forelse($users as $index => $user)
                <tr>
                    <td>{{ $index + 1 }}</td>

                    <td>{{ $user->name }}</td>

                    <td>{{ $user->email }}</td>

                    <td>
                        <form action="{{ route('admin.users.updateRole', $user) }}"
                              method="POST">
                            @csrf
                            @method('PATCH')

                            <select name="role"
                                    class="form-select form-select-sm"
                                    onchange="this.form.submit()">
                                <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>
                                    Người dùng
                                </option>

                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>
                                    Admin
                                </option>

                                <option value="organizer" {{ $user->role === 'organizer' ? 'selected' : '' }}>
                                    Đơn vị tổ chức
                                </option>
                            </select>
                        </form>
                    </td>

                    <td>
                        @if($user->is_active ?? true)
                            <span class="badge bg-success">Đang hoạt động</span>
                        @else
                            <span class="badge bg-secondary">Đã khóa</span>
                        @endif
                    </td>

                    <td>
                        <form action="{{ route('admin.users.toggleActive', $user) }}"
                              method="POST"
                              class="d-inline">
                            @csrf
                            @method('PATCH')

                            <button class="btn btn-sm btn-warning">
                                {{ ($user->is_active ?? true) ? 'Khóa' : 'Mở khóa' }}
                            </button>
                        </form>

                        <form action="{{ route('admin.users.destroy', $user) }}"
                              method="POST"
                              class="d-inline"
                              onsubmit="return confirm('Xóa tài khoản này?')">
                            @csrf
                            @method('DELETE')

                            <button class="btn btn-sm btn-danger">
                                Xóa
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">
                        Chưa có người dùng
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if ($users->hasPages())
        <div class="dashboard-pagination mt-3">
            {{ $users->links() }}
        </div>
    @endif

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const flashMessage = document.getElementById('flash-message');

        if (flashMessage) {
            setTimeout(() => {
                flashMessage.style.transition = 'all 0.6s ease';
                flashMessage.style.opacity = '0';
                flashMessage.style.transform = 'translateY(-10px)';

                setTimeout(() => {
                    flashMessage.remove();
                }, 600);
            }, 4000);
        }
    });
</script>
@endsection
