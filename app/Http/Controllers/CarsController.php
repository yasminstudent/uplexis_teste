<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Cars as CarsModel;
use App\Models\Users as UsersModel;

class CarsController extends Controller
{
    private $car;
    private $user;

    public function __construct(CarsModel $car, UsersModel $user)
    {
        $this->car = $car;
        $this->user = $user;
    }

    /**
     * Lista todos os carros relacionados ao usuário.
     */
    public function index() :\Illuminate\View\View
    {
        $userId = auth()->user()->getAuthIdentifier();
        $user = $this->user::find($userId);

        return view('index', ['cars' => $user->cars]);
    }

    /**
     * Busca carros e cadastra no banco.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        try {
            $term = urlencode($request->only(["term"]));
            $client = new Client();
            $response = $client->request(
                'GET', 
                "https://www.questmultimarcas.com.br/estoque?termo={$term}"
            );
            $contents = $response->getBody()->getContents();

            //Criar um "motor" onde ficará as regex
            //Fazer outras pq o layout da página mudou
            //se ficar algo parecido com isso, seria viável um loop
            //mudar os nomes das variáveis para inglês
            //e definir o nome dos métodos
            preg_match_all(
                '/<a href="https:\/\/[\w\d\.]*\/carros\/[a-z]+\/[\w\-]+\/[0-9]+\/[0-9]+">([a-z A-Z 0-9 .]+)/is',
                $contents,
                $matrizNomeVeiculo
            );

            preg_match_all(
                '/inner">[\n\r]*<a href="(https:\/\/www.questmultimarcas.com.br\/carros\/[a-z]+\/[\w\-\d]+\/[0-9]+\/[0-9]+)">/is',
                $contents,
                $matrizLink
            );

            preg_match_all(
                '/<a href="https:\/\/[\w\d\.]*\/carros\/[a-z]+\/[\w\-]+\/([0-9]+)\/[0-9]+">[a-z A-Z 0-9 .]+/is',
                $contents,
                $matrizAno
            );

            preg_match_all(
                '/Combustível:\s<\/span>(\n|\r)\s+<span class="card-list__info">((\n|\r)\s+)([a-z A-Z 0-9]+)/is',
                $contents,
                $matrizCombustivel
            );

            preg_match_all(
                '/Câmbio:\s<\/span>(\n|\r)\s+<span class="card-list__info">((\n|\r)\s+)([a-z A-Z 0-9 á Á]+)/is',
                $contents,
                $matrizCambio
            );

            preg_match_all(
                '/Portas:\s<\/span>(\n|\r)\s+<span class="card-list__info">((\n|\r)\s+)([a-z A-Z 0-9 á Á]+)/is',
                $contents,
                $matrizPorta
            );

            preg_match_all(
                '/Quilometragem:\s<\/span>(\n|\r)\s+<span class="card-list__info">((\n|\r)\s+)([a-z A-Z 0-9 á Á.]+)/is',
                $contents,
                $matrizQuilometragem
            );

            preg_match_all(
                '/Cor:\s<\/span>(\n|\r)\s+<span class="card-list__info">((\n|\r)\s+)([a-z A-Z 0-9 á Á.]+)/is',
                $contents,
                $matrizCor
            );

            if (count($matrizNomeVeiculo[1]) == 0) { //esse if ficaria logo no ínicio
                return response()->json([
                    'data' => [
                        'message' => 'Nenhum carro foi encontrado!',
                        'status' => 200
                    ]
                ], 200);
            }
            else {
                $numberCars = count($matrizNomeVeiculo[1]);
                for ($i = 0; $i < $numberCars; $i++) {
                    $this->car->create([
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
                ], 201);
            }
        }
        catch (\Exception $e) {
            //talvez usar a Exception
            //talvez criar umas Exceptions
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
        $car = $this->car::find($id);

        //criar um middleware para fazer essa verificação?
        if ($car->user_id == auth()->user()->getAuthIdentifier()) {
            $car->delete();
        }

        return redirect("/index");
    }
}
