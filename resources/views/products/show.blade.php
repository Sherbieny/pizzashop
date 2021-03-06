@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-4 col-sm-4">
            <h1>{{$product->name}}</h1>
        </div>
        <div class="col-md-8 col-sm-8">
            <img style="width: 50%" src="{{ asset('images/'.str_replace(' ','_',strtolower($product->name)).'.jpg') }}"
                alt="image">
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-4 col-sm-4">
            <p>
                <strong>Price:</strong>
                {{$product->price}}
            </p>
        </div>
        <div class="col-md-8 col-sm-8">
            <p>
                {{$product->description}}
            </p>
        </div>
    </div>
    <small>last modified
        {{ Carbon\Carbon::parse(date("Y-m-d H:i:s", strtotime($product->updated_at)))->diffForHumans()}}</small>
    <br>
    <small>added
        {{ Carbon\Carbon::parse(date("Y-m-d H:i:s", strtotime($product->created_at)))->diffForHumans() }}</small>
    <hr>
    <a href="/cart/{{$product->id}}/add" class="btn btn-primary">Add to Cart</a>
</div>

@endsection