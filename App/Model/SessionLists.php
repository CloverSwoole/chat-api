<?php

namespace App\Model;

use CloverSwoole\Databases\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;

/**
 * 会话列表
 * Class SessionLists
 * @package App\Model
 * @mixin Builder
 */
class SessionLists extends Model
{
    /**
     * 表名
     * @var string
     */
    protected $table = 'session_lists';
    /**
     * 使用软删除
     */
    use SoftDeletes;
}