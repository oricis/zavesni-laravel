<?php

namespace App\Repositories\Interfaces;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;

interface AuthRepositoryInterface
{
    public function register(RegisterRequest $request);
    public function login(LoginRequest $request);
    public function logout();
}
