<?php

namespace App\Repositories\Implementations;

use App\Http\Requests\AddTrackRequest;
use App\Models\Actor;
use App\Models\LikedTrack;
use App\Models\Track;
use App\Models\TrackPlay;
use App\Models\TrackPlaylist;
use App\Repositories\Interfaces\TrackRepositoryInterface;
use Bugsnag\BugsnagLaravel\Facades\Bugsnag;
use Carbon\Carbon;
use getID3;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use wapmorgan\Mp3Info\Mp3Info;

class EloquentTrackRepository implements TrackRepositoryInterface
{

    function getAll()
    {
        return response()->json(
            Track::with('owner')
            ->with('features')
            ->with('album')
            ->withCount('likes')->paginate(10));
    }

    function show(string $id)
    {
        $path = storage_path('app/audio/9A6324BF-012E-4AAD-A978-2A544BBCD420.mp3');

        if(file_exists($path)){
            $size = filesize($path);
            $track = Track::with('owner')
                ->with('features')
                ->with('album.tracks')
                ->with('genre')
                ->withCount('likes')->findOrFail($id);

            $headers = [
                'Content-Type' => 'audio/mpeg',
                'Content-Length' => $size / 2,
                'Range' => "",
                'Accept-Ranges' => 'bytes',
            ];

            return response()->stream(function () use ($path,$track, $size) {
                $stream = fopen($path, 'r');
                fseek($stream, 0);
                $length =  $size / 2;
                echo fread($stream, $length);
                fclose($stream);
            }, 206, $headers);
        } else {
            abort(404);
        }
        try {
            $track = Track::with('owner')
                ->with('features')
                ->with('album.tracks')
                ->with('genre')
                ->withCount('likes')->findOrFail($id);

            return response()->json(['track' => $track]);
        }
        catch (ModelNotFoundException $exception){
            $user = "Anonymous";
            if(Auth::hasUser()){
                $user = Auth::user()->email;
            }
            Bugsnag::notifyException(new ModelNotFoundException("User: $user, Tried searching for a track that does not exists."));
            return response()->json(['message' => 'Track not found.']);
        }
    }

