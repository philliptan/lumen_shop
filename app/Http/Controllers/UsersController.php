<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/**
 * Class UsersController
 * @package App\Http\Controllers
 */
class UsersController extends Controller
{
    /**
     * GET user profile
     *
     * @return array
     */
    public function profile(): array
    {
        return [];
    }

    /**
     * GET user list
     *
     * @return array
     */
    public function getList(): Collection
    {
        DB::listen(function($query) {
            $message = "\n SQL ::: %s \n Binding ::: %s \n Timing ::: %s";
            Log::debug(sprintf(
                $message,
                $query->sql,
                implode(', ', $query->bindings),
                $query->time
            ));
        });

        return User::all();
    }

    //
}
