<?php

namespace App\Repositories\Interfaces;

use App\Http\Requests\UpdateArtistRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Client\Request;

interface AdminRepositoryInterface
{
    function index();
    function actors();
    function artists();
    function tracks(\Illuminate\Http\Request $request);
    function albums();
    function genres();
    function roles();

    function updateArtist(string $id, FormRequest $request);
    function storeArtist(FormRequest $request);

    function deleteArtist(string $id);
    function deleteGenre(string $id);
    function deleteTrack(string $id);
    function deleteUser(string $id);


}
