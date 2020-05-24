<?php
namespace app\admin\controller;
use think\Controller;
use think\Request;
use think\View;
use think\Session;
use think\Db;
class Admin extends Controller
{

/**
* 首页框架
* @return [type] [description]
*/
    public function index()
    {
        $view = new View();
        $title="首页";
        $view->assign('title',$title);
        $uid = Session::get('adminuser')['id'];
        $template = "";
        if(!$uid){ // 未登录
            // 标记是否已经登录
            $view->logined = 0;
            // 指定加载的模板文件
            $template = "login";
        }else{ // 已登录
            // 标记是否已经登录
            $view->logined = 1;
            // 指定加载的模板文件
            $template = "index";

        } 
        // 渲染页面
        return $view->fetch($template);
    } 
 

/**
* 获取页面 
* @return [type] [description]
*/
public function getpage(){
    $pageurl = input('id')?input('id'):'error_404';
    $view = new View();  
    // p(set_comview()); 
    $view->assign('code',set_comview()); 
    return $view->fetch($pageurl);
}




















}