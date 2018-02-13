<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use SebastianBergmann\Environment\Console;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SYSTEAMInitRBAC extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'systeam:init-rbac';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '初始化权限管理系统';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (Role::all()->count() > 0){
            $this->error('不能重复初始化');
            die();
        }

        // 创建用户
        $password_hash = Hash::make($password = Str::random(12));
        /** @var User $admin */
        $admin = User::create(['email' => 'admin@local.com', 'password' => $password_hash]);

        // 创建 Role 和 Permission 并赋权
        $admin_role = Role::create(['name' => 'admin']);
        $admin_permissions = [
            'manage users',
            'manage products',
            'manage categories',
            'manage orders',
            'manage payment',
            'manage website settings'
        ];
        foreach ($admin_permissions as $permission){
            Permission::create(['name' => $permission]);
        }

        $admin_role->syncPermissions($admin_permissions);

        // 赋予admin role
        $admin->assignRole($admin_role);

        $this->info('成功初始化权限系统');
        $this->info('管理员账号： admin@local.com');
        $this->info('管理员密码：' . $password);

    }
}
