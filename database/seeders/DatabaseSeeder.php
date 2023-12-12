<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Actor;
use App\Models\Album;
use App\Models\Artist;
use App\Models\Feature;
use App\Models\Genre;
use App\Models\Track;
use App\Models\UserSettings;
use Database\Factories\FeatureFactory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        //Artist::factory(30)->create();
        Genre::factory(15)->create();
        Artist::factory(30)->create();
        Album::factory(20)->create();
        Actor::factory(20)->create();
        Track::factory(500)->create();
        Feature::factory(200)->create();
        UserSettings::factory(1)->create();
    }
}
