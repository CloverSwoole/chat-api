<?php

namespace App\Model;

use CloverSwoole\Databases\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;

/**
 * 会话消息列表
 * Class SessionLists
 * @package App\Model
 * @mixin Builder
 */
class SessionMessage extends Model
{
    /**
     * 未读
     */
    CONST UNREAD = 1;
    /**
     * 已读
     */
    CONST READ = 2;
    /**
     * 是否已经读取了
     */
    CONST IS_READ = [self::UNREAD => '未读', self::READ => '已读'];
    /**
     * 表名
     * @var string
     */
    protected $table = 'session_message';
    /**
     * 使用软删除
     */
    use SoftDeletes;
}