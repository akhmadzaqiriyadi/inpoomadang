<!doctype html>
<html lang="en">
<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>Dashboard - Owner</title>
   <link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.2/dist/css/bootstrap.min.css">
   <link rel="stylesheet" href="{{ asset('all.css') }}">
</head>
<body class="bg-light">
@section('content')
    @include('owner.navbarOwner')
    <div class="container my-5">
        <h1 class="mt-5">Dashboard</h1>
        <a href="{{ route('owner.products') }}" class="btn btn-success mb-3">Tambah Produk Baru</a>
        <a href="{{ route('owner.orders') }}" class="btn btn-primary mb-3">Pesanan</a>
        <a href="{{ route('logout') }}" class="btn btn-danger mb-3">Logout</a>

        <h2 class="mt-4">Katalog Produk</h2>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="d-flex align-items-center mb-4">
            <div class="dropdown me-2">
                <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Pilih Kategori
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('owner.products') }}">Semua Kategori</a></li>
                    @php
                        $categories = \App\Models\Product::distinct()->pluck('category');
                    @endphp
                    @foreach ($categories as $category)
                        <li><a class="dropdown-item" href="{{ route('owner.products', ['category' => $category]) }}">{{ $category }}</a></li>
                    @endforeach
                </ul>
            </div>

            <form action="{{ route('owner.products') }}" method="GET" class="d-flex">
                <input type="hidden" name="category" value="{{ request('category') }}">
                <input class="form-control me-2" type="search" name="search" placeholder="Cari produk" value="{{ request('search') }}">
                <button class="btn btn-outline-success" type="submit">Cari</button>
            </form>
        </div>

        <div class="row">
            @foreach($products as $product)
                <div class="col-sm-12 col-md-6 col-lg-4">
                    <div class="card mb-4">
                        <img src="{{ asset($product->image) }}" class="card-img-top" alt="{{ $product->name }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <p class="card-text">Harga: Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                            <p class="card-text">Stok: {{ $product->stock }}</p>
                            <button class="btn btn-primary add-to-cart" data-id="{{ $product->id }}">+</button>
                            <a href="{{ route('owner.updateStock', ['id' => $product->id]) }}" class="btn btn-warning">Edit Stok</a>
                            <a href="{{ route('owner.updateProduct', ['id' => $product->id]) }}" class="btn btn-secondary">Edit Produk</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.add-to-cart').click(function () {
                var productId = $(this).data('id');
                $.post('{{ route('owner.products') }}', { product_id: productId, _token: '{{ csrf_token() }}' }, function (response) {
                    alert(response.message);
                }, 'json');
            });
        });
    </script>
@endsection
</body>
</html>
