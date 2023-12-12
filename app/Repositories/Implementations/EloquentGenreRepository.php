<?php

namespace App\Repositories\Implementations;

use App\Http\Requests\StoreGenreRequest;
use App\Models\Genre;
use App\Models\Playlist;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use League\ColorExtractor\Client;
class EloquentGenreRepository implements \App\Repositories\Interfaces\GenreRepositoryInterface
{

    function getAll()
    {
        return Genre::all();
    }

    function show(string $id)
    {
        $playlists = Playlist::where('genre_id', $id)->take(5)->get();
        $genre = Genre::with('tracks.owner', 'tracks.features', 'tracks.album')->findOrFail($id);
        return response()->json(['playlists' => $playlists, 'genre' => $genre]);
        /*$genre = Genre::with('tracks.owner')->with('tracks.album')->with('tracks.features')->find($id);
        if($genre == null){
            return response(['message' => 'No genre has been found.'], 400);
        }

        return response($genre);*/
    }

    function store(StoreGenreRequest|FormRequest $request)
    {
        $genre = new Genre();

        $validated = $request->validated();

        $genre->name = $validated['name'];

        $genre->save();

        return response(['message' => 'You have successfully created a new genre.'], 201);
    }

    function update(StoreGenreRequest|FormRequest $request, string $id)
    {
        $validated = $request->validated();
        $genre = Genre::find($id);

        if($genre == null){
            return response(['message'=> 'No genre has been found.'], 200);
        }

        $genre->name = $validated['name'];
        $genre->save();

        return response('', 204);
    }

    function delete(string $id)
    {
        $genre = Genre::find($id);

        if($genre == null){
            return response(['message' => 'No genre has been found.']);
        }
        $genre->delete()->save();

        return response('', 204);
    }
}
