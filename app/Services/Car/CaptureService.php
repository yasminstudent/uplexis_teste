<?php

namespace App\Services\Car;

use GuzzleHttp\Client;
use App\Models\Cars as CarsModel;

class CaptureService extends Controller
{
    public function capture(string $term, Client $client, CarsModel $car)
    {
        try {
            if (empty($term)) {
                //retornar execeção de termo vazio
            }

            $response = $client->request(
                "GET", 
                "https://www.questmultimarcas.com.br/estoque?termo={$term}"
            );
            $contents = $response->getBody()->getContents();

            $regexs = $this->getRegexs($car);
            $matrix = [];

            foreach ($regexs as $key => $regex) {
                preg_match_all(
                    $regex,
                    $contents,
                    $matrix[$key]
                );

                //executa somente na primeira vez
                if (count($matrix) == 1 && count($matrix[$key][1]) == 0) {
                    //retornar execeção de de nenhum carro encontrado
                }
            }

            return $matrix;
        } catch (\Exception $e) {
            // retornar execeção
        }
    }

    private function getRegexs(CarsModel $car)
    {
        return [
            $car::NAME => '/<a href="https:\/\/[\w\d\.]*\/carros\/[a-z]+\/[\w\-]+\/[0-9]+\/[0-9]+">([a-z A-Z 0-9 .]+)/is',
            $car::LINK => '/inner">[\n\r]*<a href="(https:\/\/www.questmultimarcas.com.br\/carros\/[a-z]+\/[\w\-\d]+\/[0-9]+\/[0-9]+)">/is',
            $car::YEAR => '/<a href="https:\/\/[\w\d\.]*\/carros\/[a-z]+\/[\w\-]+\/([0-9]+)\/[0-9]+">[a-z A-Z 0-9 .]+/is',
            $car::FUEL => '/Combustível:\s<\/span>(\n|\r)\s+<span class="card-list__info">((\n|\r)\s+)([a-z A-Z 0-9]+)/is',
            $car::GEARBOX => '/Câmbio:\s<\/span>(\n|\r)\s+<span class="card-list__info">((\n|\r)\s+)([a-z A-Z 0-9 á Á]+)/is',
            $car::DOOR => '/Portas:\s<\/span>(\n|\r)\s+<span class="card-list__info">((\n|\r)\s+)([a-z A-Z 0-9 á Á]+)/is',
            $car::COLOR => '/Cor:\s<\/span>(\n|\r)\s+<span class="card-list__info">((\n|\r)\s+)([a-z A-Z 0-9 á Á.]+)/is',
            $car::MILEAGE => '/Quilometragem:\s<\/span>(\n|\r)\s+<span class="card-list__info">((\n|\r)\s+)([a-z A-Z 0-9 á Á.]+)/is',
        ];       
    }
}
