<?php
namespace App\Socket\Controller;

use App\Model\ServerNode;
use App\Model\SocketClient;
use App\Model\User;
use App\Model\UserToken;
use CloverSwoole\Swoole\ServerManager;
use CloverSwoole\Utility\FindVar;
use CloverSwoole\Utility\Random;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

/**
 * 基础控制器
 * Class BaseController
 * @package App\SocketController
 */
class BaseController
{
    /**
     * 连接信息
     * @var null | SocketClient
     */
    protected $socket_client = null;
    /**
     * 用户授权信息
     * @var null | UserToken
     */
    protected $user_token = null;
    /**
     * 用户信息
     * @var null | User
     */
    protected $user_info = null;
    /**
     * 请求的数据
     * @var array|mixed|null
     */
    protected $request_data = null;
    /**
     * @var null | Frame
     */
    protected $frame = null;
    /**
     * BaseController constructor.
     * @param Frame $frame
     */
    public function __construct(Frame $frame)
    {
        /**
         * 存储frame
         */
        $this -> frame = $frame;
        try {
            /**
             * 调用操作
             */
            $this->{FindVar::findVarByExpression('action',$this -> request_data)}();
        } catch (\Throwable $throwable) {
            /**
             * 处理异常
             */
            $this -> onException($this -> request_data['action'],$throwable);
        }
    }

    /**
     * 异常处理
     * @param $actionName
     * @param \Throwable $throwable
     */
    protected function onException($actionName,\Throwable $throwable)
    {
        $this -> returnJson(['actionName'=>$actionName,'status'=>$throwable -> getCode(),'msg'=>$throwable -> getMessage()]);
    }

    /**
     * 获取Socket Client 信息
     * @param null $name
     * @return array|mixed
     */
    protected function get_socket_client($name = null)
    {
        if($this -> socket_client == null){
            /**
             * 获取node
             */
            $node_id = ServerNode::where(['node_host' => ServerManager::getInterface() -> getSwooleRawServer() ->host, 'node_port' => ServerManager::getInterface() -> getSwooleRawServer()->port])->value('id');
            /**
             * 获取socket
             */
            $this -> socket_client = SocketClient::where(['fd' => $this -> frame ->fd, 'node' => $node_id])->first();
        }
        return FindVar::findVarByExpression($name,$this -> socket_client);
    }

    /**
     * 获取用户信息
     * @param null $name
     * @return User|\Illuminate\Database\Eloquent\Model|mixed|null|object
     */
    protected function get_user_info($name = null)
    {
        /**
         * 判断token 是否已经初始化过
         */
        if($this -> user_token === null){
            $this -> user_token = UserToken::withTrashed() -> where(['id'=>$this -> get_socket_client('token')]) -> first();
        }
        /**
         * 判断用户信息是否已经获取过
         */
        if($this -> user_info === null){
            $this -> user_info = User::where(['id'=>$this -> user_token['user_id']]) -> first();
        }
        /**
         * 返回用户的信息
         */
        return FindVar::findVarByExpression($name,$this -> user_info);
    }

    /**
     * 返回JSON 数据
     * @param $data
     * @param null $fd
     * @return mixed
     */
    protected function returnJson($data,$fd = null)
    {
        /**
         * 处理request_id
         */
        $request_id = is_string($this -> get_request_data('request_id')) && strlen($this -> get_request_data('request_id'))<1?Random::randStr(50):$this -> get_request_data('request_id');
        /**
         * 请求id
         */
        $data['request_id'] = $request_id;
        /**
         * 推送消息
         */
        ServerManager::getInterface() -> getSwooleRawServer() -> push($fd === null?$this -> frame -> fd:$fd,json_encode($data));
    }

    /**
     * 获取请求的数据
     * @param string $name
     * @return array|mixed|null
     */
    protected function get_request_data($name = null)
    {
        return FindVar::findVarByExpression($name,$this -> request_data);
    }
}