<?php

namespace App\Providers;

use App\Repositories\Implementations\EloquentActorRepository;
use App\Repositories\Implementations\EloquentAdminRepository;
use App\Repositories\Implementations\EloquentAlbumRepository;
use App\Repositories\Implementations\EloquentArtistRepository;
use App\Repositories\Implementations\EloquentAuthRepository;
use App\Repositories\Implementations\EloquentGenreRepository;
use App\Repositories\Implementations\EloquentPlaylistRepository;
use App\Repositories\Implementations\EloquentTrackRepository;
use App\Repositories\Interfaces\ActorRepositoryInterface;
use App\Repositories\Interfaces\AdminRepositoryInterface;
use App\Repositories\Interfaces\AlbumRepositoryInterface;
use App\Repositories\Interfaces\ArtistRepositoryInterface;
use App\Repositories\Interfaces\AuthRepositoryInterface;
use App\Repositories\Interfaces\GenreRepositoryInterface;
use App\Repositories\Interfaces\PlaylistRepositoryInterface;
use App\Repositories\Interfaces\TrackRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AuthRepositoryInterface::class, EloquentAuthRepository::class); //auth
        $this->app->bind(GenreRepositoryInterface::class, EloquentGenreRepository::class); //genre
        $this->app->bind(ArtistRepositoryInterface::class, EloquentArtistRepository::class); //artists
        $this->app->bind(PlaylistRepositoryInterface::class, EloquentPlaylistRepository::class); //playlists
        $this->app->bind(AlbumRepositoryInterface::class, EloquentAlbumRepository::class); // albums
        $this->app->bind(ActorRepositoryInterface::class, EloquentActorRepository::class); // actors-users
        $this->app->bind(TrackRepositoryInterface::class, EloquentTrackRepository::class); // tracks

        $this->app->bind(AdminRepositoryInterface::class, EloquentAdminRepository::class); // admin dashboard
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
