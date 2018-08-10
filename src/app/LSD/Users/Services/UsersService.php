<?php

namespace App\LSD\Users\Services;


use App\LSD\Services;
use App\LSD\Users\Repositories\UsersRepository;
use Illuminate\Support\Facades\Input;


class UsersService extends Services
{
    private $repo;

    public function __construct(UsersRepository $repo)
    {
        //Users Repository
        $this->repo = $repo;

        // base64 Image validation
        \Validator::extend('imageBase64', function ($attribute, $value, $params, $validator) {

            $image = base64_decode($value);
            $f = finfo_open();

            // Get file MIME
            $result = finfo_buffer($f, $image, FILEINFO_MIME_TYPE);

            //IMAGE PART
            $type = explode('/',$result);
            try {
                return $type[0] == 'image' ? true : false;
            } catch (\Exception $e)
            {
                return false;
            }

        },"Bad Image File");

    }

    /** List of Users
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsers(){

        // user per page limit
        $limit = 10;

        $users = $this->repo->get($limit);

        // Response form
        $res = [
            'msg'       => 'List of Users',
            'errors'    => [],
            'users'     => $users
        ];

        return response()->json($res, $this->repo->getStatus());
    }


    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUser($id){
        $user = $this->repo->single($id);

        // Response
        $res = [
            'msg'       => 'User info',
            'errors'    => $this->repo->getErrors(),
            'users'     => ['data' => [$user]]
        ];

        return response()->json($res, $this->repo->getStatus());
    }
    /** Create New User
     * @return \Illuminate\Http\JsonResponse
     */
    public function createUser()
    {
        // Response
        $rules = [
            'email'     => 'required|email|unique:lsd_users|max:255',
            'password'  => 'required|min:10',
            'first_name'=> 'required|max:255',
            'last_name' => 'required|max:255',
            'photo'     => 'required|imageBase64'
        ];

        // Validator
        $validator = \Validator::make(Input::all(),$rules);

        if ($validator->passes())
        {
            // init std to summit the user data
            $data = new \stdClass();
            $data->email        = $this->xss_clean(Input::get('email'));
            $data->password     = \Hash::make(Input::get('password'));
            $data->first_name   = $this->xss_clean(Input::get('first_name'));
            $data->last_name    = $this->xss_clean(Input::get('last_name'));

            $path ='uploads/images/users/user_' . str_random(10).'.'.'png';

            \File::put( $path, base64_decode(Input::get('photo')));

            $data->photo = $path;


                // creation
            $check = $this->repo->create($data);

            if($check)
            {
                $res = [
                    'msg'       => 'User, Created.',
                    'errors'    => $this->repo->getErrors()
                ];

                return response()->json($res, $this->repo->getStatus());
            }

            unlink($path);

            $res = [
                'msg'       => 'User Creation, Error.',
                'errors'    => $this->repo->getErrors()
            ];
            return response()->json($res, $this->repo->getStatus());


        } else {

            $res = [
                'msg'       => 'Validation Errors',
                'errors'    => $validator->errors()->all()
            ];

            return response()->json($res, 406);
        }
    }

    /** Update user
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateUser($id)
    {
        $rules = [
            'email'     => 'required|email|max:255',
            'password'  => 'required|min:10',
            'first_name'=> 'required|max:255',
            'last_name' => 'required|max:255',
            'photo'     => 'required|imageBase64'
        ];

        //Validator
        $validator = \Validator::make(Input::all(),$rules);

        if ($validator->passes())
        {
            // init std to summit the user data
            $data = new \stdClass();
            $data->email        = $this->xss_clean(Input::get('email'));
            $data->password     = \Hash::make(Input::get('password'));
            $data->first_name   = $this->xss_clean(Input::get('first_name'));
            $data->last_name    = $this->xss_clean(Input::get('last_name'));

            $path ='uploads/images/users/user_' . str_random(10).'.'.'png';

            \File::put( $path, base64_decode(Input::get('photo')));

            $data->photo = $path;


            // Update
            $check = $this->repo->update((int)$id,$data);

            if($check)
            {
                $res = [
                    'msg'       => 'User, Updated.',
                    'errors'    => $this->repo->getErrors()
                ];

                return response()->json($res, $this->repo->getStatus());
            }

            // Remove the moved file
            try{unlink($path);} catch (\Exception $e){}

            $res = [
                'msg'       => 'User updating, Error.',
                'errors'    => $this->repo->getErrors()
            ];

            return response()->json($res, $this->repo->getStatus());


        } else {

            $res = [
                'msg'       => 'Validation Errors',
                'errors'    => $validator->errors()->all()
            ];

            return response()->json($res, 406);
        }
    }


    /** Delete User
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteUser($id){

        if($check = $this->repo->delete((int)$id))
        {
            $res = [
                'msg'       => 'User deleted',
                'errors'    => $this->repo->getErrors()
            ];

            return response()->json($res, $this->repo->getStatus());
        }

        $res = [
            'msg'       => 'Deleting User Error',
            'errors'    => $this->repo->getErrors()
        ];

        return response()->json($res, $this->repo->getStatus());
    }
}