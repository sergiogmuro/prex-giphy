<?php

namespace App\Services;

use App\Interfaces\GifInterfaceService;
use App\Models\FavoriteGif;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GiphyService implements GifInterfaceService
{
    private const BASE_API_URL = 'https://api.giphy.com/v1/gifs';
    private const SEARCH = '/search';
    private const GET_BY_ID = '/%s';
    private const CACHE_TTL = 3600;

    /**
     * @param string $path
     * @param array $params
     *
     * @return Response
     */
    private function makeRequest(string $path, array $params = []): Response
    {
        $body = [
            'api_key' => config('giphy.api_key'),
            ...$params
        ];

        return Http::get(self::BASE_API_URL . $path, $body);
    }

    /**
     * @param string $query
     * @param int $limit
     * @param int $offset
     *
     * @return Collection{data: array, status: integer}
     */
    public function search(string $query, int $limit, int $offset): Collection
    {
        $cacheKey = 'search_' . serialize([$query, $limit, $offset]);

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $requestResponse = $this->makeRequest(self::SEARCH, [
            'q' => $query,
            'limit' => $limit,
            'offset' => $offset,
        ]);

        $data = collect($requestResponse->json()['data'] ?? []);

        $response = collect([
            'data' => $data,
            'status' => $requestResponse->status()
        ]);

        Cache::set($cacheKey, $response, self::CACHE_TTL);

        return $response;
    }

    /**
     * @param string $id
     *
     * @return Collection
     */
    public function getGifById(string $id): Collection
    {
        $cacheKey = 'get_id_' . $id;

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $requestResponse = $this->makeRequest(sprintf(self::GET_BY_ID, $id));

        $data = collect($requestResponse->json()['data'] ?? []);

        $response = collect([
            'data' => $data,
            'status' => $requestResponse->status()
        ]);

        Cache::set($cacheKey, $response, self::CACHE_TTL);

        return $response;
    }

    /**
     * @param int $userId
     * @param string $gifId
     * @param string $alias
     *
     * @return FavoriteGif
     */
    public function storeFavorite(int $userId, string $gifId, string $alias): FavoriteGif
    {
        return FavoriteGif::updateOrCreate([
            'user_id' => $userId,
            'gif_id' => $gifId,
        ], [
            'user_id' => $userId,
            'gif_id' => $gifId,
            'alias' => $alias,
        ]);
    }
}
