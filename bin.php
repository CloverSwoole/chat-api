<?php
/**
 * 引入自动加载规则
 */
include "vendor/autoload.php";
/**
 * 数据库配置
 */
\App\Config\Db::init();

///**
// * 入列
// */
//\App\Service\PushQueue::getInterface(1)->push(1,['event'=>'onMessage','msg'=>'你好呀']);
//\App\Service\PushQueue::getInterface(1)->push(1,['event'=>'onMessage','msg'=>'你好呀']);
//\App\Service\PushQueue::getInterface(1)->push(1,['event'=>'onMessage','msg'=>'你好呀']);
//
///**
// * 出列
// */
//foreach (\App\Service\PushQueue::getInterface(1)->pop(50) as $item){
//    if(!$item){break;}
//    dump($item);
//}
// 接管连接事件
\CloverSwoole\Swoole\SwooleSocket\SocketHandler::setOnOpenHandler(function(\Swoole\Server $server, \Swoole\Http\Request $request){
    return \App\Service\Socket::onOpen(...func_get_args());
});
// 接管消息到达事件
\CloverSwoole\Swoole\SwooleSocket\SocketHandler::setOnMessageHandler(function(\Swoole\Server $server, \Swoole\WebSocket\Frame $frame){
    return \App\Service\Socket::onMessage(...func_get_args());
});
// 接管连接关闭事件
\CloverSwoole\Swoole\SwooleSocket\SocketHandler::setOnCloseHandler(function(\Swoole\Server $server, int $fd){
    return \App\Service\Socket::onClose(...func_get_args());
});

/**
 * 运行Swoole Web and Scoket Server
 */
\CloverSwoole\CloverSwoole::runSwooleSocketServer();