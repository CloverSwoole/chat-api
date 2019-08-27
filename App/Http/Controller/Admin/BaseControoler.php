<?php
namespace App\Http\Controller\Admin;

use CloverSwoole\Http\Abstracts\Controller;
use CloverSwoole\Http\Request;
use CloverSwoole\Http\Response;

/**
 * Class BaseControoler
 * @package App\Http\Controller\Admin
 */
class BaseControoler extends Controller
{
    /**
     * 返回JSON数据
     * @param $data
     * @param bool $is_end
     */
    protected function returnJson($data, $is_end = false)
    {
        /**
         * 判断请求是否已经结束
         */
        if (Response::getInterface()->ResponseIsEnd()) {
            return;
        }
        /**
         * 内容类型
         */
        Response::getInterface()->withHeader('Content-Type', 'application/json;charset=utf-8');
        /**
         * 允许跨域访问的来源域名
         */
        Response::getInterface()->withHeader('Access-Control-Allow-Origin', Request::getInterface()->getOriginLocation());
        /**
         * 允许跨域的方法
         */
        Response::getInterface()->withHeader('Access-Control-Allow-Method', 'POST');
        /**
         * 允许客户端附带cookie
         */
        Response::getInterface()->withHeader('Access-Control-Allow-Credentials', 'true');
        /**
         * 允许修改的协议头
         */
        Response::getInterface()->withHeader('Access-Control-Allow-Headers', 'accept, content-type');
        /**
         * 响应码
         */
        Response::getInterface()->withStatus(200);
        /**
         * 响应数据
         */
        Response::getInterface()->withContent(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        /**
         * 判断是否要结束请求
         */
        if ($is_end) {
            Response::getInterface()->endResponse();
        }
    }
}