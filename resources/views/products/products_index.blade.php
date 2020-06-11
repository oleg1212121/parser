@extends('layouts.layout_main')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col mb-5">
                <h1>Товары</h1>
            </div>
        </div>

        <div class="row">
            @foreach($products as $product)
                <div class="col-10">
                    {{'Имя - '.$product->title}}<br>
                    {{'Market Id - '.$product->market_id}}
                </div>
                <div class="col-2">
                    <a href="{{route('products.show',['product' => $product->id])}}" class="btn btn-dark">Подробнее</a>
                </div>
                <hr>
            @endforeach
        </div>
    </div>
@endsection