<?php

namespace App\Repositories\Implementations;

use App\Http\Requests\LikeTrackRequest;
use App\Models\Actor;
use App\Models\Artist;
use App\Models\Track;
use App\Models\TrackPlay;
use App\Models\UserSettings;
use App\Repositories\Interfaces\ActorRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class EloquentActorRepository implements ActorRepositoryInterface
{

    function show()
    {
        $actor = Actor::withCount(['playlists', 'following'])->with(['likedAlbums','following' => function($query) { $query->orderByDesc('following.created_at');},'settings','likedTracks','playlists' => function ($query) {
            $query->with('tracks.owner')
                ->with('tracks.features')
                ->with('tracks.album')
                ->withCount('tracks')->get();
        }])->find(Auth::user()->getAuthIdentifier());

        if($actor == null) {
            return response(['message' => 'No actor has been found.']);
        }
        return response($actor, 200);
    }

    function showPlaylists()
    {
        $actor = Actor::find(\auth()->user()->getAuthIdentifier());
        return response()->json($actor->playlists()->get())->setStatusCode(200);
    }

    function showLiked()
    {
        if(Auth::hasUser()){
            $actorId = Auth::user()->getAuthIdentifier();
            $actor = Actor::find($actorId);

            if($actor == null)
            {
                return response()->json(['message' => 'No actor has been found']);
            }

            $likedTracks = $actor->likedTracks;

            if(count($likedTracks) == 0) {
                return response()->json(['message' => 'Actor did not like any tracks.']);
            }
            return response()->json($likedTracks)->setStatusCode(200);
        }
    }

    function like(LikeTrackRequest $request)
    {
        $track = $request->validated('track');
        if(Auth::hasUser()){
            $actorId = Auth::user()->getAuthIdentifier();
            $actor = Actor::find($actorId);

            $trackExists = Track::find($track);
            if($trackExists == null) {
                return response()->json(['message' => 'Provided track does not exists.']);
            }

            $alreadyLiked = $actor->likedTracks()->find($track);

            if($alreadyLiked){
                return response()->json(['message' => 'You have already liked this track.'], 409);
            }
            $actor->likedTracks()->attach($trackExists, ['created_at' => now()]);

            return response()->json(['message' => 'Added to Liked tracks'], 201);
        }
        return response()->json(['message' => 'No actor has been found']);


    }

    public function removeFromLiked(string $id)
    {
        try{
            if(Auth::hasUser()){
                $actorId = Auth::user()->getAuthIdentifier();
                $actor = Actor::findOrFail($actorId)
                ;
                $trackToRemove = $actor->likedTracks()->findOrFail($id."A4");

                if($trackToRemove) {
                    $actor->likedTracks()->updateExistingPivot($trackToRemove->id, ['deleted_at' => now()]);
                    return response()->json(['message' => 'Removed \''.$trackToRemove->title.'\' from liked'])->setStatusCode(200);
                }
                else{
                    return response()->json(['message' => 'User did not like that track.'])->setStatusCode(202);
                }

            }
            return response()->json(['message' => 'No actor has been found']);
        }
        catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()])->setStatusCode(500);
        }
    }

    function followArtist(string $artistId)
    {
        $artistExists = Artist::where('id', $artistId)->first();

        if(!$artistExists) {
            return response()->json('Artist doesnt exist.')->setStatusCode(404);
        }
        $following = \auth()->user()->following()->where('artist_id',$artistId)->first();

        if($following) {
            return response()->json('You are already following provided artist.')->setStatusCode(422);
        }
        $following = \auth()->user()->following();
        $following->attach($artistExists->id, ['created_at' => now()]);

        return response()->json('Added to your followings.')->setStatusCode(200);
    }

    function unfollowArtist(string $artistId)
    {
        $artistExists = Artist::where('id', $artistId)->first();
        if(!$artistExists){
            return response()->json('Artist doesnt exist.')->setStatusCode(404);
        }
        $following = \auth()->user()->following()->where('artist_id', $artistId)->first();

        if(!$following) {
            return response()->json('Cannot unfollow artist you don\'t follow.')->setStatusCode(409);
        }
        \auth()->user()->following()->detach($following->id);
        return response()->json()->setStatusCode(204);
    }

    function recommendArtists()
    {
        $actor = \auth('sanctum')->user();
        $followings = $actor->following();
        $followedArtistIds = $followings->pluck('artist_id')->toArray();
        $recommendedArtists = [];
        foreach ($followings->get() as $following) {
            $actorsThatAlsoFollowSameArtists = $following->followedBy()->whereNot('actor_id', $actor->getAuthIdentifier())->get();
            foreach ($actorsThatAlsoFollowSameArtists as $a) {
                /*if ($a->id == $actor->getAuthIdentifier()) {
                    continue;
                }*/
                $commonArtistsCount = $actor->following()->whereIn('artist_id', $a->following()->pluck('artist_id')->toArray())->count();
                if ($commonArtistsCount >= 2) {
                    $otherUserArtists = $a->following()->withCount('followedBy')->inRandomOrder()->take(10)->get();
                    foreach ($otherUserArtists as $other){
                        if(!in_array($other->id, $followedArtistIds)) {
                            array_push($recommendedArtists, $other); //unesi izvodjace koji ostali povezani korisnici prate
                        }
                    }
                }
            }
        }
        $uniqueRecommendedArtists = collect($recommendedArtists)->unique('id')->values();

        return response()->json($uniqueRecommendedArtists);
    }

    function recommendTracks()
    {
        $actor = \auth('sanctum')->user();

        $userPlayedTracks = TrackPlay::where('actor_id', $actor->getAuthIdentifier())->pluck('track_id')->toArray();
        $otherSimiliarUsers = TrackPlay::whereIn('track_id', array_slice($userPlayedTracks, 0, 100))
            ->whereNot('actor_id', $actor->getAuthIdentifier())
            ->groupBy('actor_id')
            ->havingRaw('COUNT(DISTINCT track_id) >= ?', [10])
            ->pluck('actor_id')
            ->unique(20)->toArray();

        $tracksOfSimiliarUsers = TrackPlay::whereIn('actor_id', $otherSimiliarUsers)->get();
        $tracksToRecommendIds = [];

        foreach ($tracksOfSimiliarUsers as $track) {
            if(!in_array($track->track_id, $userPlayedTracks)) {
                $tracksToRecommendIds[] = $track->track_id;
            }
        }
        $tracksToRecommend = Track::whereIn('id', $tracksToRecommendIds)->with(['owner', 'features'])->take(10)->get();
        return response()->json($tracksToRecommend);
    }

    function favoriteTracksInLast7Days() {
        $actor = \auth()->user();
        $now = Carbon::now();
        $sevenDays = $now->copy()->subDays(7);
        $tracksPlayed = TrackPlay::where('actor_id', $actor->getAuthIdentifier())
            ->with(['track.owner', 'track.features', 'track.album'])
            ->selectRaw('track_id,COUNT(track_id) as count')
            ->groupBy('track_id')
            ->whereBetween('created_at', [$sevenDays, $now])
            ->orderByDesc('count')
            ->take(5)
            ->get()->pluck('track');
        /*$addPlayedCount = $trackPlayed->map(function ($item) {
            $track = $item;
            $track->track->count = $item->count;
            return $track;
        });
        $tracks = $addPlayedCount->pluck('track');*/


        //$tracks = Track::whereIn('id', $trackPlayed)->get();
        //$tracks = Track::whereIn('id', $trackPlayed)->pluck('owner_id');
       /* $userPlays = TrackPlay::where('actor_id', $actor->getAuthIdentifier())->pluck('track_id');
        $userPlays1 = TrackPlay::where('actor_id', $actor->getAuthIdentifier())->selectRaw('track_id, COUNT(track_id) as count')->with('track')->groupBy('track_id')->orderByDesc('count')->get();
        $tracks = Track::whereIn('id', $userPlays)->selectRaw('owner_id,COUNT(owner_id) as count')->with('owner')->orderByRaw('count DESC')->groupBy('owner_id')->get();
        foreach ($tracks as $t) {

        }*/
        return response()->json($tracksPlayed);
    }

    function recentlyPlayed() {
        $actor = \auth('sanctum')->user();

        $tracks = TrackPlay::where('actor_id', $actor->getAuthIdentifier())
            ->select('track_id', \DB::raw('MAX(created_at) as latest_play'))
            ->orderByDesc('latest_play')
            ->groupBy("track_id")
            ->with(['track.owner', 'track.features', 'track.album'])
            ->take(5)->get()->pluck('track');

        return response()->json($tracks);
    }

    function updateSettings($request) {

        if($request->has('value') && $request->has('setting')){
            $actor = \auth()->user()->getAuthIdentifier();
            $settings = UserSettings::where('actor_id', $actor)->first();
            if($request->get('setting') == 'history') {
                $settings->history = $request->get('value');
            }
            else if($request->get('setting') == 'explicit') {
                $settings->explicit = $request->get('value');
            }
            $settings->save();
            return response()->json($settings);
        }
    }
}
