<?php

namespace App\Repositories\Implementations;

use App\DTOs\ArtistDTO;
use App\DTOs\TrackDTO;
use App\Http\Requests\StoreArtistRequest;
use App\Models\Album;
use App\Models\Artist;
use App\Models\Track;
use App\Models\TrackPlay;
use App\Repositories\Interfaces\ArtistRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Http\FormRequest;

class EloquentArtistRepository implements ArtistRepositoryInterface
{

    function getAll()
    {
        $artists = Artist::with('albums')->get();

        return response()->json($artists);
    }
    function showAlbums(string $id) {
        try{
            $albums = Album::where('artist_id', $id)->get();
            return response()->json($albums)->setStatusCode(200);
        }
        catch (ModelNotFoundException) {
            return response()->json(['message' => 'Album not found.'], 404);
        }
    }
    function showPopular(string $id) {
        try{
            $popularTracks = Artist::with([
                'ownTracks.features',
                'ownTracks.owner',
                'ownTracks.album',
                'ownTracks' => function (Builder $query) {
                    $query->orderByDesc('plays')->take(10);
                }
            ])->findOrFail($id);

            return response()->json($popularTracks)->setStatusCode(200);
        }
        catch (ModelNotFoundException $exception) {
            return response()->json(['message' => 'Artist not found.'], 404);
        }
    }
    function showFeatures(string $id) {
        try{
            $features = Artist::with('featureTracks.owner')
                    ->with('featureTracks.album')
                    ->with('featureTracks.features')
                ->findOrFail($id);

            return response()->json($features)->setStatusCode(200);
        }
        catch (ModelNotFoundException) {
            return response()->json(['message' => 'Features not found.'], 404);
        }
    }
    function showSingles(string $id) {
        try{
            $features = Artist::with('singles.owner')
                ->findOrFail($id);

            return response()->json($features)->setStatusCode(200);
        }
        catch (ModelNotFoundException) {
            return response()->json(['message' => 'Features not found.'], 404);
        }
    }
    function show(string $id)
    {
        try{
            $artist = Artist::with([
                'ownTracks' => function($query) {
                    $query->with(['features', 'owner', 'album'])->orderByDesc('plays')->take(5);
                },
                'featureTracks' => function($query) {
                    $query->with(['owner', 'album', 'features']);
                },
                'albums' => function($query) {
                    $query->with(['artist','tracks' => function($subQuery) {
                        $subQuery->with(['owner', 'album', 'features']);
                    }])->withCount('tracks');
                }
            ])
                ->withCount(['albums', 'featureTracks', 'ownTracks'])
                ->findOrFail($id);
            $artist->featured_albums = $artist->featureTracks->pluck('album')->filter()->unique()
                ->each(function ($album) use ($artist) {
                    $album->loadCount('tracks');
                })->values()->toArray();
          //  if($artist == null) return response()->json(['message' => 'No artist has been found.'])->setStatusCode(200);

            return response()->json($artist)->setStatusCode(200);
        }
        catch (ModelNotFoundException $exception){
            return response()->json(['message' => 'Artist not found.'], 404);
        }

    }

    function store(StoreArtistRequest|FormRequest $request)
    {
        $artist = new Artist();
        $newArtistName = $request->validated('name');

        $artist->name = $newArtistName;
        $artist->save();

        return response()->json(['message' => 'You have successfully created a new artist.'])->setStatusCode(201);
    }

    function update(StoreArtistRequest|FormRequest $request, string $id)
    {
        $artist = Artist::find($id);

        if($artist == null) return response()->json(['message' => 'No artist has been found.']);

        $newArtistName = $request->validated('name');

        $artist->name = $newArtistName;
        $artist->save();

        return response()->json()->setStatusCode(204);


    }

    function delete(string $id)
    {
        $artist = Artist::find($id);

        if($artist == null) return response()->json(['message' => 'No artist has been found.']);

        $artist->delete();
        $artist->save();

        return response('', 204);
    }
    function popular() {
        $now = Carbon::now();
        $sevenDays = $now->copy()->subDays(7);

        $popularArtists = Artist::select('artists.id', 'artists.name', 'artists.cover')
            ->join('tracks', 'artists.id', '=', 'tracks.owner_id')
            ->join('track_plays', 'tracks.id', '=', 'track_plays.track_id')
            ->whereBetween('track_plays.created_at', [$sevenDays, $now])
            ->groupBy('artists.id', 'artists.name', 'artists.cover')
            ->orderByRaw('COUNT(track_plays.id) DESC')->withCount('followedBy')->take(9)->get();

        return response()->json($popularArtists);
    }
}
