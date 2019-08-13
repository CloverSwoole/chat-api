<?php
namespace App\Service;
/**
 * 推送队列(Redis)
 * Class PushQueue
 * @package App\Service
 */
class PushQueue
{
    /**
     * 实例列表
     * @var array
     */
    protected static $interface_lists = [];
    /**
     * 节点
     * @var null | string| int
     */
    protected $node = null;
    /**
     * @var null | \Redis
     */
    protected $redis = null;

    /**
     * 获取实例
     * @param $node
     * @return PushQueue
     */
    public static function getInterface($node):PushQueue
    {
        if(!(isset(self::$interface_lists[$node]) && self::$interface_lists[$node] instanceof PushQueue)){
            self::$interface_lists[$node] = new static(...func_get_args());
        }
        return self::$interface_lists[$node];
    }
    protected function __construct($node)
    {
        $this -> node = $node;
        $this -> redis = new \Redis();
        $this -> redis -> connect('127.0.0.1',6379);
    }
    public function push(int $client_id,$data)
    {
        return $this -> redis -> rPush('clover_swoole_push_queue:'.$this -> node,serialize(['client_id'=>$client_id,'data'=>$data]));
    }

    /**
     * @param $length
     * @return \Generator
     */
    public function
    pop($length = 1)
    {
        for ($i=0;$i<$length;$i++){
            yield unserialize($this -> redis -> lPop('clover_swoole_push_queue:'.$this -> node));
        }
    }
}