<?php

namespace App\Repositories\Implementations;

use App\Http\Requests\AddTracksToPlaylistRequest;
use App\Http\Requests\StorePlaylistRequest;
use App\Models\Playlist;
use App\Models\Track;
use App\Models\TrackPlaylist;
use App\Repositories\Interfaces\PlaylistRepositoryInterface;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EloquentPlaylistRepository implements PlaylistRepositoryInterface
{

    function getAll()
    {
        return Playlist::withCount('tracks')->get();
    }

    function show(string $id)
    {
        $playlist = Playlist::withCount('tracks')->find($id);
        $paginatedTracks = $playlist->tracks()->with(['owner', 'features', 'album'])->withCount('trackPlays')->paginate(50);
        if($playlist == null) return response()->json(['message' => 'No playlist has been found.'])->setStatusCode(404);
        $playlist->tracks = $paginatedTracks;
        $latestAdded = DB::table('track_playlist')
                        ->where('playlist_id', $playlist->id)
                        ->orderByDesc('created_at')->first();
        if($latestAdded) {
            $playlist->latest_added = $latestAdded->created_at;
        }
        else{
            $playlist->latest_added = $playlist->created_at;
        }

        return response()->json($playlist);
    }

    function store(StorePlaylistRequest|FormRequest $request)
    {
        $playlist = new Playlist();

        $title = $request->validated('title');
        $description = $request->validated('description');
        $playlist->title = $title;

        if ($request->hasFile('image'))
        {
            $file      = $request->file('image');
            $filename  = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            // $picture   = date('His').'-'.$filename;
            $imageName = time() . '.' . $file->getClientOriginalExtension();
            $user = \auth()->user()->getAuthIdentifier();
            $file->move("F:\WebStorm Projects\zavrsniAng\src\assets\images\users\\$user\playlists", $imageName);
            $playlist->image_url = 'assets/images/users/'.auth()->user()->getAuthIdentifier().'/playlists/'.$imageName;
        }


        if($description) {
            $playlist->description = $description;
        }

        if(Auth::hasUser()){
            $playlist->actor_id = Auth::user()->getAuthIdentifier();
            $playlist->save();
            return response()->json($playlist)->setStatusCode(201);
        }



    }

    function update(StorePlaylistRequest|FormRequest $request, string $id)
    {
        $playlist = Playlist::find($id);
        if($playlist == null) return response()->json(['message' => 'No playlist has been found.']);

        $newTitle = $request->validated('title');

        $playlist->title = $newTitle;

        $playlist->save();

        return response()->json()->setStatusCode(204);
    }

    function delete(string $id)
    {
        $playlist = Playlist::withCount('tracks')->find($id);

        if($playlist == null)
            return response()->json(['message' => 'No playlist has been found.'])->setStatusCode(404);

        if($playlist->tracks_count > 0)
            return response()->json(['message' => 'Cannot delete playlist that contains tracks.'])->setStatusCode(409);

        $playlist->delete();
        $playlist->save();

        return response()->json()->setStatusCode(204);
    }

    function addTracks(AddTracksToPlaylistRequest|FormRequest $request, string $id)
    {
        $confirm = $request->get('confirm');

        $playlist = Playlist::with('tracks')->find($id);

        $tracks = $request->validated('tracks');

        $tracksExist = Track::whereIn('id', $tracks);

        if(!$tracksExist){
            return response()->json(['message' => 'Track does not exist.']);
        }

        if($confirm) {
            try {
                DB::beginTransaction();

                foreach ($tracks as $track) {
                    TrackPlaylist::create([
                        'track_id' => $track,
                        'playlist_id' => $playlist->id
                    ]);
                }

                DB::commit();
                return response()->json(
                    [
                        'status' => 'success',
                        'playlistId' => $id,
                        'addedCount' => count($tracks),
                        'message' => 'Added to \''.$playlist->title.'\'',
                    ])->setStatusCode(201);
            }
            catch (\Exception $exception) {
                DB::rollBack();
            }
        }
        $tracksAlreadyInPlaylist = $playlist->tracks()->findMany($tracks)->pluck('id')->unique()->toArray();
        if(count($tracksAlreadyInPlaylist) == 0) {
            try {
                DB::beginTransaction();

                foreach ($tracks as $track) {
                    TrackPlaylist::create([
                        'track_id' => $track,
                        'playlist_id' => $playlist->id
                    ]);
                }

                DB::commit();
                return response()->json(
                    [
                        'status' => 'success',
                        'playlistId' => $id,
                        'addedCount' => count($tracks),
                        'message' => 'Added to \''.$playlist->title.'\'',
                    ])->setStatusCode(201);
            }
            catch (\Exception $exception) {
                DB::rollBack();
            }
        }
        if (count($tracksAlreadyInPlaylist) < count($tracks)) {

            return response()->json(
                [
                    'actions' => ['Add all', 'Add new ones'],
                    'status' => 'warning-some',
                    'playlistId' => $id,
                    'tracksAlreadyInPlaylist' => $tracksAlreadyInPlaylist,
                    'allTracksIds' => $tracks,
                    'message' => 'Some already added',
                    'content' => 'Some of these are already in your \''.$playlist->title.'\' playlist.'
                ])
                ->setStatusCode(422);
        }
        if (count($tracksAlreadyInPlaylist) == count($tracks)) {
            if(count($tracks) === 1) {
                return response()->json(
                    [
                        'actions' => ['Add anyway', 'Don\'t add'],
                        'status' => 'warning-all',
                        'playlistId' => $id,
                        'tracksAlreadyInPlaylist' => $tracksAlreadyInPlaylist,
                        'content' => 'This track is already in your \''.$playlist->title.'\' playlist.',
                        'message' => 'Already added'
                    ])
                    ->setStatusCode(422);
            }
            return response()->json(
                [
                    'actions' => ['Add anyway', 'Don\'t add'],
                    'status' => 'warning-all',
                    'playlistId' => $id,
                    'tracksAlreadyInPlaylist' => $tracksAlreadyInPlaylist,
                    'content' => 'These are already in your \''.$playlist->title.'\' playlist.',
                    'message' => 'Already added'
                ])
                ->setStatusCode(422);
        }
    }

    function deleteTrack(string $id, string $track, string $pivotId)
    {
        if(!Str::isUuid($id) || !Str::isUuid($track) || !Str::isUuid($pivotId)){
            return response()->json(['message' => 'Unexpected error..'])->setStatusCode(409);
        }

        $playlist = Playlist::find($id);

        if($playlist == null) {
            return response()->json(['message' => 'No playlist has been found.'])->setStatusCode(404);
        }

        $pivotRowToDelete = TrackPlaylist::where(['track_id' => $track, 'playlist_id' => $id])->findOrFail($pivotId);

        if($pivotRowToDelete) {
           $pivotRowToDelete->delete();
            return response()->json(['message' => 'You have successfully removed track from playlist.'])->setStatusCode(204);
        }
        return response()->json($pivotRowToDelete);

        $trackToDelete = $playlist->tracks()->findOrFail($track);

        if(!$trackToDelete){
            return response()->json(['message' => 'Track does not exist in this playlist.'])->setStatusCode(404);
        }
        $playlist->tracks()->detach($trackToDelete);
        return response()->json(['message' => 'You have successfully removed track from playlist.'])->setStatusCode(204);
    }

    function getTracks(string $playlist)
    {
        $playlist = Playlist::findOrFail($playlist);

        if($playlist) {
            $tracks = $playlist->tracks()->with(['owner', 'features', 'album'])->withCount('trackPlays')->get();

            return response()->json(['tracks' => $tracks]);
        }
    }
}
