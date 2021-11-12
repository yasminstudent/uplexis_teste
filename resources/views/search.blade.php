@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h4 class="text-center">Buscar Carro</h4>
                <form method="POST" action="{{ route('search') }}" class="d-flex justify-content-center" id="search_car">
                    @csrf

                    <div id="double_req_block" class="d-none">
                        <div class="row">
                            <div class="container">
                                <div class="col d-flex justify-content-center">
                                    <span class="mt-5">
                                         carregando...
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-2 w-75">
                        <input type="text" class="form-control w-100" id="term" placeholder="Digite o nome do carro" name="term">
                    </div>

                    <button type="submit" class="btn btn-orange mb-2 ml-1">
                        {{ __('Capturar') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
