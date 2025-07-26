@extends('layouts.master')

@section('title', $libro->titulo)

@section('content')
<section class="product-details mt-60 space-extra-bottom">
    <div class="container">
        <div class="row gx-60 gy-50">
            <div class="col-lg-6">
                <div class="product-big-img">
                    @if($libro->imagen)
                    <img src="{{ asset('storage/' . $libro->imagen) }}" alt="{{ $libro->titulo }}" class="rounded-2">
                    @else
                    <div class="img"><img src="assets/img/product/post-card1-6.png" alt="Product Image"></div>
                    @endif
                </div>
            </div>
            <div class="col-xxl-6 align-self-center">
                <div class="product-about">
                    <h2 class="product-title" data-cue="slideInUp">{{ $libro->titulo }}</h2>
                    <div class="product-rating" data-cue="slideInUp">
                        <div class="star-rating" role="img" aria-label="Rated 5.00 out of 5"><span style="width:100%">Rated <strong class="rating">5.00</strong> out of 5 based on <span class="rating">1</span> customer rating</span></div>
                        <a href="shop-details.html" class="woocommerce-review-link">(<span class="count">4</span> customer reviews)</a>
                    </div>

                    <p class="text" data-cue="slideInUp">{{ $libro->sinopsis }}</p>
                    <p class="price" data-cue="slideInUp">${{ $libro->precio_base }}<del>${{ $libro->precio_final }}</del> <span class="stock-availability">Stock Availability.</span></p>
                    <div class="actions">
                        <div class="quantity" data-cue="slideInUp">
                            <button class="quantity-minus qty-btn"><i class="far fa-minus"></i></button>
                            <input type="number" class="qty-input" step="1" min="1" max="100" name="quantity" value="1" title="Qty">
                            <button class="quantity-plus qty-btn"><i class="far fa-plus"></i></button>
                        </div>
                        <button class="ot-btn" data-cue="slideInUp"><i class="fa-light fa-basket-shopping me-1"></i> Add to Cart</button>
                        <a href="wishlist.html" class="icon-btn" data-cue="slideInUp"><i class="far fa-heart"></i></a>
                        <a href="#" class="icon-btn" data-cue="slideInUp"><i class="far fa-arrows-cross"></i></a>
                    </div>
                    <div class="product_meta" data-cue="slideInUp">
                        <span class="sku_wrapper">SKU: <span class="sku">FTC1020B65D</span></span>
                        <span>Tags: <a href="shop.html">4-5 years</a><a href="shop.html">12+ years</a></span>
                        <span class="posted_in">Category: <a href="shop.html">Kids Toys</a></span>
                        <span>Tags: <a href="shop.html">Toys</a><a href="shop.html">Baby Shirt</a></span>
                    </div>
           
                </div>
            </div>
        </div>
    </div>
</section>
@endsection