<?php

namespace App\Repositories\Implementations;

use App\Http\Requests\UpdateArtistRequest;
use App\Models\Actor;
use App\Models\Album;
use App\Models\Artist;
use App\Models\Genre;
use App\Models\Playlist;
use App\Models\Track;
use App\Models\TrackPlay;
use App\Repositories\Interfaces\AdminRepositoryInterface;
use Carbon\Carbon;
use http\Client\Curl\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\DB;

class EloquentAdminRepository implements AdminRepositoryInterface
{

    function index()
    {
        $now = Carbon::now();
        $sevenDays = Carbon::now()->subWeek();
        $sevenDays1 = Carbon::now()->subMonth();
        $totalTracks = Track::count();
        $totalAlbums = Album::count();
        $totalPlaylists= Playlist::count();
        $totalArtists = Artist::count();
        $totalUsers = Actor::get()->count();
        $totalTrackPlays = TrackPlay::count();
        $averageNumberOfPlaylistsPerUser = Playlist::select('actor_id', DB::raw('Count(id) as count'))->groupBy('actor_id')->get();
        $totalUsersWithPlaylist = $averageNumberOfPlaylistsPerUser->count();
        $totalPlaylists1 = $averageNumberOfPlaylistsPerUser->sum('count');
        $averageNofTracksPerPlaylist = Playlist::select('id', DB::raw('COUNT(tp.playlist_id) as count'))->join('track_playlist as tp', 'playlists.id', '=', 'tp.playlist_id')->groupBy('id')->get();
        $sumOfTrackInPlaylists = $averageNofTracksPerPlaylist->sum('count');
        $averageTP = round($sumOfTrackInPlaylists / $averageNofTracksPerPlaylist->count(),2);
        $average = round($totalPlaylists1 / $totalUsersWithPlaylist, 2);
        $numberOfCreatedPlaylistsLast7Days = Playlist::whereBetween('created_at', [$sevenDays, $now])->count();
        $numberOfActiveUsersLast7Days = TrackPlay::select('actor_id')->whereBetween('created_at',[$sevenDays, $now])->groupBy('actor_id')->distinct('actor_id')->get()->count();
        $newUsers = Actor::whereBetween('created_at', [$sevenDays, $now])->count();
        $percentageLast7Days = round(($numberOfActiveUsersLast7Days / $totalUsers) * 100, 2);
$latestRegistered = Actor::whereBetween('created_at', [$sevenDays1, $now])->count();
        $activeUsersData = [];
        $labels1 = [];
        $data1 = [];
        $data2 = [];

        for($date = $sevenDays; $date->lte($now); $date->addDay()){
            $activeUsers = TrackPlay::whereDate('created_at', $date)->distinct('actor_id')->count();
            $createdPlaylists = Playlist::whereDate('created_at', $date)->distinct('id')->count();

            $labels1[] = $date->format('M d');
            $data1[] = $activeUsers;
            $data2[] = $createdPlaylists;
        }

        $popularGenres = TrackPlay::select('genre_id', 'g.name', 'g.hex_color', DB::raw('COUNT(track_id) as count'))
            ->join('tracks as t', 'track_plays.track_id', '=', 't.id')
            ->join('genres as g', 't.genre_id', '=', 'g.id')
            ->groupBy('genre_id', 'g.name', 'g.hex_color')->get();
        $genrePieChartLabels = [];
        $genrePieChartData = [];
        $genrePieChartHexColor = [];
        foreach ($popularGenres as $genre) {
            $genrePieChartLabels[] = $genre->name;
            $genrePieChartData[] = $genre->count;
            $genrePieChartHexColor[] = $genre->hex_color;
        }
        return response()->json(['popularGenres' => [$genrePieChartLabels, $genrePieChartData, $genrePieChartHexColor],'newUsers' => $newUsers,'averageNofTrackPerPlaylist' => $averageTP,'average' => $average,'totalTrackPlays' => $totalTrackPlays,'registered' => $latestRegistered,'totalArtists' => $totalArtists, 'totalPlaylists' => $totalPlaylists,'totalAlbums' => $totalAlbums,'totalTracks' => $totalTracks, 'createdPlaylists'=> [$labels1, $data2],'percentageOfActiveUsersInLast7Days' => [$labels1, $data1],'percentageOfActiveUsers' => $percentageLast7Days,'activeUsers' => $numberOfActiveUsersLast7Days, 'totalUsers' => $totalUsers, 'numberOfCreatedPlaylistsLast7Days' => $numberOfCreatedPlaylistsLast7Days]);
    }
    function actors()
    {
        $pagedResponse = Actor::paginate(10);
        $users = $pagedResponse->items();

        foreach ($users as $user) {
            $user->formatted_created_at = Carbon::parse($user->created_at)->format('d M Y H:i:s');
            $user->formatted_updated_at = Carbon::parse($user->updated_at)->format('d M Y H:i:s');
        }
        return response()->json($pagedResponse);
    }

