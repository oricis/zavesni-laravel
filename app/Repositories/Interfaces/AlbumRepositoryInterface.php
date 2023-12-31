<?php

namespace App\Repositories\Interfaces;

interface AlbumRepositoryInterface extends BaseRepositoryInterface
{
    function getLatest();
    function popular();
    function like(string $id);
    function removeFromLiked(string $id);
}
