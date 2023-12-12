<?php

namespace App\Repositories\Implementations;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Track;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EloquentSearchRepository
{
    public function search(Request $request) {
        if($request->has('search')) {

            $query = $request->get('search');
            if($query != "") {
                $tracks = Track::with(['features', 'owner', 'album'])->where(function ($queryBuilder) use ($query) {
                    $queryBuilder->where('title', 'like', "%$query%")
                        ->orWhereHas('owner', function ($artistQueryBuilder) use ($query){
                            $artistQueryBuilder->where('name', 'like', "%$query%");
                        });
                })
                 //   ->orderByDesc('track_plays_count')
                    ->paginate(10, ['*'], 'track_page')->appends(['search'=> $query]);

                $albums = Album::where('name', 'like', "%$query%")->withCount('tracks')->paginate(5, ['*'], 'album_page')->appends(['search'=> $query]);
                $artists = Artist::where('name', 'like', "%$query%")->withCount('followedBy')->paginate(5, ['*'], 'artist_page')->appends(['search'=> $query]);
                return response()->json([
                    'tracks' => $tracks,
                    'artists' => $artists,
                    'albums' => $albums
                ])->setStatusCode(200);
            }

            /*$bestMatch = null;

            if($query != "") {
                $tracks = Track::with('features')->with('owner')
                    ->where(function ($queryBuilder) use ($query) {
                        $queryBuilder
                            ->where('title', 'like', "%{$query}%")
                            ->orWhereHas('owner', function ($artistQuery) use ($query) {
                                $artistQuery->where('name', 'like', "%{$query}%");
                            });
                    })
                    ->orderByRaw("CASE WHEN title = ? THEN 0
                                      WHEN title LIKE ? THEN 1
                                      WHEN title LIKE ? THEN 2
                                      WHEN title LIKE ? THEN 3
                                      ELSE 4
                                      END",
                        [
                            $query,
                            $query.'%',
                            '%'.$query.'%',
                            '%'.$query
                        ])
                    ->take(5)->get();

                $albums = Album::where('name', 'like',"%{$query}%")->take(5)->get();
                $artists = Artist::where(function ($queryBuilder) use ($query)
                {
                    $queryBuilder->where('name', 'like',"%{$query}%")

                        ->orderByRaw("CHARINDEX({$query}, 'name', 1)");
                        //->orderByRaw("CASE WHEN name LIKE ? THEN 1 ELSE 2 END", [$query]);
                })->take(5)->get();
                $bestArtist = $artists->first();

                if($bestArtist) {
                    $bestMatch = $bestArtist;
                }
                else{
                    if($albums) $bestAlbum = $albums->first();
                    if($tracks) $bestTrack = $tracks->first();

                    if($bestAlbum) $bestMatch = $bestAlbum;
                    if($bestTrack) $bestMatch = $bestTrack;
                }
               // $topMatch = $artists->where('name', '=', $query);
                //$topMatch = $albums->where('title', '=', $query);
               // $topMatch = $tracks->where('title', '=', $query);
                return response()->json(['topMatch' => $bestMatch,'tracks' => $tracks, 'artists' => $artists, 'albums' => $albums])->setStatusCode(200);
            }
            return response()->json(['tracks' => [], 'artists' => [], 'albums' => []])->setStatusCode(200);*/
        }

    }
}
