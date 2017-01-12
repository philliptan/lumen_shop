<?php

namespace App\Http\Controllers;

use App\User;
use Psr\Http\Message\ServerRequestInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Get user profile
     *
     * @return Response
     */
    public function profile(ServerRequestInterface $request)
    {
        return User::findOrFail(Auth::user()->id);
    }
}