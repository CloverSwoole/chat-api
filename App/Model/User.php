<?php

namespace App\Model;

use CloverSwoole\Databases\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;

/**
 * 数据库基础模型
 * Class Users
 * @package App\Model
 * @mixin Builder
 */
class User extends Model
{
    /**
     * 表名
     * @var string
     */
    protected $table = 'users';
    /**
     * 使用软删除
     */
    use SoftDeletes;
}