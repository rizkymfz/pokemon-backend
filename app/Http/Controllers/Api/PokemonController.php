<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\PokemonResource;
use App\Services\ApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PokemonController extends Controller
{
    private $apiService;

    public function __construct(ApiService $apiService) {
        $this->apiService = $apiService;
    }

    /**
     * Get Pokemon Lists
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $data   = $this->apiService->getPokemon($request);

        if ($request->has('keyword')) {
            $collect = collect($data);
            $data    = $collect->filter(function($item) use ($request) {
                return stripos($item->name, $request->keyword) !== false;
            });
        }

        return $this->success(PokemonResource::collection($data));
    }

    /**
     * Detail Pokemon
     *
     * @param [name] $param
     * @return JsonResponse
     */
    public function detail($id)
    {
        $data   = $this->apiService->detailPokemon($id);
        

        return $this->success(new PokemonResource($data));
    }
}
