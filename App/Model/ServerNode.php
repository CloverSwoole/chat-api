<?php

namespace App\Model;

use CloverSwoole\Databases\Model;
use CloverSwoole\Swoole\ServerManager;
use CloverSwoole\Utility\Random;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;

/**
 * 服务节点表
 * Class ServerNode
 * @package App\Model
 * @mixin Builder
 */
class ServerNode extends Model
{
    /**
     * 表名
     * @var string
     */
    protected $table = 'server_node';
    /**
     * 使用软删除
     */
    use SoftDeletes;
    /**
     * 获取本机server
     * @return ServerNode|\Illuminate\Database\Eloquent\Model|null|object
     */
    public static function get_local_node()
    {
        /**
         * 获取SWOOlE Server
         */
        $swoole_server = ServerManager::getInterface() -> getSwooleRawServer();
        /**
         * 判断server 是否已经注册
         */
        if(!($node_info = self::get_server_node($swoole_server -> host,$swoole_server -> port))){
            /**
             * 注册 server_node
             */
            $node_info = self::register_node('',$swoole_server -> host,$swoole_server -> port);
        }
        /**
         * 返回节点信息
         */
        return $node_info;
    }
    public static function get_server_node($host='0.0.0.0',$port='9501')
    {
        return self::where(['node_host'=>$host,'node_port'=>$port]) -> first();
    }

    /**
     * 注册节点
     * @param string $name
     * @param string $host
     * @param string $port
     * @return ServerNode|\Illuminate\Database\Eloquent\Model|null|object
     */
    public static function register_node($name = '',$host='0.0.0.0',$port='9501')
    {
        /**
         * 定义基础数据
         */
        $data = ['created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')];
        /**
         * 判断节点是否已被注册
         */
        if($node_info = self::get_server_node($host,$port)){
            return $node_info;
        }
        /**
         * 生成node_key
         */
        do{
            $data['node_key'] = Random::randStr('50');
        }while(self::where(['node_key'=>$data['node_key']]) -> first());
        /**
         * node name
         */
        $data['node_name'] = $name;
        $data['node_host'] = $host;
        $data['node_port'] = $port;
        /**
         * 创建node
         */
        $id = self::insertGetId($data);
        /**
         * 查询新数据
         */
        return self::where(['id'=>$id]) -> first();
    }
}