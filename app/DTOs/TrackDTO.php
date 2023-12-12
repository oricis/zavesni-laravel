<?php

namespace App\DTOs;

class TrackDTO
{
    public string $id;
    public string $name;
    public string $path;
    public ArtistDTO $owner;

    public function __construct(string $id, string $name, string $path)
    {
        $this->id = $id;
        $this->name = $name;
        $this->path = $path;

    }
}
