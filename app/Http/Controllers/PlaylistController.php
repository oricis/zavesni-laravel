<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddTracksToPlaylistRequest;
use App\Http\Requests\StorePlaylistRequest;
use App\Models\Playlist;
use App\Repositories\Interfaces\PlaylistRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class PlaylistController extends Controller
{
    private PlaylistRepositoryInterface $playlistRepository;

    public function __construct(PlaylistRepositoryInterface $playlistRepository)
    {
        $this->playlistRepository = $playlistRepository;
    }

    public function index()
    {
        return $this->playlistRepository->getAll();
    }

    public function store(StorePlaylistRequest $request)
    {
        return $this->playlistRepository->store($request);
    }

    public function show(string $id)
    {
        return $this->playlistRepository->show($id);
    }

    public function update(StorePlaylistRequest $request, string $id)
    {
        return $this->playlistRepository->update($request, $id);
    }

    public function destroy(string $id)
    {
        return $this->playlistRepository->delete($id);
    }

    public function addTracks(AddTracksToPlaylistRequest $request, string $id){
        return $this->playlistRepository->addTracks($request, $id);
    }

    public function deleteTrack(string $id, string $track){
        return $this->playlistRepository->deleteTrack($id, $track);
    }
    public function getTracks(string $playlist) {
        return $this->playlistRepository->getTracks($playlist);
    }
}
