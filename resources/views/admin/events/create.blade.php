@extends('admin.layout')

@section('content')
<div class="container">
    <a href="{{ route('admin.events.index') }}" class="btn btn-secondary mb-3">
        Quay lại
    </a>

    <h2 class="mb-4"> Thêm sự kiện</h2>

    <form method="POST" action="{{ route('admin.events.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label>Tên sự kiện</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="row">
            <div class="col">
                <label>Bắt đầu</label>
                <input type="datetime-local"
                    name="start_datetime"
                    class="form-control"
                    required>
            </div>

            <div class="col">
                <label>Kết thúc</label>
                <input type="datetime-local"
                    name="end_datetime"
                    class="form-control"
                    required>
            </div>
        </div>

        <div class="mt-3">
            <label>Địa điểm</label>
            <input type="text" name="location" class="form-control" required>
        </div>

        <div class="mt-3">
            <label>Số người tối đa</label>
            <input type="number" name="max_participants" class="form-control" min="1" required>
        </div>

        <div class="mt-3">
            <label>Mô tả</label>
            <textarea name="description" class="form-control" rows="3"></textarea>
        </div>

        <div class="mt-3">
            <label for="image" class="form-label">Hình ảnh sự kiện</label>

            <input type="file"
                id="image"
                name="image"
                class="form-control"
                accept="image/jpeg,image/png,image/jpg,image/webp">

            <small class="text-muted">
                Hỗ trợ JPG, PNG, WEBP. Dung lượng tối đa 2 MB.
            </small>

            @error('image')
                <div class="text-danger mt-1">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <hr>

        <h5> Vé thường</h5>
        <div class="row">
            <div class="col">
                <input type="number" name="ticket_normal_price" class="form-control" placeholder="Giá" required>
            </div>
            <div class="col">
                <input type="number" name="ticket_normal_qty" class="form-control" placeholder="Số lượng" required>
            </div>
        </div>

        <h5 class="mt-3"> Vé VIP</h5>
        <div class="row">
            <div class="col">
                <input type="number" name="ticket_vip_price" class="form-control" placeholder="Giá" required>
            </div>
            <div class="col">
                <input type="number" name="ticket_vip_qty" class="form-control" placeholder="Số lượng" required>
            </div>
        </div>

        <button class="btn btn-primary mt-4">Lưu sự kiện</button>
    </form>
</div>
@endsection
