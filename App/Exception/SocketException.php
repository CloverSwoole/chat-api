<?php
namespace App\Exception;


use Swoole\Http\Request;
use Swoole\Server;

/**
 * Socket 异常处理
 * Class SocketException
 * @package App\Exception
 */
class SocketException
{
    /**
     * 捕获链接异常
     * @param \Throwable $throwable
     * @param Server $server
     * @param Request $request
     */
    public static function catchOpenException(\Throwable $throwable,Server $server, Request $request)
    {
        // 记录日志 TODO
        // 关闭连接
        $server->close($request->fd);
    }
}