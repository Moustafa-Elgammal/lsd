<?php

namespace App\LSD\Users\Repositories;


use App\LSD\Repositories;
use App\LSD\Users\Models\Users;
use Illuminate\Database\QueryException;

class UsersRepository extends Repositories
{
    /** Create new user.
     * @param \stdClass $user
     * @return bool
     */
    public function create(\stdClass $user)
    {
        $newUser = new Users();

        try {
            $newUser->email         = $user->email;
            $newUser->password      = $user->password;
            $newUser->first_name    = $user->first_name;
            $newUser->last_name     = $user->last_name;
            $newUser->photo         = $user->photo;
        }
        catch (\Exception $e)
        {
            $this->setErrors('stdClass Exception: creating new user');
            $this->setStatus(500);
            return false;
        }

        try{

            $check = $newUser->save();

            if($check) {
                $this->setStatus(201);
                return true;
            }

            $this->setErrors('Failed: creating new user');
            $this->setStatus(406);
            return false;

        } catch (QueryException $e)
        {
            $this->setErrors('QueryException: creating new user');
            $this->setStatus(500);
            return false;
        }

    }


    /** Update User
     * @param $uid
     * @param \stdClass $newDate
     * @return bool
     */
    public function update($uid, \stdClass $newDate)
    {
        if(!$user = Users::find((int)$uid)) // check user, existence
        {
            $this->setErrors("Failed: finding user id: $uid");
            $this->setStatus(404);
            return false;
        }

        try {
            //old user photo
            $old_image = $user->photo;

            $user->email        = $newDate->email;
            $user->password     = $newDate->password;
            $user->first_name   = $newDate->first_name;
            $user->last_name    = $newDate->last_name;
            $user->photo        = $newDate->photo;
        }
        catch (\Exception $e)
        {
            $this->setErrors("stdClass Exception: Updating new user id: $uid");
            $this->setStatus(500);
            return false;
        }

        try{

            $check = $user->save();

            if($check)
            {
                try

                {
                    unlink($old_image); // Remove
                }catch (\Exception $e) {}

                $this->setStatus(200);
                return true;
            }

            $this->setErrors("Failed: Updating user id: $uid");
            $this->setStatus(406);

            return false;

        } catch (QueryException $e)
        {
            $this->setErrors("QueryException: updating user: $uid");
            $this->setStatus(500);
            return false;
        }


    }

    /** Delete User
     * @param $uid
     * @return bool
     */
    public function delete($uid)
    {
        if(!$user = Users::find((int)$uid)) // check user, existence
        {
            $this->setErrors("Failed: Finding user id: $uid");
            $this->setStatus(404);
            return false;
        }

        try{

            // User image
            $user_image = $user->photo;

            $check = $user->delete();

            if($check) {

                try

                {
                    unlink($user_image); // Remove
                }catch (\Exception $e) {}

                $this->setStatus(200);
                return true;
            }

            $this->setErrors("Failed: Deleting user id: $uid");
            $this->setStatus(406);
            return false;

        } catch (QueryException $e)
        {
            $this->setErrors("QueryException: Deleting user: $uid");
            $this->setStatus(500);
            return false;
        }

    }

    /** List Users with pagination option
     * @param int $limit
     * @param int $id
     * @return mixed
     */
    public function get($limit = 0)
    {
        // list of users
        $users = Users::paginate((int)$limit);
        $this->setStatus(200);
        return $users;
    }

    /** Get User by its id
     * @param $id
     * @return array
     */
    public function single($id){
        $user = Users::find((int)$id);
        if(!$user){
            $this->setStatus(404);
            $this->setErrors("Failed: Finding user id: $id");
            return [];
        }

        $this->setStatus(200);
        return $user;
    }
}