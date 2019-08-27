<?php
namespace App\Http\Controller\Admin;

/**
 * 授权控制器
 * Class Auth
 * @package App\Http\Controller\Admin
 */
class Auth extends BaseControoler
{
    /**
     * 登录
     */
    public function login()
    {
        return $this -> returnJson(['status'=>200,'msg'=>'登录成功','token'=>'1111111111111']);
    }
}