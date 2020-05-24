<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author:  
// +----------------------------------------------------------------------
use think\Request;
use think\View;
use think\Db;
use think\Session;
use think\Validate;
use think\cache\driver\Redis;
// 配置验证器
/**
 * minishop md5加密方法
 * @author wyl  
 */
function yscheck($data)
{

$rule = [
       'group_name' => 'require',
        'name' => 'require',
        'id'=>'require',
        'name' => 'require',
        'articleid'=>'require',
        'childName' => 'require',
        'childSchool'=>'require',
        'grade' => 'require',
        'parentName'=>'require',
        'parentPhone' => 'require',
        'address'=>'require',
        'urgentContact' => 'require',
        'urgentPhone'=>'require',
        'attention' => 'require',
        'time1' => 'require',
        'time2' => 'require',

        ];


$msg = [
        'group_name.require'  => '用户组不能为空',
        'name.number'  => 'must be num',
        'id.require'  => 'id can not be empty',
        'name.require'  => 'name can not be empty',
        'articleid.require'  => 'articleid can not be empty',
        'childName.require'  => 'childName can not be empty',
        'childSchool.require'  => 'childSchool can not be empty',
        'grade.require'  => 'grade can not be empty',
        'parentName.require'  => 'parentName can not be empty',
        'parentPhone.require'  => 'parentPhone can not be empty',
        'address.require'  => 'address can not be empty',
        'urgentContact.require'  => 'urgentContact can not be empty',
        'urgentPhone.require'  => 'urgentPhone can not be empty',
        'attention.require'  => 'attention can not be empty',
        'time1.require'  => '上课开始时间不能为空',
        'time2.require'  => '上课结束时间不能为空',

       ]; 
 
$newrule = [];
foreach ($data as $key => $value) {
    if(isset($rule[$key])){
        $newrule[$key] = $rule[$key];  
    } 
}
 

  $validate = new Validate($newrule, $msg); 
      // $validate->loadLang();
    // 验证
    if(!$validate->check($data)){ 
        echo json_encode(['code'=>'0','msg'=>$validate->getError()]);
        exit(); 
    } 
}






function generateusername(){
   
    
    $sql = "select MAX(username)+1 AS code from ys_audition";
    $code = Db::name('user')->query($sql);
    $code = $code[0]['code'];
    if($code==null){
      $code = '001';
    }
    if($code<10){
      $code = '00'.$code;
    }
    if($code>=10&&$code<100){
      $code = '0'.$code;
    }
    if($code>=100){
      $code = $code;
    }


    return $code;
}






// 公共调用，使用rejson(参数，参数，参数)进行调用
function rejson($code=0,$msg='',$data='',$arr=''){
        return json(['code'=>$code,'msg'=>$msg,'data'=>$data,'arr'=>$arr]); 
    }











 //配置数据表
function gettable($sub=null)
{
    $where['tbnum'] = ['=',$sub];
    $data = get_data('system_conf',$where); 
    return ['tbn'=>$data['tbname'],'id'=>$data['tbid'],'kws'=>$data['tbkeywords']];  
}

 

    function p($array){
        dump($array,1,'<pre>',0,'</pre>');
        exit;
    }
 //配置各个页面的codeid
function set_comview()
{
   $data = get_datalist('system_conf'); 
   $obj = array();
   foreach ($data as $key => &$value) {
       $code = $value['tbnum'].'*';
       $obj[$value['tbcodestr']] = jiami($code);
   } 
    return $obj; 
}



 //提供给前台的  配置各个页面的codeid
function set_comview_index()
{
  
   $data = get_datalist('system_conf'); 
   $obj = array();
   foreach ($data as $key => &$value) {
       $code = $value['tbnum'].'*'; 
        $obj[$value['tbcodestr']] = $value['adminonly']==0?jiami($code):'';
       
       
   } 
    return $obj; 
}





 // 算法类——公共函数**************************************************************************************************
 
/**
 *通过url 获取json数据
 * @param    string   $address     "json/pc/admin/menu.json" 
 * @return   arr
 * @author  wyl <181984609@qq.com>
 */
function getjson($url){ 
	// 从文件中读取数据到PHP变量
	$json_string = file_get_contents($url); 
	// 把JSON字符串转成PHP数组
	$data = json_decode($json_string, true);
	return $data; 
}



/**
 *创建盐
 * @param    string   
 * @return   string
 * @author  wyl <181984609@qq.com>
 */
function create_salt($length=-6)
{
    return 'abcdef';
    // return $salt = substr(uniqid(rand()), $length);
}

/**
 *宇杉md5加密
 * @param    string   
 * @return   string
 * @author  wyl <181984609@qq.com>
 */
function ys_md5($string,$salt)
{
    return md5(md5($string).$salt);
}

/**
 *用户信息存入session,从session拿出用户信息
 * @param    string   cookie("PHPSESSID")
 * @return   stringcookie("PHPSESSID")
 * @author  wyl <181984609@qq.com>
 */
function user_auth($data=null){  
    if($data===null){
        return session('admin_sessid'); 
    }else{
        session('admin_sessid',$data); 
        return true; 
    } 
}; 

/**
 *宇杉 加密
 * @param    string   2*56    2是表的下标，56是表的id
 * @return   string
 * @author  wyl <181984609@qq.com>
 */
 function jiami($str){
     $userdata = user_auth();
     return encrypt($str,'E',$userdata['salt']); 
    }

/**
 *宇杉 解密
 * @param    string   
 * @return   string
 * @author  wyl <181984609@qq.com>
 */
function jiemi($str){
$userdata = user_auth(); 
return encrypt($str,'D',$userdata['salt']);
    }

