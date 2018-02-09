<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SYSTEAMInstallCommand extends Command
{
    protected $signature = 'systeam:install';

    protected $description = '自动安装并初始化系统';

    public function handle()
    {
        if (!empty(env('APP_KEY')) || !empty(env('JWT_SECRET'))){
            $this->error('不能重复安装');
            die();
        }

        $this->info('生成应用密匙');
        Artisan::call('key:generate');
        Artisan::call('jwt:secret', ['-f' => true]);
        $this->info('Done.');

        $this->info('执行数据库迁移');
        Artisan::call('migrate');
        $this->info('Done.');

        $this->info('初始化权限管理系统');
        Artisan::call('systeam:init-rbac');
        $this->info('----------');
        $this->info(Artisan::output());
        $this->info('----------');
        $this->info('Done.');

        //TODO: 填充网站设置默认值

        $this->info('安装完成');

    }
}