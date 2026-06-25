<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >
    <title>
        EASY-MENU
    </title>
    <link rel="stylesheet" href="{{ asset('css/customer.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>
    @include('../customer.includes.header', ['table' => $table ?? null])
    <div class="customer-layout">

        @yield('content')

    </div>
    @include('../customer.includes.footer')

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Extract table ID from URL or query parameter
        const urlParams = new URLSearchParams(window.location.search);
        const pathParts = window.location.pathname.split('/');

        let TABLE_ID = urlParams.get('table');

        // If not in query params, try to get from URL path (e.g., /menu/1 or /cart/1)
        if (!TABLE_ID && pathParts.length > 2) {
            const lastPart = pathParts[pathParts.length - 1];
            if (!isNaN(lastPart) && lastPart !== '') {
                TABLE_ID = lastPart;
            }
        }

        // Default to 1 if not found
        if (!TABLE_ID) {
            TABLE_ID = 1;
        }
    </script>

    <script src="{{ asset('js/customer.js') }}"></script>

    @yield('scripts')

</body>
</html>