function setdate($num=0,$all=null){ 
    //$a = strtotime('2015-12-26');1451059200 H:i:s
    if($all==null){
      $d = $num==0?'':date("Y-m-d", $num); 
    }else{
      $d = $num==0?'':date("Y-m-d H:i", $num);
    } 
    return $d;
}

    /**
     * 通过递归获取当前节点的所有子节点
     * @param  [type] $parent     [description]
     * @param  [type] $list       [description]
     * @param  [type] $data       [description]
     * @return [type]             [description]
     */
    function getSons($parent,$list,$data){
        if(hasSon($parent,$list)){
        	 
            //数有几个孩子
            $v = 0;
            foreach ($list as $key => $value) {

                if($parent['id'] == $value['pid']){
                    $v++;
                }
            }

            $j = 0;//计数用的
            foreach ($list as $key => &$value){

                if($parent['id'] == $value['pid']){
                    $j++;
                    $a = 2;
                    if($j==$v){ $a = 3;}  
$value['namelip'] = $parent['cstlip'].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|--'.$value['namelip']; 
$value['namestr'] = $parent['cststr'].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="/php/public/admin/images/m'.$a.'.png">'.$value['namestr']; 
$value['cststr'] = $parent['cststr'].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src="/php/public/admin/images/m1.png">';
$value['cstlip'] = $parent['cstlip'].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|--';

                     
                    $data[] = $value;
                    $data = getSons($value,$list,$data);
                }
            }
        }
        return $data;
    }

    /**
     * 判断是否有次级菜单
     * @param  [type]  $parent [description]
     * @param  [type]  $list   [description]
     * @return boolean         [description]
     */
    function hasSon($parent,$list){
        foreach ($list as $key => &$value) {
            if($parent['id']==$value['pid']){
                return true;
            }
        }
        return false;
    }


/**
 *宇杉 生成codeid
 * @param    string   
 * @return   arr
 * @author  wyl <181984609@qq.com>
 */ 
function set_codeid($arr,$num){
    $id = gettable($num)['id'];
    /*生成codeid同时，删除原始ID； 0*6  */ 
    foreach ($arr as &$value) {

        $value['codeid'] = jiami($num.'*'.$value[$id]); 


        //对各个表时间戳进行转换
        if(isset($value["createtime"])){
            if($value['createtime']){
                $value['createtime'] = setdate($value['createtime'],true);
                $value['MDtime']=date('n月d日',intval($value['createtime']));
                }
        }


  // p($value);      
        isset($value["timeline"])?$value['timeline'] = setdate($value['timeline'],1):"";
        isset($value["updatetime"])?$value['updatetime'] = setdate($value['updatetime'],true):"";
        isset($value["classtime"])?$value['classtime'] = setdate($value['classtime'],true):"";
        isset($value["last_search_time"])?$value['last_search_time'] = setdate($value['last_search_time'],true):"";
        // isset($value["last_logintime"])?$value['last_logintime'] = setdate($value['last_logintime'],true):"";
        isset($value["logintime"])?$value['logintime'] = setdate($value['logintime'],true):"";
        isset($value["certifytime"])?$value['certifytime'] = setdate($value['certifytime'],true):"";
         isset($value["time"])?$value['time'] = setdate($value['time'],true):"";
        if (isset($value["livestart"])&&isset($value["liveend"])) {
            if($value["livestart"]&&$value["liveend"]){
                if($value["liveend"]-$value["livestart"]<(3600*24)){
                    $value['livetimeformat']=date('n月d日 H:i',$value["livestart"]).'-'.date('H:i',$value["liveend"]);
                }else{
                    $value['livetimeformat']=date('n月d日',$value["livestart"]).'-'.date('n月d日',$value["liveend"]);
                }
            }
        }
        if(isset($value['activitystime'])&&isset($value['activityotime'])){
            if($value["activitystime"]&&$value["activityotime"]) 
                if($value["activityotime"]-$value["activitystime"]<(3600*24)){
                    $value['activitystr']=date('n月d日 H:i',$value["activitystime"]).'-'.date('H:i',$value["activityotime"]);
                }else{
                    $value['activitystr']=date('n月d日',$value["activitystime"]).'-'.date('n月d日',$value["activityotime"]);
                }
        }

        if(isset($value['dealtime'])){$value['dealtime']=@date('Y-m-d H:i',$value['dealtime']);}
        if(isset($value['activitystime'])){$value['activitystime']=@date('Y-m-d H:i',$value['activitystime']);}
        if(isset($value['activityotime'])){$value['activityotime']=@date('Y-m-d H:i',$value['activityotime']);}
        if(isset($value['overtime'])){$value['overtime']=@date('Y-m-d H:i',$value['overtime']);}
        if(isset($value['livestart'])){$value['livestart']=@date('Y-m-d H:i',$value['livestart']);}
        if(isset($value['liveend'])){$value['liveend']=@date('Y-m-d H:i',$value['liveend']);}
        if(isset($value['approvedtime'])){$value['approvedtime']=@date('Y-m-d',$value['approvedtime']);}
       

        

        if (isset($value["lessonstart"])&&isset($value["lessonend"])) {
            if($value["lessonstart"]&&$value["lessonend"]) 
                if($value["lessonend"]-$value["lessonstart"]<(3600*24)){
                    $value['timestr']=date('n月d日 H:i',$value["lessonstart"]).'-'.date('H:i',$value["lessonend"]);
                }else{
                    $value['timestr']=date('n月d日',$value["lessonstart"]).'-'.date('n月d日',$value["lessonend"]);
                }
        }


   

        if(isset($value['lessonstart'])){$value['lessonstart']=@date('Y-m-d H:i',$value['lessonstart']);}
        if(isset($value['lessonend'])){$value['lessonend']=@date('Y-m-d H:i',$value['lessonend']);}


        if(isset($value["content"])){
            $value["content"] = htmlspecialchars_decode($value["content"]);
        }

         if(isset($value["time1"])){
            $value["time1"] =@date('Y-m-d H:i',$value['time1']);
             $value["time2"] =@date('Y-m-d H:i',$value['time2']);;
        }

        //  if (isset($value["time1"])&&isset($value["time2"])) {
        //     if($value["time1"]&&$value["time2"]) 
        //     $value['timestr']=date('n.d',$value["time1"]).'('.(week_handle(date('w',$value["time1"]))).')'.date('H:i',$value["time1"]).'-'.date('H:i',$value["time2"]);
        // }

        if(isset($value["thumb"])){
            $temp=array();


           $v =explode(',',$value["thumb"]);
            $value['thumbarr'] = [];
            foreach ($v as $key => $val) {
              $value['thumbarr'][] = ['img'=>$val];   
            }
            $value['thnum']=count($value["thumbarr"]);

        }

        if(isset($value["imgurl"])){
            $temp=array();


           $v =explode(';',$value["imgurl"]);
            $value['imgurlarr'] = [];
            foreach ($v as $key => $val) {
              $value['imgurlarr'][] = ['img'=>$val];   
            }
            $value['thnum']=count($value["imgurlarr"]);

        }

    }  
    return $arr;
}
 
