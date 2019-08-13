<?php
namespace App\Socket\Controller;

use App\Model\SocketClient;
use App\Model\User;
use App\Model\UserToken;
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
     * SwooleServer
     * @var null|Server
     */
    protected $server = null;
    /**
     * opcode
     * @var null
     */
    protected $opcode = null;
    /**
     * 请求的数据
     * @var array|mixed|null
     */
    protected $request_data = null;
    /**
     * fd
     * @var null
     */
    protected $fd = null;

    /**
     * BaseController constructor.
     * @param SocketClient $socket_client
     * @param Server $server
     * @param Frame $frame
     */
    public function __construct(SocketClient $socket_client, Server $server, Frame $frame)
    {
        $this -> socket_client = $socket_client;
        $this -> server = $server;
        $this -> fd = $frame -> fd;
        $this -> request_data = strlen($frame -> data) > 0?json_decode($frame -> data,1):[];
        $this -> opcode = $frame -> opcode;
    }

    /**
     * 获取用户信息
     * @param null $name
     * @return User|\Illuminate\Database\Eloquent\Model|mixed|null|object
     */
    protected function get_user_info($name = null)
    {
        if($this -> user_token === null){
            $this -> user_token = UserToken::withTrashed() -> where(['id'=>$this -> socket_client['token']]) -> first();
        }
        if($this -> user_info === null){
            $this -> user_info = User::where(['id'=>$this -> user_token['user_id']]) -> first();
        }
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
        $this -> server -> push($fd === null?$this -> fd:$fd,json_encode($data));
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