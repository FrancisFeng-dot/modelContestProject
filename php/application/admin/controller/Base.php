<?php
namespace app\admin\controller;
use think\Controller;
use think\Request;
use think\View;
use think\Session;
use think\Db;
class Base extends Controller
{

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
     * $request = Request::instance();
     * $request->ip();
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
    //裁切图片
    public function cutImg(){
        if(Request::instance()->isAjax() && Request::instance()->isPost()){ 
            $arr=explode('.', input('img_src'));
            $imgfirst=$arr[0];
            $imgtype=$arr[1];
            if(__ROOT__ == ''){
                $img_src='.'.input('img_src');
            }else{
                $img_src=str_replace(__ROOT__,'.',input('img_src'));
            }
            $image = new \Think\Image();
            $image->open($img_src);
            $percent=input('width')/$image->width();
            $width=intval(input('w')/$percent);
            $height=intval(input('h')/$percent);
            $x=input('x')/$percent;
            $y=input('y')/$percent;
            $image->crop($width,$height,$x,$y)->save(str_replace('.'.$imgtype,$width.'x'.$height.'.'.$imgtype,$img_src));
            $result['status']=true;
            $result['img_src']=str_replace('.'.$imgtype,$width.'x'.$height.'.'.$imgtype, input('img_src'));
            $this->ajaxReturn($result);
        }
    }



















}