<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->default(0)->comment('父菜单id，0=顶级');
            $table->string('title', 50)->comment('菜单名');
            $table->string('route', 100)->nullable()->comment('绑定的路由名');
            $table->string('permission_slug', 100)->nullable()->comment('需要的权限标识');
            $table->unsignedInteger('sort')->default(0)->comment('排序值，小的在前');
            $table->tinyInteger('status')->default(1)->comment('1显示 0隐藏');
            $table->timestamps();

            $table->index('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
