<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Transformer\UserTransformer;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
    public function show($id): array
    {
        $user = User::findOrFail($id);
        return $this->item($user, new UserTransformer());
    }

    /**
     * GET user list
     *
     * @return array
     */
    public function index(): array
    {
        return $this->collection(User::all(), new UserTransformer());
    }

    /**
     * POST /users
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request): Response
    {
        // Validate
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = User::create($request->all());
        $data = $this->item($user, new UserTransformer());
        return response($data, Response::HTTP_CREATED)
                ->header('Location', route('user.show', ['id' => $user->id]));
    }

    /**
     * POST /users/[\d]+
     *
     * @param  Request  $request
     * @param  integer  $id
     * @return Response
     */
    public function update(Request $request, int $id): array
    {
        $user = User::findOrFail($id);
        $user->fill($request->all());
        $user->saveOrFail();

        return $this->item($user, new UserTransformer());
    }

    /**
     * DELETE /users/[\d]+
     *
     * @param  integer  $id
     * @return Response
     */
    public function destroy(int $id): Response
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    //
}
