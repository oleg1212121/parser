@extends('layouts.layout_main')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col mb-5">
                <h1>Просмотр заказа</h1>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <form action="{{route('orders.update',['order' => $order->id])}}" method="POST">
                    @method('PUT')
                    @csrf
                    <button type="submit" class="btn-success btn">Опубликовать</button>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col">
                {{'Название - '.$order->name}}
                <hr>
                {{'Описание - '.$order->description}}
                <hr>
                {{'Дата публикации - '.($order->published_at ?? 'не опубликован') }}
                <hr>
                {{'Статус - '.($order->is_done ? 'закончен' : 'в обработке')}}
                <hr>
                {{'Дополнительно скачивать картинки - '.($order->settings()->count() > 0 ? 'Да' : 'Нет')}}
                <hr>
                <p>Ссылки на категории</p>
                @foreach(\App\Models\Link::where('order_id', $order->id)->where('type',0)->get() as $item)
                    {{$item->link}}<br>
                @endforeach
            </div>
        </div>
    </div>
@endsection