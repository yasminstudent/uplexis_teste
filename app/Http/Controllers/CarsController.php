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

        $capture = $captureService->capture($term, $client, $this->car);

        $cars = [];
        $numberCars = count($capture["names"][1]);
        for (
            $i = 0;
            $i < $numberCars;
            $i++
        ) {
            $cars[] = [
                'user_id' => $this->userId,
                'nome_veiculo' =>  $capture[$this->car::NAME][1][$i],
                'link' => $capture[$this->car::LINK][1][$i],
                'ano' => $capture[$this->car::YEAR][1][$i],
                'combustivel' => $capture[$this->car::FUEL][4][$i],
                'portas' => $capture[$this->car::DOOR][4][$i],
                'quilometragem' => $capture[$this->car::MILEAGE][4][$i],
                'cambio' => $capture[$this->car::GEARBOX][4][$i],
                'cor' => $capture[$this->car::COLOR][4][$i]
            ];
        }

        $this->car->createMany($cars);
        
        //criar uma interface para cars model
        //criar em database um usuário já

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
    public function destroy($id) :\Illuminate\Http\RedirectResponse 
    {
        $car = $this->car::find($id);

        //criar um middleware para fazer essa verificação?
        if ($car->user_id == $this->userId) {
            $car->delete();
        }

        return redirect("/index");
    }
}
