@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-center">
                    <span>{{ $count }} Products </span>
                    <a class="float-left" href="/product/import">Get Products</a>
                    <a class="float-right" href="/product/create">Add New Product</a>
                </div>

                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif
                    <div class="container">
                        <div class="container">
                            @if(count($products) > 0)
                            @foreach ($products as $product)
                            <hr>
                            <div class="well">
                                <div class="row">
                                    <div class="col-md-4 col-sm-4">
                                        <img style="width: 50%"
                                            src="{{ asset('images/'.strtolower($product->name).'.jpg') }}" alt="image">
                                    </div>
                                    <div class="col-md-8 col-sm-8">
                                        <h5><a href="/product/{{$product}}">{{$product->name}}</a></h5><br>
                                        <div class="row">
                                            <div class="col-md-12 col-sm-12">
                                                <p>
                                                    <strong>Price:</strong>
                                                    {{$product->price}}
                                                </p>
                                            </div>
                                        </div>
                                        <div class=" row">
                                            <div class="col-md-12 col-sm-12">
                                                <p>
                                                    <small>last modified
                                                        {{ Carbon\Carbon::parse(date("Y-m-d H:i:s", strtotime($product->updated_at)))->diffForHumans()}}</small>
                                                    <br>
                                                    <small>added
                                                        {{ Carbon\Carbon::parse(date("Y-m-d H:i:s", strtotime($product->created_at)))->diffForHumans() }}</small>
                                                </p>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>
                            @endforeach
                            {{$products->links()}}
                            @else
                            <h5>No Products found</h5>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection