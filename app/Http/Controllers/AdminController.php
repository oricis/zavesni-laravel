<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArtistRequest;
use App\Http\Requests\UpdateArtistRequest;
use App\Repositories\Interfaces\AdminRepositoryInterface;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    private AdminRepositoryInterface $adminRepository;
    public function __construct(AdminRepositoryInterface $adminRepository)
    {
        $this->adminRepository = $adminRepository;
    }

    public function index() {
        return $this->adminRepository->index();
    }
    public function actors(Request $request) {
        return $this->adminRepository->actors($request);
    }
    public function artists(Request $request) {
        return $this->adminRepository->artists($request);
    }
    public function tracks(Request $request) {
        return $this->adminRepository->tracks($request);
    }
    public function albums(Request $request) {
        return $this->adminRepository->albums($request);
    }
    public function genres(Request $request) {
        return $this->adminRepository->genres($request);
    }
    public function roles() {
        return $this->adminRepository->roles();
    }
    public function updateArtist(UpdateArtistRequest $request, string $id) {
        return $this->adminRepository->updateArtist($id, $request);
    }
    public function storeArtist(StoreArtistRequest $request) {
        return $this->adminRepository->storeArtist($request);
    }
    public function deleteArtist(string $id){
        return $this->adminRepository->deleteArtist($id);
    }
    public function deleteGenre(string $id){
        return $this->adminRepository->deleteGenre($id);
    }
    public function deleteUser(string $id){
        return $this->adminRepository->deleteUser($id);
    }
}
