<?php

namespace App\Repositories\Implementations;

use App\Http\Requests\StoreAlbumRequest;
use App\Models\Album;
use App\Models\TrackPlay;
use App\Repositories\Interfaces\AlbumRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class EloquentAlbumRepository implements AlbumRepositoryInterface
{

    function getAll()
    {
        Log::info('Showing all albums. Ip address:',['ip' => request()->ip()]);
        return Album::with('tracks')->withCount('tracks')->paginate(10);
    }

    function show(string $id)
    {
        if(!uuid_is_valid($id)){
            return response()->json(['message' => 'No album has been found.']);
        }
        $album = Album::with('tracks.album')->with('tracks.owner')->with('artist')->with('tracks.features')->withCount('tracks')->with('artist')->find($id);

        if($album == null)
            return response()->json(['message' => 'No album has been found.']);

        return response()->json($album);
    }

    function store(StoreAlbumRequest|FormRequest $request)
    {
        $validatedName = $request->validated('name');
        $validatedArtist = $request->validated('artist');

        $album = new Album();
        $album->name = $validatedName;
        $album->artist_id = $validatedArtist;

        $album->save();

        return response()->json(['message' => 'You have successfully created a new album.'], 201);
    }

    function update(StoreAlbumRequest|FormRequest $request, string $id)
    {
        // TODO: Implement update() method.
    }

    function delete(string $id)
    {
        // TODO: Implement delete() method.
    }

    function getLatest()
    {
        $latest = Album::orderByDesc('created_at')->take(6)->get();

        return response()->json($latest);
    }

    function popular()
    {
        $now = Carbon::now();
        $sevenDays = $now->copy()->subDays(7);

        $popularLastSevenDays = TrackPlay::select('tracks.album_id')
            ->join('tracks', 'track_plays.track_id', '=', 'tracks.id')
            ->whereBetween('track_plays.created_at', [$sevenDays, $now])
            ->groupBy('tracks.album_id')
            ->havingRaw('COUNT(tracks.album_id) > 1')
            ->orderByDesc(\DB::raw('COUNT(tracks.album_id)'))
            ->take(6);

        $popularAlbums = Album::with(['artist']) // Adjust the relationships as needed
        ->whereIn('id', $popularLastSevenDays)
            ->get();

        return response()->json($popularAlbums);
    }
}
