<?php
/**
 * 自动加载规则
 */
include('./vendor/autoload.php');
/**
 * 数据库配置
 */
\App\Config\Db::init();
/**
 * 清空数据库
 */
\CloverSwoole\Databases\DB::schema()->dropAllTables();
/**
 * Socket 客户端连接表
 */
\CloverSwoole\Databases\DB::schema()->create('socket_client', function ($table) {
    /**
     * @var Illuminate\Database\Schema\Blueprint $table
     */
    $table->increments('id');
    $table->integer('node')->index();
    $table->integer('token')->index();
    $table->integer('fd')->index();
    $table->string('uuid')->index();
    $table->string('key')->index();
    $table->string('origin')->index();
    $table->string('user_agent')->index();
    $table->timestamp('created_at')->nullable()->index();
    $table->timestamp('updated_at')->nullable()->index();
    $table->softDeletes()->index();
});
/**
 * 服务节点表
 */
\CloverSwoole\Databases\DB::schema()->create('server_node', function ($table) {
    /**
     * @var Illuminate\Database\Schema\Blueprint $table
     */
    $table->increments('id');
    $table->string('node_name')->index();
    $table->string('node_key')->index();
    $table->string('node_host')->index();
    $table->string('node_port')->index();
    /**
     * 最后心跳活动时间
     */
    $table->timestamp('last_active')->index();
    $table->timestamp('created_at')->nullable()->index();
    $table->timestamp('updated_at')->nullable()->index();
    $table->softDeletes()->index();
});
/**
 * 用户授权表
 */
\CloverSwoole\Databases\DB::schema()->create('user_token', function ($table) {
    /**
     * @var Illuminate\Database\Schema\Blueprint $table
     */
    $table->increments('id');
    $table->integer('user_id')->index();
    $table->string('token')->index();
    $table->timestamp('expires_time')->index();
    $table->timestamp('created_at')->nullable()->index();
    $table->timestamp('updated_at')->nullable()->index();
    $table->softDeletes()->index();
});
/**
 * 用户表
 */
\CloverSwoole\Databases\DB::schema()->create('users', function ($table) {
    /**
     * @var Illuminate\Database\Schema\Blueprint $table
     */
    $table->increments('id');
    $table->string('account')->index();
    $table->string('password')->index();
    $table->string('nickname')->index();
    $table->string('username')->index();
    $table->string('head_img')->index();
    $table->timestamp('created_at')->nullable()->index();
    $table->timestamp('updated_at')->nullable()->index();
    $table->softDeletes()->index();
});
/**
 * 会话列表
 */
\CloverSwoole\Databases\DB::schema()->create('session_lists', function ($table) {
    /**
     * @var Illuminate\Database\Schema\Blueprint $table
     */
    $table->increments('id');
    $table->integer('type')->index();
    $table->integer('from_id')->index();
    $table->integer('user_id')->index();
    $table->timestamp('last_talk_time')->index();
    $table->timestamp('created_at')->nullable()->index();
    $table->timestamp('updated_at')->nullable()->index();
    $table->softDeletes()->index();
});
/**
 * 消息列表
 */
\CloverSwoole\Databases\DB::schema()->create('session_message', function ($table) {
    /**
     * @var Illuminate\Database\Schema\Blueprint $table
     */
    $table->increments('id');
    $table->integer('session_id')->index();
    $table->integer('type')->index();
    $table->text('body');
    $table->integer('user_id')->index();
    $table->integer('from_id')->index();
    $table->integer('is_read')->index()->default(\App\Model\SessionMessage::UNREAD);
    $table->timestamp('created_at')->nullable()->index();
    $table->timestamp('updated_at')->nullable()->index();
    $table->softDeletes()->index();
});
/**
 * 后台管理节点表
 */
\CloverSwoole\Databases\DB::schema()->create('admin_navigation', function ($table) {
    /**
     * @var Illuminate\Database\Schema\Blueprint $table
     */
    $table->increments('id');
    $table->string('name');
    $table->string('pid');
    $table->string('path');
    $table->string('icon');
    $table->string('component');
    $table->string('hidden');
    $table->string('redirect')->nullable();
    $table->timestamp('created_at')->nullable()->index();
    $table->timestamp('updated_at')->nullable()->index();
    $table->softDeletes()->index();
});