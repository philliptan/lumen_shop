<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Transformer\UserTransformer;
use App\User;
use Illuminate\Database\Eloquent\Collection;


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
    public function profile($id): array
    {
        $user = User::findOrFail($id);
        return $this->item($user, new UserTransformer());
    }

    /**
     * GET user list
     *
     * @return array
     */
    public function getList(): array
    {
        return $this->collection(User::all(), new UserTransformer());
    }

    //
}
