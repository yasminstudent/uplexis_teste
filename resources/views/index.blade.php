@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-orange text-white">{{ __('Carros Cadastrados') }}</div>

                <div class="card-body">
                    @if(count($carros) > 0)
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
                                @foreach($carros as $carro)
                                    <tr id="carro_{{$carro->id}}">
                                        <th scope="row">{{$carro->id}}</th>
                                        <td> <a href="{{$carro->link}}" target="_blank">{{$carro->nome_veiculo}}</a></td>
                                        <td>{{$carro->ano}}</td>
                                        <td>{{$carro->combustivel}}</td>
                                        <td>{{$carro->cambio}}</td>
                                        <td>{{$carro->quilometragem}}</td>
                                        <td>{{$carro->portas}}</td>
                                        <td>{{$carro->cor}}</td>
                                        <td>
                                            <form action="{{ route('carro.del', ['id' => $carro->id]) }}" method="POST">
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