/**
 *宇杉 解析codeid
 * @param    string   
 * @return   arr
 * @author  wyl <181984609@qq.com>
 */
function explain_codeid($codeid){ 
    $code = jiemi($codeid);
     if(!$code){ 
            echo json_encode(['code'=>'0','msg'=>$code]);
            exit();
      }  
    if($code=='')return false;
        $num_star_idv_arr = explode("*",$code);
        $num = $num_star_idv_arr[0]; 
        $a = gettable($num); 
        $arr =array(
        'tbn'=>$a['tbn'],
        'id'=>$a['id'],
        'kws'=>$a['kws'],
        'idv'=>intval($num_star_idv_arr[1]),
        'num'=>$num, 
        );
     // echo json_encode($arr);  
     //  exit(); 
return $arr;
} 
/**
 *宇杉 还原成真实的对象： codeid=989709ykhyku&name=guyuan ————————————>  [data=>[prid=>9,name=>guyuan],tbn=program]  
 * @param    string   
 * @return   arr
 * @author  wyl <181984609@qq.com>
 */
function real_data($post,$obj){
$tbn = $obj['tbn'];
$id = $obj['id'];
$idv = $obj['idv'];

unset($post['codeid']);  
unset($obj['tbn']);
unset($obj['id']);
unset($obj['idv']);
unset($obj['num']); 
$obj[$id] = $idv;
$newarr = array_merge($post,$obj); 
return array('tbn'=>$tbn,'data'=>$newarr);

}


/**
 *宇杉 还原成真实的对象： codeid=989709ykhyku&name=guyuan ————————————>  [data=>[prid=>9,name=>guyuan],tbn=program]  
 * @param    string   
 * @return   arr
 * @author  wyl <181984609@qq.com>
 */
function real_data_noid($post,$obj){
$tbn = $obj['tbn']; 

unset($post['codeid']); 
unset($post['tablecodeid']);
unset($obj['tbn']);
unset($obj['id']);
unset($obj['idv']);
unset($obj['num']); 
unset($obj['kws']); 
$newarr = array_merge($post,$obj); 
return array('tbn'=>$tbn,'data'=>$newarr);

}









/*********************************************************************
函数名称:encrypt
函数作用:加密解密字符串
使用方法:
加密 :encrypt('str','E','qingdou');
解密 :encrypt('被加密过的字符串','D','qingdou');
参数说明:
$string   :需要加密解密的字符串
$operation:判断是加密还是解密:E:加密   D:解密
$key  :加密的钥匙(密匙);
*********************************************************************/
function encrypt($string,$operation,$key='')
{
$src  = array("/","+","=");
$dist = array("_a","_b","_c");
if($operation=='D'){$string  = str_replace($dist,$src,$string);}
$key=md5($key);
$key_length=strlen($key);
$string=$operation=='D'?base64_decode($string):substr(md5($string.$key),0,8).$string;
$string_length=strlen($string);
$rndkey=$box=array();
$result='';
for($i=0;$i<=255;$i++)
{
$rndkey[$i]=ord($key[$i%$key_length]);
$box[$i]=$i;
}
for($j=$i=0;$i<256;$i++)
{
$j=($j+$box[$i]+$rndkey[$i])%256;
$tmp=$box[$i];
$box[$i]=$box[$j];
$box[$j]=$tmp;
}
for($a=$j=$i=0;$i<$string_length;$i++)
{
$a=($a+1)%256;
$j=($j+$box[$a])%256;
$tmp=$box[$a];
$box[$a]=$box[$j];
$box[$j]=$tmp;
$result.=chr(ord($string[$i])^($box[($box[$a]+$box[$j])%256]));
}
if($operation=='D')
{
if(substr($result,0,8)==substr(md5(substr($result,8).$key),0,8))
{
return substr($result,8);
}
else
{
return'';
}
}
else
{
$rdate  = str_replace('=','',base64_encode($result));
$rdate  = str_replace($src,$dist,$rdate);
return $rdate;
}
}



/**
 *发送邮件
 * @param    string   $address       地址
 * @param    string    $title 标题
 * @param    string    $message 邮件内容
 * @param    string $attachment 附件列表
 * @return   boolean
 */
function send_mail($address, $title, $message, $attachment = null)
{
    Vendor('PHPMailer.class#phpmailer');

    $mail = new PHPMailer;
    //$mail->Priority = 3;
    // 设置PHPMailer使用SMTP服务器发送Email
    $mail->IsSMTP();
    // 设置邮件的字符编码，若不指定，则为'UTF-8'
    $mail->CharSet   = 'UTF-8';
    $mail->SMTPDebug = 0; // 关闭SMTP调试功能
    $mail->SMTPAuth  = true; // 启用 SMTP 验证功能
    // $mail->SMTPSecure = 'ssl';  // 使用安全协议
    $mail->IsHTML(true); //body is html

    // 设置SMTP服务器。
    $mail->Host = C('CFG_EMAIL_HOST');
    $mail->Port = C('CFG_EMAIL_PORT') ? C('CFG_EMAIL_PORT') : 25; // SMTP服务器的端口号

    // 设置用户名和密码。
    $mail->Username = C('CFG_EMAIL_LOGINNAME');
    $mail->Password = C('CFG_EMAIL_PASSWORD');

    // 设置邮件头的From字段
    $mail->From = C('CFG_EMAIL_FROM');
    // 设置发件人名字
    $mail->FromName = C('CFG_EMAIL_FROM_NAME');

    // 设置邮件标题
    $mail->Subject = $title;
    // 添加收件人地址，可以多次使用来添加多个收件人
    $mail->AddAddress($address);
    // 设置邮件正文
    $mail->Body = $message;
    // 添加附件
    if (is_array($attachment)) {
        foreach ($attachment as $file) {
            is_file($file) && $mail->AddAttachment($file);
        }
    }

    // 发送邮件。
    //return($mail->Send());
    return $mail->Send() ? true : $mail->ErrorInfo;
}

 
/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 */
function format_bytes($size, $delimiter = '') {
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}











 //数据库类——公共函数*********************************************************************************************************
