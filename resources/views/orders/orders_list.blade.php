@extends('layouts.layout_main')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col mb-5">
                <h1>Заказы</h1>
            </div>
        </div>
        <div class="row">
            <div class="col mb-3">
                <a href="{{route('orders.create')}}" class="btn btn-success">Создать заказ</a>
            </div>
        </div>
        <div class="row">
            @foreach($orders as $order)
                <div class="col-12">
                    {{'Имя - '.$order->name}}<br>
                    {{'Дата публикации - '.($order->published_at ?? 'Не опубликован')}}<br>
                    {{'Описание - '.$order->description}}<br>
                    {{'Статус - '.($order->is_done ? 'Окончен' : 'В обработке')}}
                </div>
                <div class="col-12">
                    <a href="{{route('orders.show',['order' => $order->id])}}" class="btn btn-dark">Подробнее</a><hr>
                </div>
            @endforeach
        </div>
    </div>
@endsection