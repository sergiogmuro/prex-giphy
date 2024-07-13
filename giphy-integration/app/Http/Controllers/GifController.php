<?php

namespace App\Http\Controllers;

use App\Interfaces\GifInterfaceService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GifController extends Controller
{
    private GifInterfaceService $service;

    public function __construct(GifInterfaceService $service)
    {
        $this->service = $service;
    }

    public function searchGifs(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'query' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->errors()
                ], 400);
            }

            $query = $request->input('query');
            $limit = $request->input('limit', 25);
            $offset = $request->input('offset', 0);

            $response = $this->service->search($query, $limit, $offset);

            return response()->json($response, $response->get('status'));
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'status' => 'error'
            ], $e->getCode());
        }
    }

    public function getGifById(string $gifId): JsonResponse
    {
        try {
            if (empty($gifId)) {
                throw new Exception('Gif ID not set', 400);
            }

            $response = $this->service->getGifById($gifId);

            return response()->json($response, $response->get('status'));
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'status' => 'error'
            ], $e->getCode());
        }
    }

    public function saveFavoriteGif(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer',
                'gif_id' => 'required|string',
                'alias' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->errors()
                ], 400);
            }

            $userId = $request->input('user_id');
            $gifId = $request->input('gif_id');
            $alias = $request->input('alias');

            $favoriteGif = $this->service->storeFavorite($userId, $gifId, $alias);

            return response()->json([
                'payload' => $favoriteGif,
                'status' => 'ok'
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'status' => 'error'
            ], $e->getCode());
        }
    }
}
