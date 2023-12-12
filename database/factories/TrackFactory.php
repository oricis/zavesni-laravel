<?php

namespace Database\Factories;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Genre;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Track>
 */
class TrackFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $owner = Artist::inRandomOrder()->first()->id;

        $album = Album::where('artist_id', '=', $owner)->inRandomOrder()->first();

        if($album == null) {
            $album = null;
        }
        else{
            $album = $album->id;
        }
        return [
            'title' => ucfirst($this->faker->word()),
            'path' => 'assets/tracks/Donkey Kong Country - Aquatic Ambiance Remix [Kamex].mp3',
            'explicit' => $this->faker->boolean,
            'plays' => $this->faker->numberBetween(0,  10000000),
            'owner_id' => $owner,
            'album_id' => $album,
            'genre_id' => Genre::inRandomOrder()->first()->id,
            'duration' => $this->faker->numberBetween(1000000, 9999999999)
        ];
    }
}
