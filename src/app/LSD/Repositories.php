<?php
/**
 * Created by PhpStorm.
 * User: maxxi
 * Date: 1/29/2018
 * Time: 2:16 PM
 */

namespace App\LSD;


class Repositories
{
    private $errors = [];
    private $status = 200;


    /** Set Errors
     * @param $errors
     * @return bool
     */
    public function setErrors($errors)
    {
        try {

            if(is_string($errors))
                $this->errors = array_merge($this->errors, [$errors]);
            else
                $this->errors = array_merge($this->errors, $errors);
            return true;
        } catch (Exception $exception){
//            die("Services ERRORS Merging Exception");
            return false;
        }
    }

    /**
     * @return mixed
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /** Set current response status
     * @param int $s
     */
    public function setStatus($s = 200){
        $this->status = $s;
    }

    /** Current Status
     * @return int
     */
    public function getStatus(){
        return $this->status;
    }
}