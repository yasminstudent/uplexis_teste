<?php

namespace App\Services\Car;

use GuzzleHttp\Client;
use App\Models\Cars as CarsModel;
use App\Models\Users as UsersModel;

class CaptureService extends Controller
{
    public function capture(string $term, Client $client)
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

            $regexs = $this->getRegexs();
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

    private function getRegexs()
    {
        return [
            "names" => '/<a href="https:\/\/[\w\d\.]*\/carros\/[a-z]+\/[\w\-]+\/[0-9]+\/[0-9]+">([a-z A-Z 0-9 .]+)/is',
            "links" => '/inner">[\n\r]*<a href="(https:\/\/www.questmultimarcas.com.br\/carros\/[a-z]+\/[\w\-\d]+\/[0-9]+\/[0-9]+)">/is',
            "years" => '/<a href="https:\/\/[\w\d\.]*\/carros\/[a-z]+\/[\w\-]+\/([0-9]+)\/[0-9]+">[a-z A-Z 0-9 .]+/is',
            "fuels" => '/Combustível:\s<\/span>(\n|\r)\s+<span class="card-list__info">((\n|\r)\s+)([a-z A-Z 0-9]+)/is',
            "carGearbox" => '/Câmbio:\s<\/span>(\n|\r)\s+<span class="card-list__info">((\n|\r)\s+)([a-z A-Z 0-9 á Á]+)/is',
            "doors" => '/Portas:\s<\/span>(\n|\r)\s+<span class="card-list__info">((\n|\r)\s+)([a-z A-Z 0-9 á Á]+)/is',
            "colors" => '/Cor:\s<\/span>(\n|\r)\s+<span class="card-list__info">((\n|\r)\s+)([a-z A-Z 0-9 á Á.]+)/is',
            "mileage" => '/Quilometragem:\s<\/span>(\n|\r)\s+<span class="card-list__info">((\n|\r)\s+)([a-z A-Z 0-9 á Á.]+)/is',
        ];       
    }
}
