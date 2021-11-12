<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Carros;
use App\User;
use GuzzleHttp\Client;

class CarrosController extends Controller
{
    private $carro;
    private $usuario;

    public function __construct(Carros $carro, User $usuario)
    {
        $this->middleware('auth');
        $this->carro = $carro;
        $this->usuario = $usuario;
    }

    /**
     * Lista todos os carros relacionados ao usuário.
     *
     * @return \Illuminate\View\View
     */
    public function index(){
        $usuarioLogado = $this->usuario::find(auth()->user()->getAuthIdentifier());

        return view('index', [
            'carros' =>  $usuarioLogado->carros
        ]);
    }

    /**
     * Busca carros e cadastra no banco.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request){
        try {
            $data = $request->all();

            $cliente = new Client();
            $resposta = $cliente->request('GET', "https://www.questmultimarcas.com.br/estoque?termo={$data['term']}");
            $conteudo = $resposta->getBody()->getContents();


            preg_match_all(
                '/<a href="https:\/\/[\w\d\.]*\/carros\/[a-z]+\/[\w\-]+\/[0-9]+\/[0-9]+">([a-z A-Z 0-9 .]+)/is',
                $conteudo,
                $matrizNomeVeiculo
            );

            preg_match_all(
                '/inner">[\n\r]*<a href="(https:\/\/www.questmultimarcas.com.br\/carros\/[a-z]+\/[\w\-\d]+\/[0-9]+\/[0-9]+)">/is',
                $conteudo,
                $matrizLink
            );

            preg_match_all(
                '/<a href="https:\/\/[\w\d\.]*\/carros\/[a-z]+\/[\w\-]+\/([0-9]+)\/[0-9]+">[a-z A-Z 0-9 .]+/is',
                $conteudo,
                $matrizAno
            );

            preg_match_all(
                '/Combustível:\s<\/span>(\n|\r)\s+<span class="card-list__info">((\n|\r)\s+)([a-z A-Z 0-9]+)/is',
                $conteudo,
                $matrizCombustivel
            );

            preg_match_all(
                '/Câmbio:\s<\/span>(\n|\r)\s+<span class="card-list__info">((\n|\r)\s+)([a-z A-Z 0-9 á Á]+)/is',
                $conteudo,
                $matrizCambio
            );

            preg_match_all(
                '/Portas:\s<\/span>(\n|\r)\s+<span class="card-list__info">((\n|\r)\s+)([a-z A-Z 0-9 á Á]+)/is',
                $conteudo,
                $matrizPorta
            );

            preg_match_all(
                '/Quilometragem:\s<\/span>(\n|\r)\s+<span class="card-list__info">((\n|\r)\s+)([a-z A-Z 0-9 á Á.]+)/is',
                $conteudo,
                $matrizQuilometragem
            );

            preg_match_all(
                '/Cor:\s<\/span>(\n|\r)\s+<span class="card-list__info">((\n|\r)\s+)([a-z A-Z 0-9 á Á.]+)/is',
                $conteudo,
                $matrizCor
            );

            if(count($matrizNomeVeiculo[1]) == 0)
            {
                return response()->json([
                    'data' => [
                        'message' => 'Nenhum carro foi encontrado!',
                        'status' => 200
                    ]
                ],200);
            }
            else
            {
                for($i=0; $i< count($matrizNomeVeiculo[1]); $i++){
                    $this->carro->create([
                        'user_id' => auth()->user()->getAuthIdentifier(),
                        'nome_veiculo' =>  $matrizNomeVeiculo[1][$i],
                        'link' => $matrizLink[1][$i],
                        'ano' => $matrizAno[1][$i],
                        'combustivel' => $matrizCombustivel[4][$i],
                        'portas' => $matrizPorta[4][$i],
                        'quilometragem' => $matrizQuilometragem[4][$i],
                        'cambio' => $matrizCambio[4][$i],
                        'cor' => $matrizCor[4][$i]
                    ]);
                }

                return response()->json([
                    'data' => [
                        'message' => 'Carro(s) cadastrado(s) com sucesso!',
                        'status' => 201
                    ]
                ],201);
            }
        }
        catch (\Exception $e){
            return response()->json([
                'data' => [
                    'message' => 'Erro ao buscar carro!',
                ]
            ], 400);
        }
    }


    /**
     * Deleta carro.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $carro = $this->carro::find($id);

        if($carro->user_id ==  auth()->user()->getAuthIdentifier())
        {
            $carro->delete();
        }

        return redirect("/index");
    }
}
