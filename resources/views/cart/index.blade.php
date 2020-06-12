@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-center">
                    <span>Order History</span>
                </div>

                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif
                    <div class="container">
                        <div class="container">
                            @if(count($carts) > 0)
                            @foreach ($carts as $cart)
                            <hr>
                            <div class="well">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12">
                                        <h5>Order # {{$cart->id}}</h5><br>
                                        <div class="row">
                                            <div class="col-md-12 col-sm-12">
                                                <p>
                                                    <strong>Totals:</strong>
                                                    @php
                                                    $usdTotal = (float) $rate * (float) $cart->total;
                                                    $total = '€ ' . number_format($cart->total, 2) . ' | $ ' .
                                                    number_format($usdTotal, 2);
                                                    $delivery = '€ ' . number_format(2, 2) . ' | $ ' .
                                                    number_format(2 * (float)$rate, 2);
                                                    @endphp
                                                    total: {{$total}}<br>
                                                    delivery: {{$delivery}}
                                                </p>
                                            </div>
                                        </div>
                                        <div class=" row">
                                            <div class="col-md-6 col-sm-6">
                                                <p>
                                                    <small>order
                                                        {{ Carbon\Carbon::parse(date("Y-m-d H:i:s", strtotime($cart->updated_at)))->diffForHumans()}}</small>
                                                    <br>
                                                    <small>created
                                                        {{ Carbon\Carbon::parse(date("Y-m-d H:i:s", strtotime($cart->created_at)))->diffForHumans() }}</small>
                                                </p>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <a href="/cart/{{$cart->id}}" class="btn btn-primary">Open Order</a>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>
                            @endforeach
                            {{$carts->links()}}
                            @else
                            <h5>You have no orders</h5>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection