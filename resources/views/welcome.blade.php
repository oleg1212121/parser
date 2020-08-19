<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Styles -->

    </head>
    <body>
        <div class="flex-center position-ref full-height">
            <div class="content">
                <div class="title m-b-md">
                    Laravel
                </div>
                <div class="links">
                    <a href="{{route('orders.index')}}">Orders page</a>
                    <a href="{{route('horizon.index')}}">Horizon page</a>
                </div>
                <div>
                    {!! \App\Models\Page::find(143)->content !!}
                    @csrf
                    <table style="display: none;">

                    </table>
                </div>
            </div>
        </div>
        {{--<script--}}
                {{--src="https://code.jquery.com/jquery-3.5.1.min.js"--}}
                {{--integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="--}}
                {{--crossorigin="anonymous"></script>--}}
        {{--<script src="pepe.js"></script>--}}
    </body>
</html>
