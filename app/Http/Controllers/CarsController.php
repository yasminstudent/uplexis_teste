<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\Cars as CarsModel;
use App\Models\Users as UsersModel;
use App\Services\Car\CaptureService;

class CarsController extends Controller
{
    private $car;
    private $user;
    private $userId;

    public function __construct(CarsModel $car, UsersModel $user)
    {
        $this->car = $car;
        $this->user = $user;
        $this->userId = auth()->user()->getAuthIdentifier();
    }

    /**
     * Lista todos os carros relacionados ao usuário.
     */
    public function index() :\Illuminate\View\View
    {
        $user = $this->user::find($this->userId);

        return view(
            'index', 
            ['cars' => $user->cars]
        );
    }

    /**
     * Busca carros e cadastra no banco.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $term = urlencode($request->only(["term"]));
        $client = new Client();
        $captureService = new CaptureService();

        $capture = $captureService->capture($term, $client);

        $cars = [];
        $numberCars = count($capture["names"][1]);
        for (
            $i = 0;
            $i < $numberCars;
            $i++
        ) {
            $cars[] = [
                'user_id' => $this->userId,
                'nome_veiculo' =>  $capture["names"][1][$i],
                'link' => $capture["links"][1][$i],
                'ano' => $capture["years"][1][$i],
                'combustivel' => $capture["fuels"][4][$i],
                'portas' => $capture["doors"][4][$i],
                'quilometragem' => $capture["mileage"][4][$i],
                'cambio' => $capture["carGearbox"][4][$i],
                'cor' => $capture["colors"][4][$i]
            ];
        }

        $this->car->createMany($cars);
        
        //criar em config ou em outro lugar os nomes names, links etc

        return response()->json([
            'data' => [
                'message' => 'Carro(s) cadastrado(s) com sucesso!',
                'status' => 201
            ]
        ], 201);
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
        if ($car->user_id == $this->userId) {
            $car->delete();
        }

        return redirect("/index");
    }
}
