<?php
namespace app\api\controller;
use think\Controller;
use think\Request;
use think\View;
use think\Session;
use think\Db;
class Base  extends Controller
{

 /**
     * 输出验证码
     */
    public function test(){
        p(set_comview_index());
        return rejson(1,'success');  
    }


 /**
     * 输出验证码
     */
    public function verify(){
        $Verify = new \org\Captcha(config('captcha'));
        // $_GET['imageW']?$Verify->imageW=$_GET['imageW']:'';
        // $_GET['imageH']?$Verify->imageH=$_GET['imageH']:'';
        // $_GET['fontSize']?$Verify->fontSize=$_GET['fontSize']:'';
        $Verify->entry(input('id'));
    }
    /**
     * 验证码检测函数
     * @param string $code 要检测的验证码
     * @param string $id  如果一个页面中含有多个验证码，需要区分id
     * @return boolean
     */
    public function check_verify($code, $id = ''){
        $Verify = new \org\Captcha(config('captcha'));
        return $Verify->check($code, $id);
    }
    /**
     * 检测后台用户密码
     * @param int $uid 用户id
     * @param string $pass 要检测的密码
     */
    public function checkPass($uid,$pass){
        $data=Db::name('admin_user')->where('id='.$uid)->field('id,password,encrypt')->find();
        if(md5(md5($pass).$data['encrypt'])==$data['password']){
            return true;
        }else{
            return false;
        }
    }
    /**
     * 登录页面
     */
    public function login(){ 
        if(Request::instance()->isAjax() && Request::instance()->isPost()){
            if($this->check_verify(input('captcha'))){
                if(!!$data=Db::name('admin_user')->where('username=\''.input('username').'\'')->find()){
                    $data['time']=time();
                    if($this->checkPass($data['id'], input('password'))){
                        session('adminuser',$data);
                        Db::name('admin_user')->where('id='.$data['id'])->setInc('login_count');
                        $SaveData=array('last_login_time'=>time(),'last_login_ip'=>Request::instance()->ip());
                        Db::name('admin_user')->where('id='.$data['id'])->update($SaveData);
                        $result['code']=1;
                        $result['msg']='成功';
                    }else{
                        $result['code']=0;
                        $result['msg']='登录密码错误';
                    }
                }else{
                    $result['code']=0;
                    $result['msg']='用户不存在';
                }
            }else{
                $result['code']=0;
                $result['msg']='验证码填写错误';
            }
            return json($result);
        }else{
            $this->display();
        }
    }
    
    //登出
    public function logout(){
        session('adminuser',null);
        return json(['code'=>1,'msg'=>'退出成功！']); 
    }





    public function upload(){  
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
 
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
      exit; // finish preflight CORS requests here
    }
    if ( !empty($_REQUEST[ 'debug' ]) ) {
      $random = rand(0, intval($_REQUEST[ 'debug' ]) );
      if ( $random === 0 ) {
        header("HTTP/1.0 500 Internal Server Error");
        exit;
      }
    }
 
    // header("HTTP/1.0 500 Internal Server Error");
    // exit;
    // 5 minutes execution time
    @set_time_limit(5 * 60);
    // Uncomment this one to fake upload time
    // usleep(5000);
    // Settings
    // $targetDir = ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload";
    $targetDir = 'php'.DIRECTORY_SEPARATOR.'upload'.DIRECTORY_SEPARATOR.'tmp';
    $uploadDir = 'php'.DIRECTORY_SEPARATOR.'upload'.DIRECTORY_SEPARATOR.date('Ymd');
    $cleanupTargetDir = true; // Remove old files
    $maxFileAge = 5 * 3600; // Temp file age in seconds
    // Create target dir
    if (!file_exists($targetDir)) {
      @mkdir($targetDir);
    }
    // Create target dir
    if (!file_exists($uploadDir)) {
      @mkdir($uploadDir);
    }
    // Get a file name
    if (isset($_REQUEST["name"])) {
      $fileName = $_REQUEST["name"];
    } elseif (!empty($_FILES)) {
      $fileName = $_FILES["file"]["name"]; 
    } else {
      $fileName = uniqid("file_");
    }

    $oldName = $fileName;

      $ar = explode('.',$fileName);
      $n = unicode_encode($ar[0]);
      if(count($ar)>1){ 
        $fileName = $n.'.'.$ar[1];
      }else{
        $fileName = $n.'.abc';
        } 
 
