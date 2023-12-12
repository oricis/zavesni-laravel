<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArtistRequest;
use App\Http\Requests\UpdateArtistRequest;
use App\Models\Artist;
use App\Repositories\Implementations\EloquentArtistRepository;
use App\Repositories\Interfaces\ArtistRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class ArtistController extends Controller
{
    private ArtistRepositoryInterface $artistRepository;

    public function __construct(ArtistRepositoryInterface $artistRepository)
    {
        $this->artistRepository = $artistRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->artistRepository->getAll();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreArtistRequest $request)
    {
        return $this->artistRepository->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->artistRepository->show($id);
    }
    public function showAlbums(string $id) {
        return $this->artistRepository->showAlbums($id);
    }
    public function showPopular(string $id) {
        return $this->artistRepository->showPopular($id);
    }
    public function showFeatures(string $id) {
        return $this->artistRepository->showFeatures($id);
    }
    public function showSingles(string $id) {
        return $this->artistRepository->showSingles($id);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateArtistRequest $request, string $id)
    {
        return $this->artistRepository->update($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return $this->artistRepository->delete($id);
    }
    public function popular() {
        return $this->artistRepository->popular();
    }
}
