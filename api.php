<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]


// 定义应用目录
define('APP_PATH', __DIR__ . '/php/application/');
//开启调试模式
define('APP_DEBUG',true);
// 加载框架引导文件
require __DIR__ . '/php/thinkphp/base.php';


// 绑定当前入口文件到admin模块
\think\Route::bind('api');
// 关闭admin模块的路由
 \think\App::route(false);
// 执行应用
\think\App::run()->send();
