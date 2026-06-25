@extends('layouts.customer')

@section('content')

<div class="track-page">

    <div class="track-card">

        <h1>
            Order #{{ $order->id }}
        </h1>

        <p>
            {{ $order->table->name }}
        </p>

        <div id="statusContainer">

            @if($order->status == 'pending')

                <div class="status pending">
                    🟡 Pending
                </div>

            @elseif($order->status == 'preparing')

                <div class="status preparing">
                    🔵 Preparing
                </div>

            @elseif($order->status == 'completed')

                <div class="status completed">
                    🟢 Completed
                </div>

            @else

                <div class="status cancelled">
                    🔴 Cancelled
                </div>

            @endif

        </div>

        <hr>

        @foreach($order->items as $item)

            <div class="item-row">

                <span>
                    {{ $item->item->name }}
                </span>

                <strong>
                    x{{ $item->quantity }}
                </strong>

            </div>

        @endforeach

        <hr>

        <div class="order-total">

            Total:
            ${{ number_format($order->total,2) }}

        </div>

    </div>

</div>

<script>

setInterval(async () => {

    const response =
        await fetch(
            '/order-status/{{ $order->id }}'
        );

    const data =
        await response.json();

    let html = '';

    if(data.status === 'pending') {

        html =
        `<div class="status pending">
            🟡 Pending
        </div>`;

    } else if(
        data.status === 'preparing'
    ) {

        html =
        `<div class="status preparing">
            🔵 Preparing
        </div>`;

    } else if(
        data.status === 'completed'
    ) {

        html =
        `<div class="status completed">
            🟢 Completed
        </div>`;

    } else {

        html =
        `<div class="status cancelled">
            🔴 Cancelled
        </div>`;
    }

    document
        .getElementById(
            'statusContainer'
        )
        .innerHTML = html;

}, 5000);

</script>

@endsection
