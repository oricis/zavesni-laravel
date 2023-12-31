<?php

use App\Http\Controllers\ActorController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\TrackController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::middleware('api')->group(function (){
    Route::prefix('search')->group(function () {
        Route::get('/', [\App\Http\Controllers\SearchController::class, 'search']);
    });

    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/register', [AuthController::class, 'register']);
    });

    Route::prefix('genres')->group(function () {
        Route::middleware(['auth:sanctum'])->group(function () {
            Route::post('/', [GenreController::class, 'store']);
            Route::put('/{id}', [GenreController::class, 'update']);
            Route::delete('/{id}', [GenreController::class, 'destroy']);
        });
        Route::get('/', [GenreController::class, 'index']);
        Route::get('/{id}', [GenreController::class, 'show']);
    });


    Route::prefix('actor')->group(function () {
        Route::middleware(['auth:sanctum'])->group(function () {
            Route::get('/', [ActorController::class, 'show']);
            Route::get('/liked', [ActorController::class, 'showLiked']);
            Route::get('/playlists', [ActorController::class, 'showPlaylists']);
            Route::get('/favorite/tracks', [ActorController::class, 'favoriteTracksInLast7Days']);
            Route::get('/recommend/artists', [ActorController::class, 'recommendArtists']);
            Route::get('/recommend/tracks', [ActorController::class, 'recommendTracks']);
            Route::get('/recent/tracks', [ActorController::class, 'recentlyPlayed']);

            Route::post('/like', [ActorController::class, 'like']);
            Route::delete('/liked/{track}/remove', [ActorController::class, 'removeFromLiked']);

            //follow
            Route::post('/artists/{artist}/follow', [ActorController::class, 'followArtist']);
            Route::delete('/artists/{artist}/unfollow', [ActorController::class, 'unfollowArtist']);
        });
    });

    Route::prefix('artists')->group(function () {

        Route::middleware(['auth:sanctum'])->group(function () {
            Route::post('/', [ArtistController::class, 'store']);
            Route::put('/{id}', [ArtistController::class, 'update']);
            Route::delete('/{id}', [ArtistController::class, 'destroy']);
        });

        Route::get('/', [ArtistController::class, 'index']);
        Route::get('/popular', [ArtistController::class, 'popular']);
        Route::get('/{id}', [ArtistController::class, 'show']);
        Route::get('/{id}/albums', [ArtistController::class, 'showAlbums']);
        Route::get('/{id}/features', [ArtistController::class, 'showFeatures']);
        Route::get('/{id}/singles', [ArtistController::class, 'showSingles']);
        Route::get('/{id}/popular', [ArtistController::class, 'showPopular']);

    });



    Route::prefix('tracks')->group(function () {
        Route::get('/popular', [TrackController::class, 'popular']);
        Route::get('/{id}', [TrackController::class, 'getTrack']);
        Route::middleware(['auth:sanctum'])->group(function () {
            Route::post('/', [TrackController::class, 'store']);

        });
        Route::get('/', [TrackController::class, 'index']);
        Route::put('/{id}', [TrackController::class, 'update']);
        Route::delete('/{id}', [TrackController::class, 'destroy']);
        Route::post('/{id}/play', [TrackController::class, 'play']);

    });

    Route::prefix('playlists')->group(function () {
        Route::get('/', [PlaylistController::class, 'index']);

        Route::get('/{id}', [PlaylistController::class, 'show']);
        Route::get('/{id}/tracks', [PlaylistController::class, 'getTracks']);

        Route::middleware(['auth:sanctum'])->group(function () {
            Route::post('/', [PlaylistController::class, 'store']);
            Route::put('/{id}', [PlaylistController::class, 'update']);
            Route::delete('/{id}/delete', [PlaylistController::class, 'destroy']);
            Route::post('/{id}/add-tracks', [PlaylistController::class, 'addTracks']);
            Route::delete('/{playlist}/track/{track}/delete', [PlaylistController::class, 'deleteTrack']);
        });

    });

    Route::prefix('albums')->group(function () {
        Route::get('/', [AlbumController::class, 'index']);
        Route::post('/', [AlbumController::class, 'store']);
        Route::get('/latest', [AlbumController::class, 'getLatest']);
        Route::get('/popular', [AlbumController::class, 'popular']);
        Route::get('/{id}', [AlbumController::class, 'show']);
        Route::middleware(['auth:sanctum'])->group(function () {
            Route::post('/{id}/like', [AlbumController::class, 'like']);
            Route::delete('/{id}/like/delete', [AlbumController::class, 'removeFromLiked']);
        });
    });

    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index']);
        Route::get('/actors', [AdminController::class, 'actors']);
        Route::get('/artists', [AdminController::class, 'artists']);
        Route::get('/tracks', [AdminController::class, 'tracks']);
        Route::get('/albums', [AdminController::class, 'albums']);
        Route::get('/genres', [AdminController::class, 'genres']);
        Route::post('/artists/{id}/update', [AdminController::class, 'updateArtist']);
        Route::post('/artists', [AdminController::class, 'storeArtist']);
        Route::delete('/users/{id}/delete', [AdminController::class, 'deleteUser']);
        Route::delete('/genres/{id}/delete', [AdminController::class, 'deleteGenre']);
        Route::delete('/artists/{id}/delete', [AdminController::class, 'deleteArtist']);
       /*Route::middleware(['auth:sanctum'])->group(function () {

       });*/
    });

});

