<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGenreRequest;
use App\Repositories\Interfaces\GenreRepositoryInterface;

class GenreController extends Controller
{
    private GenreRepositoryInterface $genreRepository;

    public function __construct(GenreRepositoryInterface $genreRepository)
    {
        $this->genreRepository = $genreRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->genreRepository->getAll();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGenreRequest $request)
    {
        return $this->genreRepository->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->genreRepository->show($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreGenreRequest $request, string $id)
    {
        return $this->genreRepository->update($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return $this->genreRepository->delete($id);
    }
}
