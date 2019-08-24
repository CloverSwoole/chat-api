<?php
namespace App\Http\Controller\Admin;

use App\Model\Admin\Navigation as NavigationModel;

/**
 * 公告接口
 * Class Common
 * @package App\Http\Controller\Admin
 */
class Navigation extends BaseControoler
{
    /**
     * 获取导航
     */
    public function lists()
    {
        /**
         * 获取全部
         */
        $data = NavigationModel::where(['pid'=>0]) -> get();
        /**
         * 关联子集
         */
        $data -> load('children');
        foreach ($data as $item){
            $item -> children -> load('children');
        }
        $nav_lists = [];

        foreach ($data as $item){
            $nav_lists[] = [
                'id'=>$item['id'],
                'name'=>$item['name'],
                'path'=>$item['path'],
                'icon'=>$item['icon'],
                'component'=>$item['component'],
                'hidden'=>$item['hidden'],
                'redirect'=>$item['redirect'],
                'created_at'=>$item -> created_at->format('Y-m-d H:i:s'),
                'updated_at'=>$item->updated_at->format('Y-m-d H:i:s'),
                'pid'=>$item['pid'],
            ];
            foreach ($item->children as $children_item){
                $nav_lists[] = [
                    'id'=>$children_item['id'],
                    'name'=>'|->'.$children_item['name'],
                    'path'=>$children_item['path'],
                    'icon'=>$children_item['icon'],
                    'component'=>$children_item['component'],
                    'hidden'=>$children_item['hidden'],
                    'redirect'=>$children_item['redirect'],
                    'created_at'=>$children_item -> created_at->format('Y-m-d H:i:s'),
                    'updated_at'=>$children_item->updated_at->format('Y-m-d H:i:s'),
                    'pid'=>$children_item['pid'],
                ];
                foreach ($children_item -> children as $cc_item){
                    $nav_lists[] = [
                        'id'=>$cc_item['id'],
                        'name'=>'|->|->'.$cc_item['name'],
                        'path'=>$cc_item['path'],
                        'icon'=>$cc_item['icon'],
                        'component'=>$cc_item['component'],
                        'hidden'=>$cc_item['hidden'],
                        'redirect'=>$cc_item['redirect'],
                        'created_at'=>$cc_item -> created_at->format('Y-m-d H:i:s'),
                        'updated_at'=>$cc_item->updated_at->format('Y-m-d H:i:s'),
                        'pid'=>$cc_item['pid'],
                    ];
                }
            }
        }
        /**
         * 返回信息
         */
        return $this->returnJson($nav_lists);
    }

    /**
     * 添加导航
     */
    public function create()
    {
        try{
            NavigationModel::add($this -> getRequestParam());
            return $this -> returnJson(['status'=>200,'msg'=>'添加成功']);
        }catch (\Throwable $throwable){
            return $this -> returnJson(['status'=>$throwable -> getCode(),'msg'=>$throwable -> getMessage()]);
        }
    }

    /**
     * 获取指定导航的信息 TODO
     */
    public function show()
    {

    }
    /**
     * 删除导航
     */
    public function remove()
    {
        try{
            /**
             * 删除
             */
            NavigationModel::remove($this -> getRequestParam('ids'));
            /**
             * 返回信息
             */
            return $this -> returnJson(['status'=>200,'msg'=>'删除成功']);
        }catch (\Throwable $throwable){
            return $this -> returnJson(['status'=>$throwable -> getCode(),'msg'=>$throwable -> getMessage()]);
        }
    }
}