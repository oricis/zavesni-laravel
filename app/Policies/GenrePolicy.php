<?php

namespace App\Policies;

use App\Models\Actor;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class GenrePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Actor $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Actor $user, Genre $genre): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Actor $actor): Response
    {
        if($actor->canAddGenre()){
            return Response::allow('', 200);
        }
        return Response::deny(['message' => 'Unauthorized access.'], 401);

    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Actor $user, Genre $genre): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Actor $user, Genre $genre): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(Actor $user, Genre $genre): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(Actor $user, Genre $genre): bool
    {
        //
    }
}
