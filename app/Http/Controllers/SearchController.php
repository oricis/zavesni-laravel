<?php

namespace App\Http\Controllers;

use App\Repositories\Implementations\EloquentSearchRepository;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    private $searchRepository;

    public function __construct(EloquentSearchRepository $searchRepository) {
        $this->searchRepository = $searchRepository;
    }
    public function search(Request $request) {
        return $this->searchRepository->search($request);
    }
}
