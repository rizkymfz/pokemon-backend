<?php

namespace App\Services;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;

class ApiService 
{
    private $client;
    private $baseUrl;

    public function __construct() {
        $this->baseUrl = "https://pokeapi.co";
        $this->client  = new Client([
            'base_uri'        => $this->baseUrl,
            'connect_timeout' => 45,
            'headers' => [
                'Accept' => 'application/json'
            ]
        ]);
    }

    public function getPokemon($request)
    {
        try {
            
            $response = $this->client->get('/api/v2/pokemon', [
                'query'   => [
                    'limit' => $request->limit ?? 10
                ]
            ]);
            $result = $this->parseResponse($response);

            $pokemons = [];
            foreach ($result->results as $key => $item) {
                $call    = Cache::remember("get-pokemon-$key", 60, function() use ($item) {
                    return Http::get($item->url)->object();
                });
                $details = $call;
                $pokemons[] = [
                    'id'        => $details->id,
                    'name'      => $details->name,
                    'sprites'   => $details->sprites,
                    'abilities' => $details->abilities
                ];
            }

            return json_decode(json_encode($pokemons), false);
        } catch (ClientException $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function detailPokemon($id)
    {
        try {
            $response = Cache::remember("detail-pokemon-$id", 60, function() use ($id) {
                return $this->parseResponse(
                    $this->client->get('/api/v2/pokemon/' . $id)
                );
            });

            return $response;
        } catch (ClientException $e) {
            throw new Exception($e->getMessage());
        }
    }

    private function parseResponse(ResponseInterface $res)
    {
        return json_decode($res->getBody()->getContents());
    }
}