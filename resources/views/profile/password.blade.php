@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="col-md-6 mx-auto card p-4">
        <h4>Đổi mật khẩu</h4>

        <form method="POST">
            @csrf

            <input type="password" name="password" class="form-control mb-3" placeholder="Mật khẩu mới">
            <input type="password" name="password_confirmation" class="form-control mb-3" placeholder="Nhập lại mật khẩu">

            <button class="btn btn-primary">Xác nhận</button>
        </form>
    </div>
</div>
@endsection
