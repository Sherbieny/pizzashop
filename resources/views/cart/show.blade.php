@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-center">
                    @if($cart->qty > 0)
                    @if($cart->is_active)
                    <span>Your cart has {{$cart->qty}} items with a total of {{$total}} plus {{$delivery}} delivery
                    </span><br>
                    @else
                    <span>Your order has {{$cart->qty}} items with a total of {{$total}} plus {{$delivery}} delivery
                    </span><br>
                    <span>
                        <strong>Address:</strong>
                        {{$cart->address}}
                    </span>
                    @endif
                    @endif
                </div>
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif
                    <div class="container">
                        <div class="container">
                            @if($cart->qty > 0)
                            @foreach ($cart->items as $item)
                            <hr>
                            <div class="well">
                                <div class="row">
                                    <div class="col-md-4 col-sm-4">
                                        <img style="width: 70%"
                                            src="{{ asset('images/'.str_replace(' ','_',strtolower($item->product->name)).'.jpg') }}"
                                            alt="image">
                                    </div>
                                    <div class="col-md-8 col-sm-8">
                                        <h5><a href="/product/{{$item->product_id}}">{{$item->product->name}}</a></h5>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-6 col-sm-6">
                                                <p>
                                                    <strong>Cost:</strong>
                                                    {{$item->cost}}
                                                </p>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <p>
                                                    <strong>Qty:</strong>
                                                    {{$item->qty}}
                                                </p>
                                            </div>
                                        </div>
                                        <div class=" row">
                                            @if($cart->is_active)
                                            <div class="col-md-6 col-sm-6">
                                                <p>
                                                    <small>last modified
                                                        {{ Carbon\Carbon::parse(date("Y-m-d H:i:s", strtotime($item->updated_at)))->diffForHumans()}}</small>
                                                    <br>
                                                    <small>added
                                                        {{ Carbon\Carbon::parse(date("Y-m-d H:i:s", strtotime($item->created_at)))->diffForHumans() }}</small>
                                                </p>
                                            </div>
                                            @else
                                            <div class="col-md-6 col-sm-6">
                                                <p>
                                                    <small>ordered
                                                        {{ Carbon\Carbon::parse(date("Y-m-d H:i:s", strtotime($item->updated_at)))->diffForHumans()}}</small>
                                                    <br>
                                                    <small>created
                                                        {{ Carbon\Carbon::parse(date("Y-m-d H:i:s", strtotime($item->created_at)))->diffForHumans() }}</small>
                                                </p>
                                            </div>
                                            @endif
                                            @if($cart->is_active)
                                            <div class="col-md-6 col-sm-6">
                                                <a href="/cart/{{$item->id}}/remove" class="btn btn-danger">Remove</a>
                                            </div>
                                            @endif
                                        </div>


                                    </div>
                                </div>

                            </div>
                            @endforeach
                            @if($cart->is_active)
                            <hr>
                            <div class="well">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12">
                                        <a data-toggle="collapse" data-target="#collapse" href="#" style="width: 100%"
                                            class="btn btn-primary btn-block">Place Order</a>
                                    </div>
                                    <div class="col-md-12 col-sm-12 mt-1 text-center">
                                        <div class="panel-group">
                                            <div class="panel panel-default">
                                                <div id="collapse" class="panel-collapse collapse">
                                                    <h6>Please fill the form to submit your order</h6>
                                                    {!! Form::open(['action' => ['CartController@update', $cart],
                                                    'method' =>
                                                    'post']) !!}
                                                    <div class="form-group">
                                                        {{Form::email('email', $value = $cart->customer_email ?? '', ['class' => 'form-control','placeholder' => 'email', 'required', $cart->customer_email ? 'readonly': ''])}}
                                                    </div>
                                                    <div class="form-group">
                                                        {{Form::text('first_name', $value = $cart->customer_firstname ?? '', ['class' => 'form-control','placeholder' => 'first name', 'required'])}}
                                                    </div>
                                                    <div class="form-group">
                                                        {{Form::text('last_name', $value = $cart->customer_lastname ?? '', ['class' => 'form-control','placeholder' => 'last name', 'required'])}}
                                                    </div>
                                                    <div class="form-group">
                                                        {{Form::text('address', $value = $cart->address ?? '', ['class' => 'form-control','placeholder' => 'addresss', 'required'])}}
                                                    </div>
                                                    {{Form::hidden('_method','PUT')}}
                                                    {{Form::submit('Submit',['class' => 'btn btn-primary'])}}
                                                    {!! Form::close() !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                        @else
                        <h5>Cart is empty</h5>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
</div>
@endsection