<?php
namespace App\Http\Controller\Admin;

use App\Model\Admin\Navigation;

/**
 * 公告接口
 * Class Common
 * @package App\Http\Controller\Admin
 */
class Common extends BaseControoler
{
    /**
     * 获取导航
     */
    public function get_navigation()
    {
        /**
         * 获取全部
         */
        $data = Navigation::where(['pid'=>0]) -> get();
        /**
         * 关联子集
         */
        $data -> load('children');
        foreach ($data as $item){
            $item -> children -> load('children');
        }
        /**
         * 返回信息
         */
        return $this->returnJson($data);
    }

}