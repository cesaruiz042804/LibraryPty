


<div id="QuickView" class="white-popup mfp-hide">
    <div class="container bg-white rounded-10">
        <div class="row gx-60">
            <div class="col-lg-6">
                <div class="product-big-img">
                    {{-- La imagen se actualizará con JS --}}
                    <div class="img"><img id="quickview-imagen" src="assets/img/product/post-card1-6.png" alt="Product Image"></div>
                </div>
            </div>
            <div class="col-lg-6 align-self-center">
                <div class="product-about">
                    {{-- El precio se actualizará con JS --}}
                    <p class="price" id="quickview-precio">$120.85<del>$150.99</del></p>
                    {{-- El título se actualizará con JS --}}
                    <h2 class="product-title" id="quickview-titulo">Ultricies At Torquent Dui</h2>
                    <div class="product-rating">
                        <div class="star-rating" role="img" aria-label="Rated 5.00 out of 5"><span style="width:100%">Rated <strong class="rating">5.00</strong> out of 5 based on <span class="rating">1</span> customer rating</span></div>
                        <a href="shop-details.html" class="woocommerce-review-link">(<span class="count">4</span> customer reviews)</a>
                    </div>
                    {{-- La sinopsis se actualizará con JS --}}
                    <p class="text" id="quickview-sinopsis">Prepare to embark on a sensory journey with the Bosco Apple...</p>
                    <div class="mt-2 link-inherit">
                        <p>
                            <strong class="text-title me-3">Availability:</strong>
                            {{-- El stock se actualizará con JS --}}
                            <span class="stock in-stock" id="quickview-stock"><i class="far fa-check-square me-2 ms-1"></i>In Stock</span>
                        </p>
                    </div>
                    <div class="actions">
                        <div class="quantity">
                            <button class="quantity-minus qty-btn"><i class="far fa-minus"></i></button>
                            <input type="number" class="qty-input" step="1" min="1" max="100" name="quantity" value="1" title="Qty">
                            <button class="quantity-plus qty-btn"><i class="far fa-plus"></i></button>
                        </div>
                        <button class="ot-btn">Agregar al carrito</button>
                        <a href="wishlist.html" class="icon-btn"><i class="far fa-heart"></i></a>
                    </div>
                    <div class="product_meta">
                        {{-- El ISBN se actualizará con JS --}}
                        <span class="sku_wrapper">SKU: <span class="sku" id="quickview-isbn">Bosco-Apple-Fruit</span></span>
                        {{-- La categoría se actualizará con JS --}}
                        <span class="posted_in">Category: <a href="shop.html" id="quickview-categoria">Fresh Fruits</a></span>
                    </div>
                </div>
            </div>
        </div>
        <button title="Close (Esc)" type="button" class="mfp-close">×</button>
    </div>
</div> 