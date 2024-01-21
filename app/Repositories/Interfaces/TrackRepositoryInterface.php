<?php

namespace App\Repositories\Interfaces;

use App\Http\Requests\AddTrackRequest;
use App\Http\Requests\DeleteManyRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

interface TrackRepositoryInterface extends BaseRepositoryInterface
{
    function getTrack(string $id, Request $request);
    function popular();
    function deleteMany(DeleteManyRequest $request);
}
