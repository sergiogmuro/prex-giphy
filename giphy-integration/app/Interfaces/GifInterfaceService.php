<?php

namespace App\Interfaces;

use App\Models\FavoriteGif;
use Illuminate\Support\Collection;

interface GifInterfaceService
{
    /**
     * @param string $query
     * @param int $limit
     * @param int $offset
     *
     * @return Collection{data:Collection, status: integer}
     */
    function search(string $query, int $limit, int $offset): Collection;

    /**
     * @param string $id
     *
     * @return Collection{data:Collection, status: integer}
     */
    function getGifById(string $id): Collection;

    function storeFavorite(int $userId, string $gifId, string $alias): FavoriteGif;
}
