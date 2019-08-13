<?php
namespace App\Socket\Controller\Im;

use App\Socket\Controller\BaseController;

/**
 *
 * Class User
 * @package App\Socket\Controller\Im
 */
class User extends BaseController
{
    public function user_info()
    {
        $this ->returnJson($this -> get_request_data());
    }
}