<?php

namespace App\Repositories\Interfaces;

use App\Http\Requests\DeleteManyRequest;

interface AlbumRepositoryInterface extends BaseRepositoryInterface
{
    function getLatest();
    function popular();
    function like(string $id);
    function removeFromLiked(string $id);
    function deleteMany(DeleteManyRequest $request);
}
