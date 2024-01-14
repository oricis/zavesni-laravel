<?php

namespace App\Repositories\Interfaces;

use Illuminate\Http\Request;

interface TrackRepositoryInterface extends BaseRepositoryInterface
{
    function getTrack(string $id, Request $request);
    function popular();
    function deleteMany(string $ids);
}
