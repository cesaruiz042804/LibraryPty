<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Library Pty')</title>
    <meta name="description" content="Morden Bootstrap HTML5 Template">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700&family=Rubik:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <!-- Fontawesome Icon -->
    <link rel="stylesheet" href="{{ asset('assets/css/fontawesome.min.css') }}">
    <!-- Magnific Popup -->
    <link rel="stylesheet" href="{{ asset('assets/css/magnific-popup.min.css') }}">
    <!-- Swiper Js -->
    <link rel="stylesheet" href="{{ asset('assets/css/swiper-bundle.min.css') }}">
    <!-- Theme Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    @stack('styles')
</head>

<body>
    <style>
        .box-title {
            min-height: 45px;
        }

        .ot-product {
            padding: 12px;
            max-width: max-content;
        }

        .shop input {
            height: 68px;
            border-radius: 8px;
        }

        .actions {
            margin-right: 9px;
        }

        .actions a {
            z-index: 100px;
        }

        .ot-product .product-hover-content {
            background: #964F4F !important;
        }

        .ot-product .product-hover-content .price,
        .ot-product .product-hover-content .box-title {
            color: whitesmoke !important;
        }

        .ot-btn {
            background-color: #B8860B;
        }

        .star-rating {
            color: #FFFFFFFF;
        }

        .fixed-image-container {
            width: 100%;
            height: 250px;
            /* Puedes ajustar esto a lo que prefieras */
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f8f8;
            /* Color de fondo si la imagen no cubre todo */
        }

        .fixed-image-container img {
            max-height: 100%;
            width: auto;
            object-fit: contain;
            /* o 'cover' si prefieres que rellene todo */
        }

        .ot-product .price {
            color: #212529;
        }

        .ot-product .product-hover-content a:hover {
            color: #EACA63FF;
        }

        .header-form {
            max-width: 100%;
        }

        .header-form .form-select {
            width: 280px;
        }

        /* version movil form */
        @media (max-width: 768px) {
            .filterForm {
                display: none;
            }

            .mobileFilterForm {
                display: block;
            }

        }
    </style>
    @include('library.partials.navbar')
    @include('library.partials.quickView')

    @yield('content')

    @include('library.partials.footer')
    @stack('scripts')
    <script src="{{ asset('assets/js/vendor/jquery-3.7.1.min.js') }}"></script>
    <!-- Swiper Js -->
    <script src="{{ asset('assets/js/swiper-bundle.min.js') }}"></script>
    <!-- Bootstrap -->
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    <!-- scrollCue Js -->
    <script src="{{ asset('assets/js/scrollCue.min.js') }}"></script>
    <!-- Magnific Popup -->
    <script src="{{ asset('assets/js/jquery.magnific-popup.min.js') }}"></script>
    <!-- Counter Up -->
    <script src="{{ asset('assets/js/jquery.counterup.min.js') }}"></script>
    <!-- Range Slider -->
    <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
    <!-- Gsap for Slider Cursor -->
    <script src="{{ asset('assets/js/gsap.min.js') }}"></script>
    <!-- Isotope Filter -->
    <script src="{{ asset('assets/js/imagesloaded.pkgd.min.js') }}"></script>
    <script src="{{ asset('assets/js/isotope.pkgd.min.js') }}"></script>

    <script src="{{ asset('assets/js/particles.min.js') }}"></script>

    <script src="{{ asset('assets/js/particles-config.js') }}"></script>
    <!-- Main Js File -->
    <script src="{{ asset('assets/js/main.js') }}"></script>
</body>

</html>