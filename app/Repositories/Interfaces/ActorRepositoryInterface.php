<?php

namespace App\Repositories\Interfaces;

use App\Http\Requests\LikeTrackRequest;

interface ActorRepositoryInterface
{
    function show();
    function showLiked();
    function like(LikeTrackRequest $request);
    function removeFromLiked(string $id);
    function followArtist(string $artistId);
    function unfollowArtist(string $artistId);
    function showPlaylists();
    function recommendArtists();
    function recommendTracks();
    function favoriteTracksInLast7Days();
    function recentlyPlayed();
}
