<?php

namespace App\Model;

use CloverSwoole\Databases\Model;
use CloverSwoole\Utility\Random;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;

/**
 * 用户授权记录
 * Class UserToken
 * @package App\Model
 * @mixin Builder
 */
class UserToken extends Model
{
    /**
     * 表名
     * @var string
     */
    protected $table = 'user_token';
    /**
     * 使用软删除
     */
    use SoftDeletes;
    /**
     * 默认到期天数
     */
    CONST EXPIRES_DAY = 7;

    /**
     * 判断token 是否有效
     * @param string $token
     * @return bool
     */
    public static function token_invalid(string $token)
    {
        return !self::token_info(...func_get_args());
    }

    /**
     * 查询token 信息
     * @param string $token
     * @return mixed
     */
    public static function token_info(string $token)
    {
        return self::where(['token'=>$token]) -> where('expires_time','>',date('Y-m-d H:i:s')) -> first();
    }

    /**
     * 授权
     * @param int $user_id
     * @param int $EXPIRES_DAY
     * @return array
     * @throws \Exception
     */
    public static function create_token(int $user_id,$EXPIRES_DAY = self::EXPIRES_DAY)
    {
        /**
         * 基础数据
         */
        $data = ['created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')];
        /**
         * 用户id
         */
        if(!($user_info = User::where(['id'=>$user_id]) -> first())){
            throw new \Exception('用户不存在',401);
        }else{
            $data['user_id'] = $user_id;
        }
        /**
         * 创建token
         */
        do{
            $data['token'] = Random::randStr(50);
        }while(self::where(['token'=>$data['token']]) -> first());
        /**
         * 到期时间
         */
        $data['expires_time'] = date('Y-m-d H:i:s',time()+($EXPIRES_DAY*86400));
        /**
         * 创建token
         */
        if(!($id = self::insertGetId($data))){
            dump($data);
            throw new \Exception('授权创建失败',402);
        }
        return $data;
    }
}