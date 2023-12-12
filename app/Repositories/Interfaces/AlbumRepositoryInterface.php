<?php

namespace App\Repositories\Interfaces;

interface AlbumRepositoryInterface extends BaseRepositoryInterface
{
    function getLatest();
    function popular();
}