// 数据库增删改查一些方法


// 1、单表查询列表 返回数组[]；
    function get_datalist($tbn=null,$where=[],$field=null,$num=null,$sort=null){
        $data = Db::name($tbn);
        if($field){$data = $data->field($field);  }
        $where['isdel']=['=','0'];
        if($tbn=='module_news'||$tbn=='comment'){
            $where['approved']=['=','2'];
        }
        $data = $data->where($where); 
        if($num){$data = $data->limit($num);}        
        if($sort){$data = $data->order($sort);}
        $re = $data->select();   
       
        
        return $re;
    }
// 2、单表查询列表分页 返回对象  {datalist:[],total:35}；
   
    function get_datalist_page($tbn=null,$where=[],$curPage=null,$listnum=null,$order=null){
    
    $where['isdel']=['=','0']; 
    $total = Db::name($tbn)->where($where)->count();

  
    $pages = ceil($total/$listnum); 
    $data=Db::name($tbn)->where($where)->page($curPage)->limit($listnum)->order($order)->select();
    $arr = array('pages'=>$pages,'datalist'=>$data,'total'=>$total);
    return $arr;
}   
// 3、单表查询1条记录 返回对象   {}；
    function get_data($tbn,$where=null,$field=null,$sort=null){
         $data = Db::name($tbn);
          if($field){$data = $data->field($field);}

           $where['isdel']=['=','0'];
         // if($tbn=='module_news'||$tbn=='comment'){
         //        $where['approved']=['=','2'];
         //    }
           $data = $data->where($where); 
          if($sort){$data = $data->order($sort);}
          
          $re = $data->find(); 
          return $re; 
    }



 


// 7、增加 
function insert_data($tbn,$data){
      $re = Db::name($tbn)->insertGetId($data);
      return $re;
    };
// 删除采用物理删除 isdel字段， 0默认没删除，1已删除；
// 8、删除一条或者多条记录
    function delete_data($tbn,$id,$idarr){
        $re = Db::name($tbn)->where($id,'in',$idarr)->setField('isdel',1);
        return $re;
    };


// 9、更新一条记录
  function update_one($tbn,$where,$content){
    $data = Db::name($tbn)->where($where)->update($content); 
    return $data;
    };

// 10、更新多条记录

        function update_more($tbn,$id,$value){

        return true;
    };

 
//数这个表有多少条记录
        function countnum($tbn,$where=[]){
             $data = Db::name($tbn); 
           $where['isdel']=['=','0']; 
           $data = $data->where($where);  
          $re = $data->count();  
        return $re;
    };



//生成N张推荐码；





    /**
 * 写基础配置文件
 * @param $data
 */
function writeConfig($data)
{
    $path = APP_ROOT . '/config/group.conf';
    @file_put_contents($path, serialize($data));
    return true;
}

//读配置文件
function readConfig()
{
    $path = APP_ROOT . '/config/group.conf';
    $conf = file_get_contents($path);
    if(empty($conf))
        return [];

    return unserialize($conf);
}

//写聊天配置
function writeCtConfig($data)
{
    $path = APP_ROOT . '/config/chat.conf';
    @file_put_contents($path, serialize($data));
    return true;
}

//读聊天配置文件
function readCtConfig()
{
    $path = APP_ROOT . '/config/chat.conf';
    $conf = file_get_contents($path);
    if(empty($conf))
        return [];

    return unserialize($conf);
}

//获取评论
function getComment($blogId)
{
    $list = db('comment')->where('blog_id', $blogId)->select();
    if(empty($list)){

        echo "";
    }else{

        $html = '';
        foreach($list as $key=>$vo){
            $html .= '<a href="javascript:;" class="pull-left"><img alt="image" src="' . $vo['com_avatar'] . '"></a>';
            $html .= '<div class="media-body"><a href="javascript:;" style="color:#337AB7">' . $vo['com_user'];
            $html .= '&nbsp;&nbsp;&nbsp;&nbsp;</a>' . $vo['content'] . '<br/>';
            $html .= '<small class="text-muted">' . date('Y-m-d H:i', $vo['com_time']) . '</small></div>';
        }

        echo $html;
    }

}

//将对象转换成数组
function objToArr($obj)
{
    return json_decode(json_encode($obj), true)['data'];
}

//将内容进行UNICODE编码，编码后的内容格式：\u56fe\u7247 （原始：图片）  
function unicode_encode($name)  
{  
    $name = iconv('UTF-8', 'UCS-2', $name);  
    $len = strlen($name);  
    $str = '';  
    for ($i = 0; $i < $len - 1; $i = $i + 2)  
    {  
        $c = $name[$i];  
        $c2 = $name[$i + 1];  
        if (ord($c) > 0)  
        {    // 两个字节的文字  
            $str .= 'u'.base_convert(ord($c), 10, 16).base_convert(ord($c2), 10, 16);  
        }  
        else  
        {  
            $str .= $c2;  
        }  
    }  
    return $str;  
} 

/**
 * 判断一个表是否已经存在
 * @param string $table_name 要判断的表名
 * @return boolean
 */
 
 
 
 /**
 * 获取表名
 * @param int    $moduleid  模型id
 * @param string $tablem    返回表名
 */
function gettbname($moduleid){
    $module=M('SystemModule');
    //获取数据表名称
    $table=$module->where('id='.$moduleid)->field('table_name')->find();
    $tablename=str_replace(C('DB_PREFIX'), '', $table['table_name']);
    $tablename=explode('_',$tablename);
    foreach($tablename as $k=>$v){
        $tablem.=ucfirst($v);
    }
    return $tablem;
}
function tableexist($table_name){ 
    $tableisexit=Db::query('SHOW TABLES LIKE \''.$table_name.'\'');
    if(count($tableisexit)){
        return true;
    }else{
        return false;
    }
}
/**
 * 生成创建表的sql语句
 * @param string $table_name  要创建的表名
 * @param array $default_value  默认有的字段,二维数组，每个子数组中需包含value_name字段名，value_title注释
 * @param string $table_description 表注释
 */
