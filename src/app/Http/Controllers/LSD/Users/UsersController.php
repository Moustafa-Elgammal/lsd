<?php

namespace App\Http\Controllers\LSD\Users;

use App\LSD\Users\Repositories\UsersRepository;
use App\LSD\Users\Services\UsersService;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UsersController extends Controller
{

    private $service;

    public function __construct()
    {
        $this->middleware('throttle:20,1');
        $this->middleware('auth:api',['except' => ['index','show']]);

        $this->service = new UsersService(new UsersRepository());
    }


    public function auth(Request $request)
    {
        $user_id = $request->get("uid", 0);
        $user = User::find($user_id);
        return $user;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return $this->service->getUsers();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        return $this->service->createUser();
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        return $this->service->getUser($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        return $this->service->updateUser($id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
       return $this->service->deleteUser($id);
    }
}
