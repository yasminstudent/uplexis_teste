@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-orange text-white">{{ __('Carros Cadastrados') }}</div>

                <div class="card-body">
                    @if(count($cars) > 0)
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Ano</th>
                                <th>Combustivel</th>
                                <th>Câmbio</th>
                                <th>Quilometragem</th>
                                <th>Portas</th>
                                <th>Cor</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                                @foreach($cars as $car)
                                    <tr id="carro_{{$car->id}}">
                                        <th scope="row">{{$car->id}}</th>
                                        <td> <a href="{{$car->link}}" target="_blank">{{$car->nome_veiculo}}</a></td>
                                        <td>{{$car->ano}}</td>
                                        <td>{{$car->combustivel}}</td>
                                        <td>{{$car->cambio}}</td>
                                        <td>{{$car->quilometragem}}</td>
                                        <td>{{$car->portas}}</td>
                                        <td>{{$car->cor}}</td>
                                        <td>
                                            <form action="{{ route('car.delete', ['id' => $car->id]) }}" method="POST">
                                                @method('DELETE')
                                                @csrf
                                                <button class="btn btn-sm btn-danger btn-delete"><i class="fa fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                        </tbody>
                    </table>
                    @else
                        Não há itens para exibir
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
