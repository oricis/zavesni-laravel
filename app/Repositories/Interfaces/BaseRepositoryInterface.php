<?php

namespace App\Repositories\Interfaces;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

interface BaseRepositoryInterface
{
    function getAll();
    function show(string $id);
    function store(FormRequest $request);
    function update(FormRequest $request, string $id);
    function delete(string $id);
}