    function store(FormRequest|AddTrackRequest $request)
    {
        if(!($request->has('track') && $request->file('track')->isValid())) {
            return response()->json(['msg' => 'Audio file is required.', 'status' => 422])->setStatusCode(422);
        }
        if(!($request->has('cover') && $request->file('cover')->isValid())) {
            return response()->json(['msg' => 'Cover image is required.', 'status' => 422])->setStatusCode(422);
        }

        //track
        try {
            $track = $request->file('track');
            $trackName = time().'.'.$track->getClientOriginalName();
            $audio = new Mp3Info($track);
            $duration = floor($audio->duration);
            $track->storeAs('uploads', $track->getClientOriginalName());
            Storage::disk('public')->put($trackName, $track);
            $trackPath = Storage::disk('public')->url($trackName);
            try {
                DB::beginTransaction();
                $track = new Track();

                $track->title = $request->get('title');
                $track->owner_id = $request->get('owner');
                $track->genre_id = $request->get('genre');

                $track->cover = 'cover_url.S3_account';
                $track->path = storage_path($trackPath);
                $track->plays = 0;
                if($request->get('explicit') == 'true') {
                    $track->explicit = true;
                } else{
                    $track->explicit = false;
                }
                if($request->has('album')) {
                    $track->album_id = $request->get('album');
                }
                $track->duration = $duration;
                $track->save();

                if($request->has('features')) {
                    foreach ($request->get('features') as $feature)
                        $track->featuring()->create([
                            'track_id' => $track->id,
                            'artist_id' =>$feature
                        ]);
                }
                DB::commit();

                return response()->json('created track');
            }
            catch (\Exception $exception) {
                DB::rollBack();

                return response()->json($exception->getMessage());
            }
        }
        catch (\Exception $exception) {
            $user = "Anonymous";
            if(Auth::hasUser()){
                $user = Auth::user()->email;
            }
            Bugsnag::notifyException(new \Exception('User: '.$user.'; Exception'.$exception->getMessage()));
            return response()->json('Fatal error!');
        }
        //Storage::disk('public')->put($trackName, $track);

        //cover
        $image = $request->file('cover');
        $imageName = time() . '.' . $image->getClientOriginalExtension();

        //Storage::disk('public')->put($imageName, $image);

        return response()->json(['name' => $track->getClientOriginalName(),'duration' => $duration, 'size' => $audio->_fileSize]);
        if(!$request->hasFile('track')) {
            return response()->json(['message' => 'Track not provided.', 'status' => 422])->setStatusCode(422);
        }
        if(!$request->hasFile('cover')) {
            return response()->json(['message' => 'Cover image not provided.', 'status' => 422])->setStatusCode(422);
        }

        $file = $request->file('track');
        $audio = new Mp3Info($file);
        $image = $request->file('cover');


        $duration = floor($audio->duration);
        $bitRate = $audio->bitRate;

        try {
            DB::beginTransaction();
            $track = new Track();

            $track->title = $request->get('title');
            $track->owner_id = $request->get('owner');
            $track->genre_id = $request->get('genre');

            $track->cover = 'cover_url.S3_account';
            $track->path = 'path_to_track.S3_account';
            $track->plays = 0;
            if($request->get('explicit') == 'true') {
                $track->explicit = true;
            } else{
                $track->explicit = false;
            }
            if($request->has('album')) {
                $track->album_id = $request->get('album');
            }
            $track->duration = $duration;
            $track->save();

            if($request->has('features')) {
                foreach ($request->get('features') as $feature)
                $track->featuring()->create([
                    'track_id' => $track->id,
                    'artist_id' =>$feature
                ]);
            }
            DB::commit();
            return response()->json($track);

        }
        catch (\Exception $exception) {
            DB::rollBack();

            // Log the exception for debugging purposes

            // Return an error response with a meaningful message
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    function update(FormRequest|AddTrackRequest $request, string $id)
    {
        if(!$request->hasFile('track')) {
            return response()->json(['msg' => 'Track not provided.', 'status' => 422])->setStatusCode(422);
        }
        if(!$request->hasFile('cover')) {
            return response()->json(['msg' => 'Cover image not provided.', 'status' => 422])->setStatusCode(422);
        }
        return response()->json(['request'=> $request->get('title'), 'id' => $id]);
    }

    function delete(string $id)
    {
        // TODO: Implement delete() method.
    }

    function getTrack(string $id, Request $request)
    {
        $play = new TrackPlay();
        $actor = \auth('sanctum')->user();
        if($actor) {
            $actor_id = $actor->getAuthIdentifier();
            $play->actor_id = $actor_id;
        }

        $play->track_id = $id;

        $play->save();

        return response()->json('https://commondatastorage.googleapis.com/codeskulptor-demos/DDR_assets/Kangaroo_MusiQue_-_The_Neverwritten_Role_Playing_Game.mp3');

        $section = $request->get('section');
        if($section) {
            $path = storage_path('app/audio/'.$id.'.mp3');

            if(file_exists($path)){
                $size = filesize($path);
                $track = Track::with('owner')
                    ->with('features')
                    ->with('album.tracks')
                    ->with('genre')
                    ->withCount('likes')->findOrFail($id);

                $headers = [
                    'Content-Type' => 'audio/mpeg',
                    'Content-Length' => 500000,
                    'Range' => "",
                    'Accept-Ranges' => 'bytes',
                ];

              /*  return response()->stream(function () use ($section, $path, $size) {
                    $stream = fopen($path, 'r');
                    fseek($stream, $section);
                    $length =  500000;
                    echo fread($stream, $length);
                    fclose($stream);
                }, 206, $headers);*/
            } else {
                abort(404);
            }
        }

    }

    function popular()
    {
        $now = Carbon::now();
        $sevenDays = $now->copy()->subDays(30);

        $popularLastSevenDays = TrackPlay::select('track_id')
            ->whereBetween('created_at', [$sevenDays, $now])
            ->groupBy('track_id')
            ->havingRaw('COUNT(track_id) > 5') // pustana vise od n puta ukupno
            ->havingRaw('COUNT(DISTINCT actor_id) > 0') // vise od n korisnika
            ->orderByDesc(\DB::raw('COUNT(track_id)'))
            ->take(10);

        $tracks = Track::with(['owner', 'features', 'album'])
            ->whereIn('id', $popularLastSevenDays)
            ->get();

        return response()->json($tracks);
    }

    public function deleteMany($request)
    {
        $tracksToDelete = $request->get('data');

        try {
            DB::beginTransaction();

            TrackPlaylist::whereIn('track_id', $tracksToDelete)->delete();
            TrackPlay::whereIn('track_id', $tracksToDelete)->delete();
            LikedTrack::whereIn('track_id', $tracksToDelete)->delete();
            Track::whereIn('id', $tracksToDelete)->delete();

            DB::commit();


            return response()->json('successfully deleted tracks');
        }
        catch (\Exception $exception) {
            return response()->json('exception caught!');
        }

        return response()->json($request->get('data'));
    }
}
