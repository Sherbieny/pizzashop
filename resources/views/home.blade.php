@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Welcom to Pizza Shop</div>

                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif
                    @guest
                    Login or Register to view orders history
                    @else
                    You are logged in!
                    @endguest

                    <a class="btn btn-primary" href="{{ url('product') }}">{{ __('Go to Menu') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection