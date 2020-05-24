<?php
namespace app\admin\controller;
use think\Controller;

//公共方法
class Init extends Controller
{   

    public function _initialize()
    {   
         //若session没有取到值，则返回登录系统！！
        // if( !session('id') ){                        
        //      $this->error('请先登录系统！',url('login/login'));
        //      // return returnjson(1,'');
        // }    
    }


}
