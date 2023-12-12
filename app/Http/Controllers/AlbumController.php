<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAlbumRequest;
use App\Models\Album;
use App\Repositories\Interfaces\AlbumRepositoryInterface;
use Illuminate\Http\Request;

class AlbumController extends Controller
{
    private AlbumRepositoryInterface $albumRepository;

    public function __construct(AlbumRepositoryInterface $albumRepository){
        $this->albumRepository = $albumRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->albumRepository->getAll();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAlbumRequest $request)
    {
        return $this->albumRepository->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->albumRepository->show($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreAlbumRequest $request, string $id)
    {
        return $this->albumRepository->update($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return $this->albumRepository->delete($id);
    }

    public function getLatest() {
        return $this->albumRepository->getLatest();
    }
    public function popular() {
        return $this->albumRepository->popular();
    }
}
