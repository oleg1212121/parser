@extends('layouts.layout_main')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col mb-5">
                <h1>Редактирование заказа</h1>
            </div>
        </div>
        <div class="row">
            {{--<form class="was-validated" style="width: 100%" action="{{route('orders.store')}}" method="POST">--}}
                {{--@csrf--}}
                {{--<div class="mb-3">--}}
                    {{--<label for="validationText">Название заказа</label>--}}
                    {{--<input name="name" type="text" id="validationText" class="form-control rounded-right" placeholder="Название заказа" required>--}}
                    {{--<div class="invalid-feedback">--}}
                        {{--Введите название заказа--}}
                    {{--</div>--}}
                {{--</div>--}}

                {{--<div class="mb-3">--}}
                    {{--<label for="validationTextarea">Описание заказа</label>--}}
                    {{--<textarea name="description" class="form-control" id="validationTextarea" placeholder="Поле ввода описания" required></textarea>--}}
                    {{--<div class="invalid-feedback">--}}
                        {{--Введите описание заказа.--}}
                    {{--</div>--}}
                {{--</div>--}}


                {{--<div class="custom-file mb-3">--}}
                    {{--<input name="document" type="file" class="custom-file-input" id="validatedCustomFile">--}}
                    {{--<label class="custom-file-label" for="validatedCustomFile">Выберите файл...</label>--}}
                    {{--<div class="invalid-feedback">Проверьте прикрепленный файл</div>--}}
                {{--</div>--}}

                {{--<div class="custom-control custom-checkbox mb-3">--}}
                    {{--<input name="settings" type="checkbox" class="custom-control-input" id="customControlValidation1" >--}}
                    {{--<label class="custom-control-label" for="customControlValidation1">Сохранять картинки</label>--}}
                    {{--<div class="invalid-feedback">Example invalid feedback text</div>--}}
                {{--</div>--}}
                {{--<h5>Ссылки</h5>--}}
                {{--<div class="links">--}}
                    {{--<div class="mb-3 link">--}}
                        {{--<input name="links[]" type="text" class="form-control rounded-right" placeholder="Ссылка" required>--}}
                        {{--<div class="invalid-feedback">--}}
                            {{--Введите ссылку--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--<div>--}}
                    {{--<button type="button" class="btn btn-success mb-3" id="add_link">+</button>--}}
                {{--</div>--}}
                {{--<button class="btn btn-primary" type="submit">Создать</button>--}}
            {{--</form>--}}
        </div>
    </div>
@endsection