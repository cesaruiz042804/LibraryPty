@extends('layouts.master')

@section('title', 'Inicio')

@section('content')
<div class="container py-4">
    <div class="container py-4">
        <div class="row align-items-center mb-4 gy-3">
            <div class="search col-12 col-sm-12 col-md-8">
                {{-- Formulario escritorio (oculto en móviles) --}}
                <form id="filterForm" action="{{ route('libros.search') }}" method="GET" class="header-form d-none d-md-flex gap-2">
                    <div class="form-group">
                        <select name="category" id="category-desktop" class="form-select">
                            <option value="" selected disabled>Categorías</option>
                            @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}" {{ request('category') == $categoria->id ? 'selected' : '' }}>{{ $categoria->nombre }}</option>
                            @endforeach
                        </select>
                        <i class="fa-sharp fa-solid fa-grid-2"></i>
                    </div>
                    <div class="form-group d-flex w-100">
                        <input type="text" class="form-control" name="search" placeholder="Buscar libros..." value="{{ request('search') }}">
                        <button type="submit" class="submit-btn simple-icon"><i class="far fa-search"></i></button>
                    </div>
                </form>

                {{-- Formularios móviles (solo visibles en móviles) --}}
                <div class="d-md-none mt-3">
                    <div class="card p-3 shadow-sm">
                        {{-- Formulario de categoría (auto-envío) --}}
                        <form id="mobileCategoryForm" action="{{ route('libros.search') }}" method="GET" class="mb-3">
                            <div class="form-group">
                                <label for="category-mobile" class="form-label">Categoría</label>
                                <select name="category" id="category-mobile" class="form-select">
                                    <option value="" selected disabled>Categorías</option>
                                    @foreach($categorias as $categoria)
                                    <option value="{{ $categoria->id }}" {{ request('category') == $categoria->id ? 'selected' : '' }}>{{ $categoria->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </form>

                        {{-- Formulario de búsqueda --}}
                        <form id="mobileSearchForm" action="{{ route('libros.search') }}" method="GET">
                            <div class="form-group">
                                <label for="search-mobile" class="form-label">Buscar</label>
                                <div class="input-group">
                                    <input type="text" id="search-mobile" class="form-control" name="search" placeholder="Buscar libros..." value="{{ request('search') }}">
                                    <button type="submit" class="btn btn-primary"><i class="far fa-search"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Listado de libros --}}
            <div class="row g-4">
                @foreach($libros as $libro)
                <div class="col-12 col-sm-12 col-md-2 col-lg-2">
                    <div class="ot-product d-flex flex-column">
                        <div class="product-img position-relative">
                            @if($libro->imagen)
                            <img src="{{ asset('storage/' . $libro->imagen) }}" alt="{{ $libro->titulo }}" class="img-fluid rounded-2">
                            @else
                            <img src="{{ asset('assets/img/product/post-card1-4.png') }}" alt="Product Image" class="img-fluid rounded-2">
                            @endif
                            <div class="actions position-absolute top-0 end-0 p-2">
                                <a href="#" class="icon-btn"><i class="far fa-heart"></i></a>
                            </div>
                        </div>
                        <div class="product-content text-center mt-2">
                            <h6 class="box-title"><a href="{{ route('libros.show', $libro->id) }}">{{ $libro->titulo }}</a></h6>
                            <span class="price">${{ $libro->precio_base }}</span>
                            <div class="woocommerce-product-rating mt-1">
                                <div class="star-rating">
                                    <span>Rated <strong class="rating">5.00</strong> out of 5</span>
                                </div>
                            </div>
                        </div>
                        <div class="product-hover-content text-center mt-auto">
                            <h6 class="box-title"><a href="shop-details.html">{{ $libro->titulo }}</a></h6>
                            <span class="price">${{ $libro->precio_base }}</span>
                            <div class="woocommerce-product-rating">
                                <div class="star-rating">
                                    <span>Rated <strong class="rating">5.00</strong> out of 5</span>
                                </div>
                            </div>
                            <a class="ot-btn btn btn-primary mt-2" href="{{ route('libros.show', $libro->id) }}">
                                <i class="fa-light fa-basket-shopping me-1"></i> Agregar al carrito
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endsection

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Enviar automáticamente en escritorio
            const categoryDesktop = document.getElementById('category-desktop');
            const desktopForm = document.getElementById('filterForm');
            if (categoryDesktop && desktopForm) {
                categoryDesktop.addEventListener('change', function() {
                    desktopForm.submit();
                });
            }

            // Enviar automáticamente en móvil
            const categoryMobile = document.getElementById('category-mobile');
            const mobileForm = document.getElementById('mobileCategoryForm');
            if (categoryMobile && mobileForm) {
                categoryMobile.addEventListener('change', function() {
                    mobileForm.submit();
                });
            }
        });
    </script>
    @endpush