    function artists()
    {
        $artists = Artist::withCount('ownTracks')->paginate(10);
        return response()->json($artists);
    }
    public function tracks()
    {
        $tracksPaginator = Track::withCount('trackPlays')->with(['owner', 'features', 'album'])->paginate(10);
        $tracks = $tracksPaginator->items();

        foreach ($tracks as $track) {
            $track->formatted_created_at = Carbon::parse($track->created_at)->format('d M Y H:i:s');
            $track->formatted_updated_at = Carbon::parse($track->updated_at)->format('d M Y H:i:s');
        }
        return response()->json($tracksPaginator);
    }

    public function albums() {
        $albums = Album::withCount('tracks')->paginate(10);

        return response()->json($albums);
    }
    function updateArtist(string $id, UpdateArtistRequest|FormRequest $request)
    {
        $artist = Artist::withCount('ownTracks')->findOrFail($id);
        if ($request->hasFile('image')){
            $file      = $request->file('image');
            $filename  = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            // $picture   = date('His').'-'.$filename;
            $imageName = time() . '.' . $file->getClientOriginalExtension();
            $file->move("F:\WebStorm Projects\zavrsniAng\src\assets\images\artists", $imageName);
            $artist->cover = 'assets/images/artists/'.$imageName;
        }
        if($request->has('name')) {
            $artist->name = $request->get('name');
            $artist->save();

            return response()->json($artist)->setStatusCode(200);
        }
    }
    public function storeArtist(FormRequest $request)
    {
        $artist = new Artist();
        $name = $request->get('name');
        if($request->hasFile('image')){
            $artist->name = $name;
            $file      = $request->file('image');
            /*$filename  = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();*/
            // $picture   = date('His').'-'.$filename;
            $imageName = time() . '.' . $file->getClientOriginalExtension();
            $file->move("F:\WebStorm Projects\zavrsniAng\src\assets\images\artists", $imageName);
            $artist->cover = 'assets/images/artists/'.$imageName;
            $artist->save();
        }
        return response()->json()->setStatusCode(201);
    }
    public function deleteArtist(string $id)
    {
        $artist = Artist::findOrFail($id);

        $artist->delete();
        $artist->save();
        return response()->json()->setStatusCode(204);
    }
    public function genres()
    {
        $genresPagination = Genre::paginate(10);
        $genres = $genresPagination->items();

        foreach ($genres as $genre) {
            $genre->formatted_created_at = Carbon::parse($genre->created_at)->format('d M Y H:i:s');
            $genre->formatted_updated_at = Carbon::parse($genre->updated_at)->format('d M Y H:i:s');
        }
        return response()->json($genresPagination);
    }

    function deleteGenre(string $id)
    {
        // TODO: Implement deleteGenre() method.
    }

    function deleteTrack(string $id)
    {
        // TODO: Implement deleteTrack() method.
    }

    function deleteUser(string $id)
    {
        $user  = Actor::findOrFail($id);

        return response()->json($user);
    }
}
