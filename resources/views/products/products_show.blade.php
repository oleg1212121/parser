@extends('layouts.layout_main')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col mb-5">
                <h1>Товар</h1>
            </div>
        </div>
        <div class="row">
            <div class="col">
                @foreach(json_decode($product->content) as $key => $item)
                    {{ $key . ' - '. $item }} <br>
                @endforeach
            </div>
        </div>
        <div class="row">
            @foreach($product->images as $key => $image)
                <img src="{{\Illuminate\Support\Facades\URL::asset('storage/images/'.$image->name.$image->extention)}}" alt="{{$key}}">
                <hr>
            @endforeach
        </div>
    </div>
@endsection