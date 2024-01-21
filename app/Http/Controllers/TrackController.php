<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddTrackRequest;
use App\Http\Requests\DeleteManyRequest;
use App\Models\Track;
use App\Repositories\Interfaces\TrackRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class TrackController extends Controller
{
    private TrackRepositoryInterface $trackRepository;
    public function __construct(TrackRepositoryInterface $trackRepository) {
        $this->trackRepository = $trackRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->trackRepository->getAll();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AddTrackRequest $request)
    {
        return $this->trackRepository->store($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->trackRepository->show($id);
    }

    public function getTrack(string $id, Request $request) {
        return $this->trackRepository->getTrack($id, $request);
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AddTrackRequest $request, string $id)
    {
        return $this->trackRepository->update($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function destroyMany(DeleteManyRequest $request)
    {
        return $this->trackRepository->deleteMany($request);
    }
    public function popular() {
        return $this->trackRepository->popular();
    }
}
