<?php

namespace Database\Factories;

use App\Models\Artist;
use App\Models\Track;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class FeatureFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $track = Track::inRandomOrder()->first();

        $owner = $track->owner;

        $artist = Artist::where('id', '!=', $owner->id)->inRandomOrder()->first();
        return [
            'track_id' => $track->id,
            'artist_id' => $artist->id
        ];
    }
}
