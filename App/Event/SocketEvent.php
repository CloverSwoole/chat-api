<?php
namespace App\Event;

use App\Exception\SocketException;
use Swoole\Http\Request;
use Swoole\Server;

/**
 * Class SocketEvent
 * @package App\Event
 */
class SocketEvent
{
    /**
     * 连接成功
     * @param Server $server
     * @param Request $request
     * @param array $socket_client
     */
    public static function openSuccess(Server $server, Request $request,array $socket_client = [])
    {
        /**
         * 返回链接成功信息到客户端
         */
        $server->push($request->fd, json_encode(['status' => 200, 'msg' => '连接成功', 'uuid' => isset($socket_client['uuid'])?$socket_client['uuid']:null]));
    }

    /**
     * 链接失败
     * @param \Throwable $throwable
     * @param Server $server
     * @param Request $request
     */
    public static function openFail(\Throwable $throwable,Server $server, Request $request)
    {
        /**
         * 处理异常
         */
        SocketException::catchOpenException($throwable,$server,$request);
    }
}