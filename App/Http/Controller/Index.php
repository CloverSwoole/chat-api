<?php
namespace App\Http\Controller;
use App\Model\UserToken;
use CloverSwoole\Http\Abstracts\Controller;
use CloverSwoole\Http\Response;

/**
 * 默认控制器
 * Class Index
 * @package App\Http\Controller
 */
class Index extends Controller
{
    public function index()
    {
        Response::dump(UserToken::create_token(1,1));
    }
}