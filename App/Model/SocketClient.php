<?php

namespace App\Model;

use CloverSwoole\Databases\Model;
use CloverSwoole\Utility\Random;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Swoole\Http\Request;
use Swoole\Server;

/**
 * Socket 客户端连接
 * Class SocketClient
 * @package App\Model
 * @mixin Builder
 */
class SocketClient extends Model
{
    /**
     * 表名
     * @var string
     */
    protected $table = 'socket_client';
    /**
     * 使用软删除
     */
    use SoftDeletes;
    /**
     * 同一个TOKEN 同一个 UUID 连接数不能超过的数量限制
     */
    CONST UUID_TOKEN_CONNECTION_MAX_NUM = 3;

    /**
     * 创建client
     * @param Server $server
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public static function create_client(Server $server, Request $request)
    {
        /**
         * 判断token格式是否正确
         */
        if(!(isset($request -> get['token']) && strlen($request -> get['token']) > 0)){
            throw new \Exception('token 格式错误');
        }
        /**
         * 查询token是否有效
         */
        if(UserToken::token_invalid($request -> get['token'])){
            throw new \Exception('token 无效或已过期');
        }
        /**
         * 定义默认数据
         */
        $data = ['created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')];
        /**
         * 获取服务节点
         */
        $data['node'] = ServerNode::get_local_node()['id'];
        /**
         * 链接句柄
         */
        if ($request->fd > 0) {
            $data['fd'] = $request->fd;
        } else {
            throw new \Exception('链接句柄无效');
        }
        /**
         * 唯一标识
         */
        if (isset($request->get['uuid']) && strlen($request->get['uuid']) > 0) {
            $data['uuid'] = $request->get['uuid'];
        } else {
            do {
                $data['uuid'] = Random::randStr(50);
            } while (self::where(['uuid' => $data['uuid']])->first());
        }
        /**
         * 同一个token 同一个uuid 总连接数不能 超过指定数量
         */
        if (self::where(['token'=>$request -> get['token'],'uuid'=>$data['uuid']]) -> count() < self::UUID_TOKEN_CONNECTION_MAX_NUM) {
            if ($token_info = UserToken::token_info($request->get['token'])) {
                $data['token'] = $token_info->id;
            } else {
                throw new \Exception('token 信息不存在');
            }
        } else {
            throw new \Exception("token 连接数不能超过:".self::UUID_TOKEN_CONNECTION_MAX_NUM);
        }
        /**
         * socket 链接协商的 key
         */
        if (isset($request->header['sec-websocket-key']) && strlen($request->header['sec-websocket-key']) > 0) {
            $data['key'] = $request->header['sec-websocket-key'];
        } else {
            throw new \Exception('链接协商key 不合法');
        }
        /**
         * 连接来源
         */
        if (isset($request->header['origin']) && strlen($request->header['origin']) > 0) {
            $data['origin'] = $request->header['origin'];
        }
        /**
         * 用户代理
         */
        if (isset($request->header['user-agent']) && strlen($request->header['user-agent'])) {
            $data['user_agent'] = $request->header['user-agent'];
        }
        /**
         * 插入数据
         */
        if (!($id = self::insertGetId($data))) {
            throw new \Exception('连接失败');
        }
        /**
         * 删除链接的token
         */
        UserToken::where(['token'=>$request->get['token']]) -> delete();
        /**
         * 判断是否一个设备多连接
         */
        if (isset($request->get['uuid']) && strlen($request->get['uuid']) > 0) {
            /**
             * 删除询fd及node相同的数据
             */
            self::where('id','<>',$id) -> where(['node'=>$data['node'],'fd'=>$request -> fd]) -> delete();
            /**
             * 查询其他链接是否已经掉线
             */
            $old_cons = self::where('fd', '<>', $request->fd)->where(['token'=>$request -> get['token'],'uuid' => $request->get['uuid'], 'node' => $data['node']])->get();
            if (count($old_cons) > 0) {
                foreach ($old_cons as $con) {
                    if (!$server->exist($con->fd)) {
                        $con->delete();
                    }
                }
            }
        }
        return $data;
    }
}