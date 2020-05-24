<?php
namespace app\api\controller;
use think\Controller;
use think\Request;

//公共方法
class Init extends Controller
{   

    public function _initialize()
    {   
    	// $_POST = json_decode(file_get_contents('php://input'),true);
      //   if(Request::instance()->isPost()){
    	 //  if(!user_auth()){ 
		    //     echo json_encode(['code'=>'-1','msg'=>'登录过期']);
		    //     exit();
		    // }

      //    } 
       

    }


}
