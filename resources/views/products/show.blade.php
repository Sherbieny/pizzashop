@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Product Details</h1>
    <img style="width: 100%" src="{{ asset('images/'.str_replace(' ','_',strtolower($product->name)).'.jpg') }}"
        alt="image">
    <br><br>
    <h5>{{$product->name}}</h5>
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
    <a href="/product/{{$product->id}}/edit" class="btn btn-default">Edit</a>
    {!!Form::open(['action' => ['ProductController@destroy',$product->id],'method' => 'POST', 'class' =>
    'float-right'])!!}
    {{Form::hidden('_method','DELETE')}}
    {{Form::submit('Delete',['class' => 'btn btn-danger'])}}
    {!!Form::close()!!}
</div>

@endsection