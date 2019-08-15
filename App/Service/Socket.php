<?php

namespace App\Service;

use App\Event\SocketEvent;
use App\Exception\SocketException;
use App\Model\ServerNode;
use App\Model\SocketClient;
use CloverSwoole\Swoole\ServerManager;
use CloverSwoole\Utility\FindVar;
use CloverSwoole\Utility\Random;
use Swoole\Http\Request;
use Swoole\Websocket\Frame;
use Swoole\Websocket\Server;

/**
 * Class Socket
 * @package App\Service
 */
class Socket
{
    /**
     * 建立连接
     * @param Server $server
     * @param Request $request
     */
    public static function onOpen(Server $server, Request $request)
    {
        try {
            /**
             * 创建客户端
             */
            $data = SocketClient::create_client($server, $request);
            /**
             * 事件通知
             */
            SocketEvent::openSuccess($server,$request,$data);
        } catch (\Throwable $throwable) {
            /**
             * 事件通知
             */
            SocketEvent::openFail($throwable,$server,$request);
        }
    }


    /**
     * 消息到达
     * @param Server $server
     * @param Frame $frame
     */
    public static function onMessage(Server $server, Frame $frame)
    {
        /**
         * 放置server
         */
        (new ServerManager($server)) -> setAsGlobal();
        /**
         * 判断信息是否正确
         */
        if ($frame->opcode == 1 && $frame->finish == true && strlen($frame->data) > 0) {
            $request_data = json_decode($frame->data, 1);
        } else {
            $request_data = [];
        }
        /**
         * 获取请求id
         */
        $request_id = strlen(FindVar::findVarByExpression('request_id',$request_data)) > 0 ? $request_data['request_id'] : Random::randStr(50);
        try {
//            /**
//             * 获取用户连接节点
//             */
//            $node_id = ServerNode::where(['node_host' => $server->host, 'node_port' => $server->port])->value('id');
//            /**
//             * 查询client 信息
//             */
//            if (!($websocket_client = SocketClient::where(['fd' => $frame->fd, 'node' => $node_id])->first())) {
//                throw new \Exception('连接信息不存在');
//            }
            /**
             * 应用名过滤
             */
            if (strlen(FindVar::findVarByExpression('app',$request_data)) < 1) {
                throw new \Exception('应用不存在');
            }
            /**
             * 模块过滤
             */
            if (strlen(FindVar::findVarByExpression('controller',$request_data)) < 1) {
                throw new \Exception('控制器不存在');
            }
            /**
             * 获取消息的操作目的
             */
            $class = '\App\Socket\Controller\\' . $request_data['app'] . '\\' . $request_data['controller'];
            /**
             * 判断控制器是否存在
             */
            if (!class_exists($class)) {
                throw new \Exception("找不到指定控制器");
            }
            /**
             * 调用指定操作
             */
            $ref = new \ReflectionClass($class);
            /**
             * 判断要调用的操作是否存在 及 合法
             */
            if ($ref->hasMethod($request_data['action']) && $ref->getMethod($request_data['action'])->isPublic()) {
                /**
                 * 实例化控制器
                 */
                new $class($websocket_client, $server, $frame);
            } else {
                throw new \Exception('找不到指定操作');
            }
        } catch (\Exception $exception) {
            // 记录日志 TODO
            $server->push($frame->fd, json_encode(['status' => 500, 'msg' => $exception->getMessage(), 'request_id' => $request_id]));
        } catch (\Throwable $throwable) {
            // 记录日志 TODO
            $server->push($frame->fd, json_encode(['status' => 500, 'msg' => $throwable->getMessage(), 'request_id' => $request_id]));
        }
    }

    public static function onClose(\Swoole\Server $server, int $fd)
    {
        dump($fd);
    }
}