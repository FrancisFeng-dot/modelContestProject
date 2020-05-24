<?php
namespace app\api\controller;
use think\Controller;
use think\Request;
use think\View;
use think\File;
use think\Db;
use think\Session;
use think\Route;
use think\Loader;
define("TOKEN","linxcABCDEFGHIJGJJ");
define("Appid","wx3edf97fba911c055");
define("AppSecret","069f867e0970e0d563abbbe80b84ffc3");

class Open extends Controller
{
    /**
     * 微信接入验证
     * @return [type] [description]
    */
    public function index(){
        $view = new View();  
        if (!Session::get('jsoninfo')) {
	        if (array_key_exists('code',$_GET)||Session::get('wxopenid')) {
	        	if (Session::get('wxopenid')) {
	        		$openid = Session::get('wxopenid');
	        	}else{
		            $code = $_GET['code'];
		            $oauth2url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".Appid."&secret=".AppSecret."&code={$code}&grant_type=authorization_code";
	                $jsoninfo = $this->http_curl($oauth2url,null);
	                $access_token = $jsoninfo['access_token'];
	                $openid = $jsoninfo['openid'];
	        	}
                $url = "https://api.weixin.qq.com/sns/userinfo?access_token={$access_token}&openid={$openid}&lang=zh_CN";      
                $jsoninfo = $this->http_curl($url,null);        
                Session::set('jsoninfo',$jsoninfo); 
                Session::set('wxopenid',$openid);              
		    }
		    else{
		        header(wechatRedirect('index'));   
		        exit(); 
		    }            	
        }
        // p($jsoninfo);
    }   

   
    /**
     * 获取全局的access_token方法
     * @return [type] [description]
     */
    public function getAccessToken(){
    	$field = 'access_token,modify_time';
    	$condition = array('token'=>TOKEN,'appid'=>Appid,'appsecret'=>AppSecret);
    	// $data = M('wechat')->field($field)->where($condition)->find();
    	// if($data['access_token'] && time()-$data['modify_time']<7000){
    	// 	$access_token = $data['access_token'];
    	// }else{
    		$url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.Appid.'&secret='.AppSecret.'';
    		$jsoninfo = $this->http_curl($url,null);
    		if(!$jsoninfo){
    			var_dump($jsoninfo);
    		}else{
    			$access_token = $jsoninfo['access_token'];
    			$data = array('access_token' =>$access_token,'modify_time'=>time());
    			// M('wechat')->where($condition)->save($data);
    		}
    	// }
    	return $access_token;
    }

    /**
     * curl方法
     * @param  [type] $url  [description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function http_curl($url,$data){
	    //1.初始化curl
	    $ch = curl_init();
	    //2.设置curl的参数
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
	    if($data){
	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: '.strlen($data)));
	    }
	    //3.采集
	    $output = curl_exec($ch);
	    //4.关闭
	    curl_close($ch);
	    $jsoninfo = json_decode($output, true);
	    return $jsoninfo;
    }
	/*
	 * 辅助方法1：微信接入验证
	 */
	public function main(){
	  $token = 'linxclinxc';
	  $nonce = $_GET["nonce"];  
	  $timestamp = $_GET["timestamp"];
	  // $echoStr = $_GET["echostr"];
	  $signature = $_GET["signature"];
	  $tmpArr = array($token, $timestamp, $nonce);
	  sort($tmpArr, SORT_STRING);
	  $tmpStr = implode( $tmpArr );
	  $tmpStr = sha1( $tmpStr );
	  // if( $tmpStr == $signature && $echoStr){
	  //   // $this->responseMsg();
	  //   ob_clean();
	  //   echo $echoStr;
	  //   exit;
	  // }else{
	    $this->responseMsg();
	  // }
	}

    /**
     * 发送消息
     * @return [type] [description]
     */
    public function responseMsg(){
      $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];               
      $postObj = simplexml_load_string($postStr,"SimpleXMLElement",LIBXML_NOCDATA);//XML转String
    //根据消息类型将信息分发
      if(strtolower( $postObj->MsgType) == 'event'){
          if(strtolower($postObj->Event) == 'subscribe'){
                $toUserName = $postObj->FromUserName;
                $fromUserName = $postObj->ToUserName;
                $createTime = time();
                $msgType = 'text';
                $content = "【私募招聘网】是一家专注私募行业求职、招聘的垂直平台；通过线上招聘+线下社交的创新模式为企业实现快速的人才战略。为私募企业和人才搭建桥梁；让招聘、求职变得更加简单、高效！
快速进入找工作：http://www.91smzpw.com/jobs
 【私募社群】是私募招聘网旗下专注私募行业的垂直社交平台；为用户提供人脉拓展、建立渠道合作、对接优质项目、交流学习的机会聚积地；我们的梦想是连接每一个私募人；在未来的道路上互相帮助、学习、成长！
 欢迎阁下加入；一起共创美好未来；点击下方的【私募社群】即可加入平台";
                $template ="<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>
                            <Content><![CDATA[%s]]></Content>
                            </xml>";
                $info = sprintf($template, $toUserName, $fromUserName,$createTime, $msgType,$content);
                echo $info;
          }
      }
    }
    
	public function img()
	{
	  $view = new View();  
	    return $view->fetch();
	}  
	public function attend()
	{
	  $view = new View();  
	    return $view->fetch();
	}  

	public function create_menu(){
	  $appid = Appid;
	  $appsecret = AppSecret;
	  //下面是测试号的appid和appsecrect
	  // $appid = 'wx855ea7a09cac1332';
	  // $appsecret = '61898f7349e81bda57297d900bdb67ba';
	  $accestoken = $this->getAccessToken($appid,$appsecret);
	  $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token={$accestoken}";
	  $menu = '{
	    "button": [
	        {
	            "type": "view",
	            "name": "再梦江南",
	            "url" : "https://open.weixin.qq.com/connect/oauth2/authorize?appid='.Appid.'&redirect_uri=http%3a%2f%2fwww.chenbaozhong.com%2findex.php%2fIndex%2findex&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect"
	        },    
	        {
	            "type": "view",
	            "name": "找工作",
	            "url" : "http://www.91smzpw.com"
	        },  
	        {
	            "type": "view", 
	            "name": "BOSS微信", 
	            "url":"http://www.91smzpw.com/simu/index.php/index/img"
	        } 
	    ]
	  }';  
	    $jsoninfo = $this->http_curl($url,$menu);
	    var_dump($jsoninfo);
	    exit;
	} 







	public function test(){
		// return json(input('post.'));
	  yscheck(input('post.'));
	} 






}