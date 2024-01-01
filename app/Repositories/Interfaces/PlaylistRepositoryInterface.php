<?php

namespace App\Repositories\Interfaces;

use App\Http\Requests\AddTracksToPlaylistRequest;

interface PlaylistRepositoryInterface extends BaseRepositoryInterface
{
    function addTracks(AddTracksToPlaylistRequest $request, string $id);
    function deleteTrack( string $playlist, string $track, string $pivotId);
    function getTracks(string $playlist);
}
