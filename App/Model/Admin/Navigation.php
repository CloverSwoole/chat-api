<?php
namespace App\Model\Admin;

use CloverSwoole\Databases\Model;
use CloverSwoole\Utility\FindVar;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Navigation
 * @package App\Model\Admin
 */
class Navigation extends Model
{
    /**
     * 软删
     */
    use SoftDeletes;
    /**
     * 表名
     */
    protected $table = 'admin_navigation';

    /**
     * 添加
     * @param array $param
     * @return int
     * @throws \Exception
     */
    public static function add($param = [])
    {
        /**
         * 默认数据
         */
        $insert_data = ['created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')];
        /**
         * 名称
         */
        if(strlen(FindVar::findVarByExpression('name',$param)) > 0){
            $insert_data['name'] = FindVar::findVarByExpression('name',$param);
        }else{
            throw new \Exception('名称不能为空',400);
        }
        /**
         * 图标
         */
        if(strlen(FindVar::findVarByExpression('icon',$param)) > 0){
            $insert_data['icon'] = FindVar::findVarByExpression('icon',$param);
        }else{
            throw new \Exception('图标不能为空',400);
        }
        /**
         * 组件名称
         */
        if(strlen(FindVar::findVarByExpression('component',$param)) > 0){
            $insert_data['component'] = FindVar::findVarByExpression('component',$param);
        }else{
            throw new \Exception('组件不能为空',400);
        }
        /**
         * 路径
         */
        if(strlen(FindVar::findVarByExpression('path',$param)) > 0){
            $insert_data['path'] = FindVar::findVarByExpression('path',$param);
        }else{
            throw new \Exception('路径不能为空',400);
        }
        /**
         * 父级导航
         */
        if(strlen(FindVar::findVarByExpression('pid',$param)) > 0 && self::find(FindVar::findVarByExpression('pid',$param))){
            $insert_data['pid'] = FindVar::findVarByExpression('pid',$param);
        }else{
            $insert_data['pid'] = 0;
        }
        /**
         * 是否隐藏
         */
        if(in_array(FindVar::findVarByExpression('hidden',$param),[0,1])){
            $insert_data['hidden'] = FindVar::findVarByExpression('hidden',$param);
        }else{
            $insert_data['hidden'] = 0;
        }
        /**
         * 插入数据
         */
        if($id = self::insertGetId($insert_data)){
            return $id;
        }else{
            throw new \Exception('创建失败',400);
        }
    }

    /**
     * 删除
     * @param array|string|int $ids
     * @return bool|null
     * @throws \Exception
     */
    public static function remove($ids)
    {
        if(!is_array($ids)){
            if(is_string($ids) || is_int($ids)){
                $ids = [$ids];
            }else{
                throw new \Exception('id类型错误');
            }
        }
        /**
         * 判断是否存在子级
         */
        if(self::whereIn('pid',$ids) -> first()){
            throw new \Exception('要删除的菜单存在子级');
        }
        /**
         * 删除
         */
        return self::whereIn('id',$ids) -> delete();
    }

    /**
     * 关联子集
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this -> hasMany(self::class,'pid','id');
    }
}