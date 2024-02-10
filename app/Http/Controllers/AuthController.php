<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Actor;
use App\Repositories\Interfaces\AuthRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    private AuthRepositoryInterface $authRepository;

    public function __construct(AuthRepositoryInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }

    public function register(RegisterRequest $request) {
        return $this->authRepository->register($request);
    }
    public function login(LoginRequest $request){
        return $this->authRepository->login($request);
    }
    public function logout() {
        return $this->authRepository->logout();
    }
    public function getToken() {
        return $this->authRepository->getToken();
    }
    public function revokeToken() {
        return $this->authRepository->revokeToken();
    }
}