    $filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;
    // $uploadPath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;
    // Chunking might be enabled
    $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
    $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 1;
    // Remove old temp files
    if ($cleanupTargetDir) {
      if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
        die('{"code" : 0, "error" : {"code": 0, "message": "Failed to open temp directory."}, "id" : "id"}');
      }
      while (($file = readdir($dir)) !== false) {
        $tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;
        // If temp file is current file proceed to the next
        if ($tmpfilePath == "{$filePath}_{$chunk}.part" || $tmpfilePath == "{$filePath}_{$chunk}.parttmp") {
          continue;
        }
        // Remove temp file if it is older than the max age and is not the current file
        if (preg_match('/\.(part|parttmp)$/', $file) && (@filemtime($tmpfilePath) < time() - $maxFileAge)) {
          @unlink($tmpfilePath);
        }
      }
      closedir($dir);
    }
 
    // Open temp file
    if (!$out = @fopen("{$filePath}_{$chunk}.parttmp", "wb")) {
      die('{"code" : 0, "error" : {"code": 0, "message": "Failed to open output stream1111.'.$filePath.'"}, "id" : "id"}');
    }
    if (!empty($_FILES)) {
      if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
        die('{"code" : 0, "error" : {"code": 0, "message": "Failed to move uploaded file."}, "id" : "id"}');
      }
      // Read binary input stream and append it to temp file
      if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
        die('{"code" : 0, "error" : {"code": 0, "message": "Failed to open input stream222."}, "id" : "id"}');
      }
    } else {
      if (!$in = @fopen("php://input", "rb")) {
        die('{"code" : 0, "error" : {"code": 0, "message": "Failed to open input stream3333."}, "id" : "id"}');
      }
    }
    while ($buff = fread($in, 4096)) {
      fwrite($out, $buff);
    }
    @fclose($out);
    @fclose($in);
    rename("{$filePath}_{$chunk}.parttmp", "{$filePath}_{$chunk}.part");
    $index = 0;
    $done = true;
    for( $index = 0; $index < $chunks; $index++ ) {
      if ( !file_exists("{$filePath}_{$index}.part") ) {
        $done = false;
        break;
      }
    }
 
 
 
    if ( $done ) {
      $pathInfo = pathinfo($fileName);
      $hashStr = substr(md5($pathInfo['basename']),8,16);
      $hashName = time() . $hashStr . '.' .$pathInfo['extension'];
      $uploadPath = $uploadDir . DIRECTORY_SEPARATOR .$hashName;
 
      if (!$out = @fopen($uploadPath, "wb")) {
        die('{"code" : 0, "error" : {"code": 0, "message": "Failed to open output stream444."}, "id" : "id"}');
      }
      if ( flock($out, LOCK_EX) ) {
        for( $index = 0; $index < $chunks; $index++ ) {
          if (!$in = @fopen("{$filePath}_{$index}.part", "rb")) {
            break;
          }
          while ($buff = fread($in, 4096)) {
            fwrite($out, $buff);
          }
          @fclose($in);
          @unlink("{$filePath}_{$index}.part");
        }
        flock($out, LOCK_UN);
      }
      @fclose($out);
      $response = [
        'code'=>1,
        'oldname'=>$oldName,
        'url'=>'/'.$uploadPath,
        'filesize'=>format_bytes($_FILES["file"]['size']),
        'filesuffixes'=>$pathInfo['extension']
        // 'file_id'=>$data['id'],
        ];
 
      die(json_encode($response));
    } 
  
    die('{"code" : "0", "result" : null, "id" : "id"}');

}



  /**
     * 获取验证码
     * @return [type] [description]
     */
    public function verifycode(){
        $captcha = new \org\Captcha(config('captcha'));
        $rd = array('code'=>1,'msg'=>'success','data'=>array());
        $sendcode = substr(time(), 4,6);
        // if ($_POST['applytype']==2) {
        //   Session::set('tcverify',$sendcode); 
        // }else{
        //   Session::set('usercverify',$sendcode);
        // }


$pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
        if ( preg_match( $pattern, $_POST['username'] ) ){
$_POST['type']=1;
        }else{
   $_POST['type']=2;       
        }


Session::set('usercverify',$sendcode);


        if($_POST['type']==2){//手机验证码的
           
$message = '【城市数据派】派友你好，你的验证码是:'.$sendcode.',验证码十分钟内有效,请勿泄露!城市大数据，精彩尽在此www.udparty.com';
        $url = 'http://www.smswst.com/api/httpapi.aspx?action=send&account=13877620240&password=55555&mobile='.$_POST['username'].'&content='.$message.'&sendTime=&AddSign=Y';

       $a = getphcode($url);
          return json($a); 
            // sendmessage($_POST['username'],$message);
        }else{//邮箱验证码
            $title = "数据派注册邮箱验证";
            $message = '派友你好，终于等到你！<br>派姐欢迎你加入城市大数据派对!你的验证码是:'.$sendcode.',验证码十分钟内有效,请勿泄露!<br><br>城市大数据，精彩尽在此www.udparty.com<br>微信号：udparty';
   
$a = send_email($_POST['username'], $title, $message, null);
            // sendemail($_POST['username'], $title, $message, null);
   return json($a);
        }
        return json($rd);
    }





    public  function toVerifyCode(){
        $userid = Session::get('user')['userid']; 
        $user = Db::name('user')->where('userid','=',$userid)->find();
        $rd = array('code'=>1,'msg'=>'success','data'=>array());
        $data = array();
        $phonecode = Session::get('usercverify'); 
        $update = $_POST;


        // if ($_POST['applytype']==2) {
        //   $update['teacherapply'] = '2';         
        // }else{
        //   $update['nowtype'] = '1';           
        // }        
        if($_POST['phonecode'] != $phonecode){
            $rd['code'] = 0;
            $rd['msg'] = '您的短信或邮箱验证码错误！';
            return json($rd);
        }

        $update['nowtype'] = '1';
        $update['updatetime'] = time();
        unset($update['applytype']);
        unset($update['phonecode']);
        if($user['usertype']==3){$update['usertype'] = 1;}
        if($user['usertype']==4){$update['usertype'] = 2;}           
        $result = Db::name('user')->where('userid','=',$user['userid'])->update($update); 
        if (!$result) {
          $rd = array('code'=>0,'msg'=>'fail');
        }         
   
        return json($rd);
    }












 //文件图片上传    
public function formupload(){
    $rd = array('code'=>0,'msg'=>'fail','data'=>array());
    // 获取表单上传文件 例如上传了001.jpg
    $file = request()->file('file');
    // 移动到框架应用根目录/public/uploads/ 目录下
    $info = $file->move(ROOT_PATH . 'upload');
    if($info){
        $path = $info->getSaveName();

        $path = str_replace("\\","/",$path);
        $rd = array('code'=>1,'msg'=>'success','data'=>array('pathurl'=>$path));
    }
    return json($rd);
}












}