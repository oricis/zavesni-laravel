<?php

namespace App\DTOs;

class ArtistDTO
{
    public string $id;
    public string $name;
    public $albums;
    public $tracks;

    public function __construct(string $id, string $name){
        $this->id = $id;
        $this->name = $name;
    }
}
