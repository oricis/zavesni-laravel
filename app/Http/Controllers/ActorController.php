<?php

namespace App\Http\Controllers;

use App\Http\Requests\LikeTrackRequest;
use App\Models\Actor;
use App\Models\Track;
use App\Repositories\Interfaces\ActorRepositoryInterface;
use Illuminate\Http\Request;

class ActorController extends Controller
{
    private ActorRepositoryInterface $actorRepository;

    public function __construct(ActorRepositoryInterface $actorRepository){
        $this->actorRepository = $actorRepository;
    }
    public function show()
    {
        return $this->actorRepository->show();
    }
    public function showPlaylists() {
        return $this->actorRepository->showPlaylists();
    }
    public function showLiked()
    {
        return $this->actorRepository->showLiked();
    }
    public function like(LikeTrackRequest $request)
    {
        return $this->actorRepository->like($request);
    }
    public function removeFromLiked(string $id) {
        return $this->actorRepository->removeFromLiked($id);
    }

    public function followArtist(string $artistId) {
        return $this->actorRepository->followArtist($artistId);
    }
    public function unfollowArtist(string $artistId) {
        return $this->actorRepository->unfollowArtist($artistId);
    }
    public function recommendArtists() {
        return $this->actorRepository->recommendArtists();
    }
    public function recommendTracks() {
        return $this->actorRepository->recommendTracks();
    }
    public function favoriteTracksInLast7Days() {
        return $this->actorRepository->favoriteTracksInLast7Days();
    }

    public function recentlyPlayed() {
        return $this->actorRepository->recentlyPlayed();
    }
}
