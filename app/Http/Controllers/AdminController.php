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
    public function actors() {
        return $this->adminRepository->actors();
    }
    public function artists() {
        return $this->adminRepository->artists();
    }
    public function tracks() {
        return $this->adminRepository->tracks();
    }
    public function albums() {
        return $this->adminRepository->albums();
    }
    public function genres() {
        return $this->adminRepository->genres();
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