function createAddTableSql($table_name,$default_value,$table_description='无'){
    $varchar=array('input','radio','checkbox','select','image','date','datetime','time','file');
    $text=array('textarea','cleareditor','editor','imagelist','fileslist');
    $sql='CREATE TABLE IF NOT EXISTS `'.$table_name.'` (  
            `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
            `listorder` int(11) default 9999,
            `cloneid` int(11) default NULL,
            `url` varchar(200) default NULL,
            `createtime` char(20),
            `updatetime` char(20),
            `cateid` int(11) NOT NULL COMMENT \'所属栏目\',';
    foreach($default_value as $k=>$v){
        if(in_array($v['attr'],$varchar)){
            $sql.='`'.$v['value_name'].'` varchar(100) DEFAULT NULL COMMENT \''.$v['value_title'].'\',';
        }else if(in_array($v['attr'], $text)){
            $sql.='`'.$v['value_name'].'` text COMMENT \''.$v['value_title'].'\',';
        }
    }
    $sql.='  PRIMARY KEY (`id`)
            ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8  COMMENT=\''.$table_description.'\';';
    return $sql;
}
/**
 * 生成表字段的sql语句
 * @param string $table_name  要操作的表名
 * @param string $field_name  添加的字段名
 * @param string $table_type  字段类型
 * @param string $notnull   非空 
 * @param string $isunique  唯一值（）因现字段类型只有text 和varchar(100) 停用 
 * @param string $default   默认 
 * @param string $sql   返回sql语句 
 */
                            //表名，字段名，字段类型，非空，唯一，默认
function createAddFieldSql($table_name,$field_name,$table_type,$notnull,$isunique,$default){
    $varchar=array('input','radio','checkbox','select','image','date','datetime','time','file');
    $text=array('textarea','cleareditor','editor','imagelist','fileslist');
    //判断字段类型
    if(in_array($table_type,$varchar)){
        $table_type=' varchar(100) ';
    }else{
        $table_type=' text ';
        $default='';
    }
    //非空
    $notnull=($notnull==1)?' not null ':' ';
    //唯一
    $isunique=($isunique==1)?' unique ':' ';
    $sql='alter table '.$table_name.' add '.$field_name
    //类型
    .$table_type
    //非空
    .$notnull
    //唯一
    .$isunique
    //默认值
    .' default'.$default;
/*  .' default"'.$default.'"'; */
    $result['sql']=$sql;
    return $sql;
} 


/**
 * 获取 获取数组的id数组
 * @param 
 * @return   arr
 * @author  wyl
 */
function getids($data,$id){
    $narr = [];
    foreach ($data as $key => $value) {
         $narr[] = $value[$id];
    }
    return $narr;
}

/**
 * 获取 两个数组通过某个id进行拼接
 * @param 
 * @return   arr
 * @author  wyl
 */
function jointwoarr($arr1,$arr2,$id1,$id2){ 
    foreach ($arr1 as $key => &$value) {
         foreach ($arr2 as $k => $val) {
            if($value[$id1]==$val[$id2]){
                unset($val[$id2]);
                $value = array_merge($value, $val);
            }
         }
    }
    return $arr1;
}
 



/**
 * 设备 浏览器
 * @param 
 * @return   arr
 * @author  xubo
 */
function getAgentInfo(){  
    $agent = $_SERVER['HTTP_USER_AGENT'];  
    $brower = array(  
        'MSIE' => 1,  
        'Firefox' => 2,  
        'QQBrowser' => 3,  
        'QQ/' => 3,  
        'UCBrowser' => 4,  
        'MicroMessenger' => 9,  
        'Edge' => 5,  
        'Chrome' => 6,  
        'Opera' => 7,  
        'OPR' => 7,  
        'Safari' => 8,  
        'Trident/' => 1  
    );  
    $system = array(  
        'Windows Phone' => 4,  
        'Windows' => 1,  
        'Android' => 2,  
        'iPhone' => 3,  
        'iPad' => 5  
    );  
    $browser_num = 0;//未知  
    $system_num = 0;//未知  
    foreach($brower as $bro => $val){  
        if(stripos($agent, $bro) !== false){  
            $browser_num = $bro;  
            break;  
        }  
    }  
    foreach($system as $sys => $val){  
        if(stripos($agent, $sys) !== false){  
            $system_num = $sys;  
            break;  
        }  
    }  
    return array('sys' => $system_num, 'bro' => $browser_num);  
} 







/***********************微信*************************
/**
 * 判断重定向
 * @param    string   
 * @return   arr
 * @author  lxc
 */
function wechatRedirect($url){
    $url = str_replace("","%2f",$url);
    // if (Session::get('jsoninfo')) {
        // $scope = 'snsapi_base';
    // }else{
        $scope = 'snsapi_userinfo';
    // }
    $reurl = 'Location:https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxf01bee5587ed09db&redirect_uri=http%3a%2f%2fwww.chenbaozhong.com%2fwap%2findex.php%2f'.$url.'&response_type=code&scope='.$scope.'&state=1#wechat_redirect';
    return $reurl;
} 

/**
 * 获取微信用户信息
 * @param    string   
 * @return   arr
 * @author  lxc
 */
function getWxinfo($GET){
    if (!Session::get('jsoninfo')) {
        if (array_key_exists('code',$GET)||Session::get('wxopenid')) {
            if (Session::get('wxopenid')) {
                $openid = Session::get('wxopenid');
            }else{
                $code = $GET['code'];
                $oauth2url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".Appid2."&secret=".AppSecret2."&code={$code}&grant_type=authorization_code";
                $jsoninfo = http_curl($oauth2url,null);
                $access_token = $jsoninfo['access_token'];
                $openid = $jsoninfo['openid'];
            }
            $url = "https://api.weixin.qq.com/sns/userinfo?access_token={$access_token}&openid={$openid}&lang=zh_CN";      
            $jsoninfo =http_curl($url,null);        
            Session::set('jsoninfo',$jsoninfo); 
            Session::set('wxopenid',$openid);              
        }
        else{
            header(wechatRedirect('index'));   
            exit(); 
        }                
    }else{
        $jsoninfo = Session::get('jsoninfo');
    }
    return $jsoninfo;
} 



/**
 * 获取全局的access_token方法
 * @return [type] [description]
 */
function getAccessToken(){
    $field = 'access_token,modify_time';
    $condition = array('token'=>TOKEN2,'appid'=>Appid2,'appsecret'=>AppSecret2);
    // $data = M('wechat')->field($field)->where($condition)->find();
    // if($data['access_token'] && time()-$data['modify_time']<7000){
    //  $access_token = $data['access_token'];
    // }else{
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.Appid2.'&secret='.AppSecret2.'';
        $jsoninfo = http_curl($url,null);
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
function http_curl($url,$data){
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
/**
 * 发送消息
 * @return [type] [description]
 */
function responseMsg(){
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
//***********************微信*************************
 








//判断活动报名状态
function enroll_time_hanlde($Eend,$Astart,$Aend){//$Eend是报名截止时间，$Astart是活动开始时间，$Aend是活动结束时间
    
    if (time()-$Eend<0) {
        return $result='0';//0代表活动尚未开始，状态为预报名
    }else if($Astart<time()&&time()<$Aend){
        return $result='1';//1代表活动正在进行，状态为进行中
    }else if(time()>$Aend){
        return $result='2';//2代表活动已经结束，状态为已完成
    }
}


//免费学堂--预告与往期
function free_lesson_time($time){
    if ($time>time()) {
        return '1';
    }else{
        return '0';
    }
}




// //时间处理
function time_handle($time){
    $t=(time()-$time)/60/60/24;
    if ($t<1) {//时间差小于一天
        (String)$t=floor($t*24);
        if($t<1){
            $result='刚刚';
        }else{
        $result=$t.'小时前';
        }
    }elseif ($t<3) {//时间差小于三天
        (String)$t=floor($t);
        $result=$t.'天前';
    }else{//时间差大于三天
        $result=date('Y m d',$time);
    }
    return $result;
}


//数量处理
function num_handle($num){
    if($num>10000){
        $num=round($num/10000,1);
       return $result=$num.'万';
    }else{
       return $result=(String)$num;
    }
}


//点赞状态判断
function zanType($data,$uid){

    if (isset($data['zanUser'])) {
    
    $data['zanType']='0';
        if($data['zanUser']){
                $re=explode(',',$data['zanUser']);
                //遍历数组中是否已经有uid,如果存在即已点赞
                foreach ($re as $key => $value) {
                    if ($value==$uid) {
                        $data['zanType']='1';
                    }
                }
        }
    }
    

    return $data;
}


//收藏状态判断
function collegeType($data,$uid){

    $data['collegeType']='0';
    $tbn='college';
    $where['articleid']=['=',$data['id']];
    $newdata=get_data($tbn,$where);

    if (isset($newdata['collegeUser'])) {
        
    
        if($newdata['collegeUser']){
                $re=explode(',',$newdata['collegeUser']);
                //遍历数组中是否已经有uid,如果存在即已点赞
                foreach ($re as $key => $value) {
                    if ($value==$uid) {
                        $data['collegeType']='1';
                    }
                }
        }
    }

    return $data;
}




function datas_handle($data,$uid,$tbn){//对多条纪录处理
       // p($data);
        foreach ($data as $key => &$value) { 

        $value=zanType($value,$uid);

        if(isset($value['commentnum'])) {
            $value['commentnum']=DB::table('ys_comment')->where('articleid',$value['id'])->where('isdel',0)->count();
            $where['id'] = ['=',$value['id']];
            update_one('module_news',$where,$value);
        }

        if ($tbn=='module_news') {
            $value=collegeType($value,$uid);
            // $value['thumb']=explode(',',$value['thumb']);
            // $value['imgnum']=count($value['thumb']);
        }

        if ($tbn=='comment') {
            $temp=array();
            $arr=explode(',',$value['comimg']);
            if($arr[0]!=null&&$arr){
            foreach ($arr as $k => $v) {
                $temp[]=$v;
            }
                $value['comimg']=$temp;
                $value['comimgnum']=count($temp);
            }else{
                $value['comimg']=$temp;
                $value['comimgnum']=count($temp);
            }

        }

        //对活动的时间格式处理
        if(isset($value['createtime'])) {$value['timeformat']=time_handle($value['createtime']);}

        //对评论数量处理
        if(isset($value['comnum'])) {
             $value['comnum']=num_handle($value['comnum']);  
         }



        //对免费学堂 预期或往期处理
        if(isset($value['createtime'])) {$value['time']=free_lesson_time($value['createtime']);}
    }
    return $data;
}

function data_handle($data,$uid,$tbn){//对一条记录处理
    //对时间格式处理
     $data=zanType($data,$uid);

    if ($tbn=='module_news') {
    $data=collegeType($data,$uid);
    // $value['thumb']=explode(',',$value['thumb']);
    // $value['imgnum']=count($value['thumb']);
    }

    if(isset($data['commentnum'])) {
        $data['commentnum']=DB::table('ys_comment')->where('isdel',0)->where('articleid',$data['id'])->count();
        $where['id'] = ['=',$data['id']];
        update_one('module_news',$where,$data);
    }

    return $data;
}

function fenye($temp,$listnum){
         $c=0;
         $result=[];
          $num=count($temp);
        for ($j=1; $j <=$num; $j++) { 
          $result[$c][]=$temp[$j-1];
          if($j!=0&&$j%$listnum==0&&$listnum!=1){$c++;}
          if ($listnum==1) {
            $c++;
          }
      }
      return $result;
}


function order_time_handle($time){
    if (time()-$time<(3600*24*365)) {
        return date('n月d日',$time);
    }else{
        return date('Y年n月d日',$time);
    }
}

function week_handle($time){
    switch ($time) {
        case '0':
            return '星期日';
            break;
        case '1':
            return '星期一';
            break;
        case '2':
            return '星期二';
            break;
        case '3':
            return '星期三';
            break;
        case '4':
            return '星期四';
            break;
        case '5':
            return '星期五';
            break;
        case '6':
            return '星期六';
            break;
        
        default:
            return '';
            break;
    }
}

function viewnum_handle($data){
     if(isset($data["viewnum"])){
          $data['viewnum'] = ++$data['viewnum'];
          $where['id']=['=',$data['id']];
         $temp['viewnum']=$data['viewnum'];
         $re=update_one('module_news',$where,$temp);
      }
      return $data;
}

function live_handle($where,$time){
   switch ($time) {
             case '0':
               $where['livestart']=['<',time()];
               break;
             case '1':
               $where['livestart']=['>',time()];
               break;           
             default:
               $where=$where;
               break;
           }
 
    return $where;

    // $next=array();
    // $pre=array();
    // foreach ($data as $key => $value) {
    //     if(isset($value['livestart'])){
    //         if($value['livestart']-time()>0){
    //             $next[]=$value;
    //         }else{
    //             $pre[]=$value;
    //         }
    //     }
    // }
    // if($temp===0){
    //     return $pre;
    // }else{
    //     return $next;
    // }
}

// function activity_handle($data,$status){
//     $temp=array();
//     foreach ($data as $key => $value) {
   
//     if(isset($value['overtime'])&&isset($value['activitystime'])&&isset($value['activityotime'])){
//     $reastatus=enroll_time_hanlde($value['overtime'],$value['activitystime'],$value['activityotime']);
//              if($reastatus==$status){
//                 //unset($data[$key]);
//                 $temp[]=$value;
//              }
//       }      
//     }
//     return $temp;
// }

function where_handle($where,$status){
   switch ($status) {
             case '0':
               $where['overtime']=['>',time()];
               break;
             case '1':
               $where['overtime']=['<',time()];
               $where['activityotime']=['>',time()];
               break;
             case '2':
               $where['activityotime']=['<',time()];
               break;           
             default:
               $where=$where;
               break;
           }
 
    return $where;
}


/**
 * 新增通知
 * @return [type] [description]
 */
 
function newnotice($notice,$userid='',$noticetype=1){
  $noticeinsert = array('user_id'=>$userid,'noticetype'=>$noticetype,'createtime'=>time(),'content'=>$notice,'type'=>4);
  $re = Db::name('notice')->insert($noticeinsert);
} 


/**
 * 简单对称加密算法之加密
 * @param String $string 需要加密的字串
 * @param String $skey 加密EKY
 * @author Anyon Zou <zoujingli@qq.com>
 * @date 2013-08-13 19:30
 * @update 2014-10-10 10:10
 * @return String
 */
function encode($string = '', $skey = 'cxphp') {
    $strArr = str_split(base64_encode($string));
    $strCount = count($strArr);
    foreach (str_split($skey) as $key => $value)
        $key < $strCount && $strArr[$key].=$value;
    return str_replace(array('=', '+', '/'), array('O0O0O', 'o000o', 'oo00o'), join('', $strArr));
}


/**
 * 简单对称加密算法之解密
 * @param String $string 需要解密的字串
 * @param String $skey 解密KEY
 * @author Anyon Zou <zoujingli@qq.com>
 * @date 2013-08-13 19:30
 * @update 2014-10-10 10:10
 * @return String
 */
function decode($string = '', $skey = 'cxphp') {
    $strArr = str_split(str_replace(array('O0O0O', 'o000o', 'oo00o'), array('=', '+', '/'), $string), 2);
    $strCount = count($strArr);
    foreach (str_split($skey) as $key => $value)
        $key <= $strCount  && isset($strArr[$key]) && $strArr[$key][1] === $value && $strArr[$key] = $strArr[$key][0];
    return base64_decode(join('', $strArr));
}



function admin_timeformat($data){
    foreach ($data as &$value) {

   
        if(isset($value['liveend'])){$value['liveend']=date('Y m d',$value['liveend']);}
        if(isset($value['approvedtime'])){$value['approvedtime']=date('Y m d',$value['approvedtime']);}

        if(isset($value['activitystime'])&&isset($value['activityotime'])){
            $value['activitystr']=date('Y年n月d日 H:i',$value['activitystime']).'-'.date('H:i',$value['activityotime']);
        }

        if(isset($value['livestart'])&&isset($value['liveend'])){
            $value['livestr']=date('Y年n月d日 H:i',$value['livestart']).'-'.date('H:i',$value['liveend']);
        }

    }
    return $data;
}



function admin_edittime($data){
    if(isset($data['overtime'])){
        if($data['overtime'])
            $data['overtime']=strtotime($data['overtime']);
    }

    if(isset($data['activitystime'])){
        if($data['activitystime']){
            $data['activitystime']=strtotime($data['activitystime']);
        }
    }

    if(isset($data['activityotime'])){
        if($data['activityotime'])
            $data['activityotime']=strtotime($data['activityotime']);
    }

    if(isset($data['livestart'])){
        if($data['livestart'])
            $data['livestart']=strtotime($data['livestart']);
    }

    if(isset($data['liveend'])){
        if($data['liveend'])
            $data['liveend']=strtotime($data['liveend']);
    }

    if(isset($data['approvedtime'])){
        if($data['approvedtime'])
            $data['approvedtime']=strtotime($data['approvedtime']);
    }

    if(isset($data['dealtime'])){
        if($data['dealtime'])
            $data['dealtime']=strtotime($data['dealtime']);
    }

    return $data;
}


function to_one_time($time1,$time2){
    if($time1>$time2){
        $temp=$time1;
        $time1=$time2;
        $time2=$temp;
    }

    $time1=strtotime($time1);
    $time2=strtotime($time2);
    $time=date('Y年n月d日 H:i',$time1)."-".date('H:i',$time2);
    return $time;
}

 



function is_takein($data,$id){

  if($data){
        $arr=explode(',',$data);
        foreach ($arr as $key => $value) {
          if($value==$id){
            return true;
        }
    }
  }
    return false;
}


function get_child($id){
      //查找是有否子栏目
  $child=array();
  $ww['pid']=['=',$id];
  $dd=get_datalist('category',$ww);
  if($dd){
    foreach ($dd as $key => $value) {
      if(isset($value['pid'])){
      if($value['pid']){
            $child[]=$value['id'];
          }
      }
    }
  }
  return $child;
}



//获取验证码 判断这个手机号码是否被注册
function getphcode($tel,$content){
    $rearr = array('code'=>1,'msg'=>'','data'=>array());
    if(!$tel){
        $rearr['code']=0;
        $rearr['msg']='号码为空，发送失败';
    }
    if($content=='verificate'){
        $sendcode=cutcode(time(),4);
        cache('phcode',''.$tel.'+'.$sendcode.'',18000);
        $content = '尊敬的用户，您的验证码是：'.$sendcode.'。在30分钟内有效，红纽扣工作人员不会向您索取，请勿泄露。';
    }
    $message = 'http://www.smswst.com/api/httpapi.aspx?action=send&account=13877620240&password=55555&mobile='.$tel.'&content='.$content.'&sendTime=&AddSign=Y';
    // $rearr['msg']=$message;
    // return $rearr;
    $re = file_get_contents($message);
    $obj = simplexml_load_string($re);
    $rearr['code']='1';
    $rearr['msg']='发送短信成功！';
   
    $rearr['data']=$obj;
    return $rearr;
}

function cutcode($str,$num){
    $ltrl = strlen($str);
     $start = $ltrl - $num; 
     $encoding = 'utf-8'; 
    $lstr = mb_substr($str,$start,$num,$encoding);
    return $lstr;
    }

    



































//这段要进行封装，有3个地方要注意：
//1数据库加入access_token；
//2、封装到1个插件里面；
//3、调用方法怎么写


/////////////微信自定义分享模块////////////////////////
//获取token
function get_token() {
    $info=config('Wx');
    $token=$info['token'];
    session ( 'token', $token );
    return $token;
}
// 获取access_token，自动带缓存功能
function get_access_token($token = '') {
    empty ($token) && $token = get_token();
    $model = Db::name("access_token");
    $map['token'] = $token;
    $info = $model->where($map)->find();
    if(!$info)
    {
        $newaccess_token = getNowAccesstoken($token);
    }
    else
    {
        $nowtime = time();//现在时间
        $time = $nowtime - $info['lasttime'];
        $newaccess_token = $info['access_token'];
        if($time >= 1800){
            $newaccess_token = getNowAccesstoken($token);
            if($newaccess_token == 0){//重新再 调用一次
                $newaccess_token = getNowAccesstoken($token);
            }
        }
    }

    return $newaccess_token;
}
function getNowAccesstoken($token = ''){
    $nowtime = time();//现在时间
    empty ( $token ) && $token = get_token ();
    $info = get_token_appinfo ($token);
    if (empty ($info ['appid'] ) || empty ($info['secret'])) {
        return 0;
    }
    $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $info ['appid'] . '&secret=' . $info ['secret'];
  

    $ch1 = curl_init ();
    $timeout = 5;
    curl_setopt ( $ch1, CURLOPT_URL, $url );
    curl_setopt ( $ch1, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt ( $ch1, CURLOPT_CONNECTTIMEOUT, $timeout );
    curl_setopt ( $ch1, CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt ( $ch1, CURLOPT_SSL_VERIFYHOST, false );
    $accesstxt = curl_exec ( $ch1 );
    curl_close ( $ch1 );
    $tempArr = json_decode ($accesstxt, true);
    // p($tempArr);
    if (!isset($tempArr['errmsg'])) {
        $model = Db::name("access_token");
        $map['token'] = $token;
        //保存新access_token到数据库，更新最后时间
        $data = array(
            'access_token'=>$tempArr ['access_token'],
            'lasttime'=>$nowtime
        );
        $info=$model->where($map)->find();
        if($info)
        {
            $model->where($map)->update($data);
        }
        else
        {
            $data['token'] = $token;
            $model->where($map)->insert($data);
        }
        return $tempArr ['access_token'];
    }else{
        return 0;
    }
}
// 获取jsapi_ticket，判断是不过期
function getJsapiTicket($token = '') {
    empty ($token) && $token = get_token();
    $model = Db::name("jsapi_ticket");
    $map['token'] = $token;
    $info = $model->where($map)->find();
    if(!$info)
    {
        $new_jsapi_ticket = getNowJsapiTicket($token);
    }
    else
    {
        $nowtime = time();//现在时间
        $time = $nowtime - $info['lasttime'];
        $new_jsapi_ticket = $info['ticket'];
        if($time>=1800){
            $new_jsapi_ticket = getNowJsapiTicket($token);
            if($new_jsapi_ticket == 0){//重新再 调用一次
                $new_jsapi_ticket = getNowJsapiTicket($token);
            }
        }
    }

    return $new_jsapi_ticket;
}
//获取jsapi_ticket
function getNowJsapiTicket($token='')
{
    empty ($token) && $token = get_token();
    $access_token=get_access_token();
    $url='https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=' .$access_token. '&type=jsapi';
    $ch1 = curl_init ();
    $timeout = 5;
    curl_setopt ( $ch1, CURLOPT_URL, $url );
    curl_setopt ( $ch1, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt ( $ch1, CURLOPT_CONNECTTIMEOUT, $timeout );
    curl_setopt ( $ch1, CURLOPT_SSL_VERIFYPEER, FALSE );
    curl_setopt ( $ch1, CURLOPT_SSL_VERIFYHOST, false );
    $accesstxt = curl_exec ( $ch1 );
    curl_close ( $ch1 );
    $tempArr = json_decode ($accesstxt, true);
    $ext=$tempArr['errmsg'];
    if ($ext=='ok') {
        $model = Db::name("jsapi_ticket");
        $map['token'] = $token;
        $nowtime=time();
        //保存新jsapi_ticket到数据库，更新最后时间
        $data = array(
            'ticket'=>$tempArr ['ticket'],
            'lasttime'=>$nowtime
        );
        $info=$model->where($map)->find();
        if($info)
        {
            $model->where($map)->update($data);
        }
        else
        {
            $data['token'] = $token;
            $model->where($map)->insert($data);
        }
        return $tempArr['ticket'];
    }
    else
    {
        return 0;
    }
}
// 获取公众号的信息
function get_token_appinfo() {
    $info=config('Wx');
    return $info;
}
//获取signature的值 获取签名值数组
function get_signature()
{
    $url='http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
   // p($_SERVER);
    $ticket=getJsapiTicket();
    $noncestr=createNonceStr();
    $timestamp=time();
    $string='jsapi_ticket='.$ticket.'&noncestr='.$noncestr.'&timestamp='.$timestamp.'&url='.$url;
    $signature = sha1($string);
    $signPackage = array(
        "appId"     =>config('Wx.appid'),
        "nonceStr"  =>$noncestr,
        "timestamp" => $timestamp,
        "url"       => $url,
        "signature" => $signature,
        "string" => $string,
        "jsapi_ticket" => $ticket,
    );
    return  $signPackage;
}
//随机生成字符串
 function createNonceStr($length = 16) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
        $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
}
/////////////微信自定义分享模块////////////////////////