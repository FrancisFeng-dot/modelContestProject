<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\View;
class Adminapi extends Controller
{



/**
 *测试接口
 * @param    string   $address     "json/pc/admin/menu.json" 
 * @return   arr
 * @author  wyl <181984609@qq.com>
 */
  public function text()
  {
    

     $arr = ['a'=>123];
    return rejson(1,'',$arr);
  }


/**
 *后端查找codeid
 * @param    string   $address     "json/pc/admin/menu.json" 
 * @return   arr
 * @author  wyl <181984609@qq.com>
 */
  public function findcodeid()
  {
    
    $view = new View();
    $arr = set_comview($view);
    return rejson(1,'',$arr);
  }



//公共方法
/** 
 *宇杉ajax示例方法——增  增c、删d、改u、查r 
 * @return   arr
 * @author  wyl <181984609@qq.com>
 */
public function com_editadd()
    {
     if(!input('codeid')){return rejson(0,'参数错误');}  
    $p = input('post.');
  if(count($p)==1){return rejson(0,'参数不齐全');} 
    //$p['createtime'] = time(); 
    $arr = explain_codeid(input('codeid')); 
    //return json($arr); 
  if($arr['idv']){
    //id值为不为空，这里是编辑更新
    $content= real_data($p,$arr);
    unset($content['data']['createtime']);

    $content['data']=admin_edittime($content['data']);
    $content['puublishname']=session('adminuser')['username'];
    $content['data']['updatetime'] =time();
     if(input('time1')){$content['data']['time1']=strtotime(input('time1'));}
    if(input('time2')){$content['data']['time2']=strtotime(input('time2'));}

    if(input('lessonstart')){$content['data']['lessonstart']=strtotime(input('lessonstart'));}
    if(input('lessonend')){$content['data']['lessonend']=strtotime(input('lessonend'));}
    if(input('dealtime')){$content['data']['dealtime']=strtotime(input('dealtime'));}
    if(input('hot')){
      $hotarr=explode(',',input('hot'));
      foreach ($hotarr as $hotkey => $hotvalue) {
        if(!$hotvalue)unset($hotarr[$hotkey]);
      }
      $content['data']['hot']=implode(',',$hotarr);
    }
    if(input('time1')&&input('time2')){
      $time=to_one_time(input('time1'),input('time2'));
    if($arr['tbn']=='volume'){
      if($time)
        $content['data']['timestr']=strtotime($time);
    }
  }
    // return json($content);
       $where[$arr['id']]=['=',$arr['idv']]; 
       $re = update_one($arr['tbn'],$where,$content['data']); 

       //观点审批未通过通知
       if ($re&&$arr['tbn']=='module_news'&&$content['data']['approved']=='1') {
       $thisnew = Db::name('module_news')->where('id','=',$content['data']['id'])->find();
       $thisnew['time'] = date("Y-m-d",$thisnew['createtime']);
       newnotice('您于'.$thisnew['time'].'发布的观点:<p>'.$thisnew['content'].'</p><p>因为'.$content['data']['reason'].',审核不通过</p>',$thisnew['uid']);
       }
       //活动审批未通过通知
       if ($re&&$arr['tbn']=='enroll'&&$content['data']['approved']=='1') {
       $thisnew = Db::name('enroll')->where('id','=',$content['data']['id'])->find();
       $activity = Db::name('module_news')->where('id','=',$thisnew['articleid'])->find();
       $thisnew['time'] = date("Y-m-d",$thisnew['createtime']);
         newnotice('您于'.$thisnew['time'].'报名参加的'.$activity['title'].'活动,因为'.$content['data']['reason'].',报名失败',$thisnew['uid']);
       }

       //教师未通过
       if ($re&&$arr['tbn']=='user') {
        if(isset($content['data']['applyteacher'])){
          if($content['data']['applyteacher']>0){
            if ($content['data']['applyteacher']=='2') {
              newnotice('您申请教师角色,审批不通过',$content['data']['id']);
            }else{ 
              newnotice('您申请教师角色,审批通过',$content['data']['id']);
            }
          }
        }
       }
          



  }else{
    //id值为空，这里是插入
    $content = real_data_noid($p,$arr);
    $content['data']=admin_edittime($content['data']);
    $content['puublishname']=session('adminuser')['username'];
    $content['data']['createtime']=time();
    $content['data']['updatetime'] =time();
    if(input('time1')){$content['data']['time1']=strtotime(input('time1'));}
    if(input('time2')){$content['data']['time2']=strtotime(input('time2'));}
    if(input('lessonstart')){$content['data']['lessonstart']=strtotime(input('lessonstart'));}
    if(input('lessonend')){$content['data']['lessonend']=strtotime(input('lessonend'));}
    if(input('hot')){
      $hotarr=explode(',',input('hot'));
      foreach ($hotarr as $hotkey => $hotvalue) {
        if(!$hotvalue)unset($hotarr[$hotkey]);
      }
      $content['data']['hot']=implode(',',$hotarr);
    }


    if($arr['tbn']=='module_news'){
    $hrefw['code']='activity';
    $field='id';
    $href1=get_datalist('category',$hrefw,$field);
    foreach ($href1 as $key => $value) {
      $hrefchild=get_child($value['id']);
      if(count($hrefchild)==0){
        $hrefchild[]=$value;
      }
      if(in_array(input('cateid'),$hrefchild))$content['data']['hreftype']='activity_detail';
    }

    $hrefw['code']='news';
    $field='id';
    $href1=get_datalist('category',$hrefw,$field);
    foreach ($href1 as $key => $value) {
      $hrefchild=get_child($value['id']);
      if(count($hrefchild)==0){
        $hrefchild[]=$value;
      }
      if(in_array(input('cateid'),$hrefchild))$content['data']['hreftype']='class_newsdetail';
    }
  }


    if(input('time1')&&input('time2')){
      $time=to_one_time(input('time1'),input('time2'));
    if($arr['tbn']=='volume'){
      if($time)
        $content['data']['timestr']=strtotime($time);
    }
  }

      $re = insert_data($arr['tbn'],$content['data']);    
  } 
  if($re){
    return rejson(1,'提交成功！'); 
    }else{
        return rejson(0,'提交失败！'); 
    } 
    }
//
// 公共插入方法，用户的插入，删除，更新，一定是非公共方法；
/** 
 *宇杉ajax示例方法——增  增c、删d、改u、查r 
 * @return   arr
 * @author  wyl <181984609@qq.com>
 */
public function com_insert()
    {
     if(!input('codeid')){return rejson(0,'参数错误');}  
    $p = input('post.');
  if(count($p)==1){return rejson(0,'参数不齐全');} 
  $p['createtime'] = time(); 
    $arr = explain_codeid(input('codeid')); 
    $content = real_data_noid($p,$arr);
    //调用模型层——插入表
    $result =  insert_data($arr['tbn'],$content['data']); 
  if($result>0){
    return rejson(1,'提交成功！'); 
    }else{
        return rejson(0,'提交失败！'); 
    } 
    }




// 公共删除方法；

/** 
 *宇杉公共方法——删  增c、删d、改u、查r 
 * @param    {codeid:"asdfdsd"}  
 * @return   arr
 * @author  wyl <181984609@qq.com>
 */
public function com_delete()
    { 
      if(!input('codeid')){return rejson(0,'参数错误');} 
      $codeid=explode(",",input('codeid'));
      $str=['tbn'=>'','id'=>'','idv'=>[]];
      foreach ($codeid as $key => $value) { 
        $str = explain_codeid($value);
        $arr['tbn'] = $str['tbn'];
        $arr['id']=$str['id'];
        $arr['idv'][]=$str['idv'];
      }   
      //调用模型层——删除表中记录
      $del = delete_data($arr['tbn'],$arr['id'],$arr['idv']); 
      if($del){
        return rejson(1,'删除成功！'); 
      }else{
        return rejson(0,'删除失败！'); 
      } 
}

  
// 公共更新方法，有些字段可以；部分字段不可以；
/** 
  *宇杉公共方法  更新 
  * @return   arr
  * @author  hsl
 */
public function com_update(){  
  if(!input('codeid')){return rejson(0,'参数错误');} 
    $codeid = input('codeid');  
     $p = input('post.');
     if(count($p)==1){return rejson(0,'参数不齐全');} 
     $arr = explain_codeid($codeid); 

     $where[$arr['id']]=['=',$p['id']];
     
     $re = update_one($arr['tbn'],$where,$p); 

    if($re>0){ 
    return rejson(1,'更新成功！'); 
      }else{
        return rejson(0,'更新失败！'); 
    }
 
    } 





// 无分页查询列表
public function com_list()
    {
    if(!input('codeid')){return rejson(0,'参数错误');} 
    //对codeid进行解析； 
    $arr = explain_codeid(input('codeid')); 
    // p($arr); 
    $where=[];
  
   //各表不同，关键字对应字段不同，走配置化
if(input('keyword')){
   $keyword= $arr['kws']==''?'keyword':$arr['kws'];
  $where[$keyword] = ['like','%'.input('keyword').'%'];}



    if(input('title')){$where['title'] = ['like','%'.input('title').'%'];}
     if(input('pid')){$where['pid'] = ['=',input('pid')];}
     if(input('cateid')){$where['cateid'] = ['=',input('cateid')];}
        if(input('qid')){$where['qid'] = ['=',input('qid')];}
    //调用模型层接口
    $data = get_datalist($arr['tbn'],$where);

    if(input('pid')){
      $data2 = Db::name('province')->select();
      foreach ($data as $key => $value) {
        foreach ($data2 as $key1 => $val) {
          if($value['pid']==$val['pid']){
            $data[$key]['province']=$val['province'];
          }
        }
      }
    }
    //对返回去的记录进行加密codeid，方便后面再次调用公共接口； 
    $datalist = set_codeid($data,$arr['num']); 
      if(count($datalist)>0){ 
         return rejson(1,'查询成功',$datalist);
      }else{
      return rejson(0,'查询失败',[]);   
      } 
    }


// 有分页查询列表 
public function com_list_page()
    {  
      $curPage = input('curPage')?input('curPage'):1;
      $listnum = input('listnum')?input('listnum'):10;
     //对codeid 进行解密；
      $arr = explain_codeid(input('codeid')); 
      $tbn = $arr['tbn']; 

      if(!$tbn){return rejson(1,'参数错误',$arr);} 
      $where =[];
      $order=[];
      if(input('lessontype')){$where['lessontype'] = ['=',input('lessontype')];}
      if(input('status')){$where['status'] = ['=',input('status')];}
      if(input('vip')){$where['vip'] = ['=',input('vip')];}
      if(input('cateid')){$where['cateid'] = ['in',input('cateid')];}
      if(input('articleid')){$where['articleid'] = ['in',input('articleid')];}
      if(input('usertype')){$where['usertype'] = ['=',input('usertype')];}
      if(input('usertype')==='0'){$where['usertype'] = ['=',input('usertype')];}
      if(input('username')){$where['username'] = ['like','%'.input('username').'%'];}
      if(input('qid')){$where['qid'] = ['=',input('qid')];}
      if(input('pid')){$where['pid'] = ['=',input('pid')];}
      if(input('uid')){$where['uid'] = ['=',input('uid')];}
      if(input('noticetype')){$where['noticetype'] = ['=',input('noticetype')];}
      if($arr['tbn']=='points'){$where['uid'] = ['=',input('id')];}
      //if($arr['tbn']=='volume'){$order = ['createtime' => 'desc'];}
 // p($arr);
//各表不同，关键字对应字段不同，走配置化
if(input('keyword')&&$arr['kws']!=''){
  $keyword = $arr['kws'];
  $where[$keyword] = ['like','%'.input('keyword').'%'];
}
if($arr['tbn']=='feed'){
      if(input('type')){$where['type'] = ['=',input('type')];}
      }
 if($arr['tbn']=='points'){
      if(input('uid')){$where['uid'] = ['=',input('uid')];}
      }     
  if($arr['tbn']=='audition'){
      $order = ['createtime' => 'desc'];
  }    
      $data = get_datalist_page($tbn,$where,$curPage,$listnum,$order);


      if($tbn=='recommend'){
        foreach ($data['datalist'] as $key => &$value) {
          $field='sum(price) amount';
          $w['pid']=['=',$value['id']];
          $d=get_data('user_order',$w,$field);
          $value['amount']=$d['amount'];

          if($value['country']){
            $countryarr=explode(',',$value['country']);
            if(is_array($countryarr)){
              foreach ($countryarr as $k => $v) {
                $cw['id']=$v;
                $field='country';
                $cdata=get_data('country',$cw,$field);
                $ctemp[]=$cdata['country'];
              }
              $value['countrystr']=implode(',',$ctemp);
            }else{
              $cw['id']=$countryarr;
              $field='country';
              $cdata=get_data('country',$cw,$field);
              $value['countrystr']=$cdata['country'];
            }
          }
        }
      }

      //查找module_news表里内容的父ID
      if($arr['tbn']=='module_news'){

        foreach ($data['datalist'] as $key => &$value) {

          if($value['teachers']){
            $arr2=explode(',',$value['teachers']);

            $value['teachers']=$arr2;
          }

          if(isset($value['hot'])){
            $value['hot']=explode(',',$value['hot']);
          }

          $w['id']=['=',$value['cateid']];
          $field='pid';
          $predata=get_data('category',$w,$field);
          $w['id']=['=',$predata['pid']];
          $field='name';
          $d=get_data('category',$w,$field);
          if($d){
          $value['prename']=$d['name'];
          }
        }
      }
      //查找class表里内容的父ID
      if($arr['tbn']=='class'){
        foreach ($data['datalist'] as $key => &$value) {
          $w['id']=['=',$value['articleid']];
          $field='cateid';
          $a=get_data('module_news',$w,$field);
          $w['id']=['=',$a['cateid']];
          $field='pid';
          $b=get_data('category',$w,$field);
          $w['id']=['=',$value['id']];
          $field='name';
          $d=get_data('category',$w,$field);
          if($d){
            $value['prename']=$d['name'];
          }
        }
      }
      //user表序号
      $c=0;
      if($arr['tbn']=='user'){
        foreach ($data['datalist'] as $key => &$value) {
          $c = $c+1;
          $value['listorder'] =$c;
        }
      }
      if($arr['tbn']=='feed'){
        foreach ($data['datalist'] as $key => &$value) {
          $c = $c+1;
          $value['level'] =$c;
          $w['id']=['=',$value['uid']];
          $field='nickname';
          $a=get_data('user',$w,$field);
          if($a){
            $value['username']=$a['nickname'];
          }
        }
      }
           
      //对结果进行加密主键id为codeid；
     $data['datalist'] = set_codeid($data['datalist'],$arr['num']);
      // p($data['datalist']);
      if(count($data['datalist'])>0){
         return rejson(1,'查询成功',$data);
      }else{
         return rejson(1,'查询失败',['total'=>0,'datalist'=>[]]); 
      }  
    }


// 无分页查询列表
public function com_detail()
    {
    if(!input('codeid')){return rejson(0,'参数错误');} 
    //对codeid进行解析； 
    $arr = explain_codeid(input('codeid'));  

     $p = input('post.');
    $tbn = $arr['tbn'];
    $data = Db::name($tbn)->where('id','=',$p['id'])->find();
      if($data>0){          
       //对返回去的记录进行加密codeid，方便后面再次调用公共接口； 
       $datav = set_codeid([$data],$arr['num']);  
         return rejson(1,'查询成功',$datav[0]);
      }else{
      return rejson(0,'查询失败',[]);   
      } 
    }

 
//无分页获取获取文章，以及头像公共接口
 public function com_artlist()
    {
    if(!input('codeid')){return rejson(0,'参数错误');} 
    //对codeid进行解析； 
    $arr = explain_codeid(input('codeid')); 
    // p($arr); 
    $where = array();
    $tbn=$arr['tbn'];
   //各表不同，关键字对应字段不同，走配置化
if(input('keyword')){
   $keyword= $arr['kws']==''?'keyword':$arr['kws'];
  $where[$keyword] = ['like','%'.input('keyword').'%'];
}
 

if(input('title')){$where['title'] = ['like','%'.input('title').'%'];}
if(input('pid')){$where['pid'] = ['=',input('pid')];}
if(input('cateid')){$where['cateid'] = ['=',input('cateid')];}
if(input('hot')){$where['hot'] = ['=',input('hot')];}
if(input('id')){$where['id'] = ['=',input('id')];}
if(input('articleid')){$where['articleid'] = ['=',input('articleid')];}

    //调用模型层接口
    $data = get_datalist($arr['tbn'],$where,'',input('top')); 
    if(count($data)==0){return rejson('0','无数据');}

    //对返回去的记录进行加密codeid，方便后面再次调用公共接口； 
    $data = set_codeid($data,$arr['num']); 
//查询对应的用户
  $uids = getids($data,'uid'); 
  $uwhere['id'] = ['in',$uids];
  $field = 'nickname,id,headimg,city,usertype';
  $arr2 = get_datalist('user',$uwhere,$field); 
  $data = jointwoarr($data,$arr2,'uid','id');

    

      if(count($data)>0){ 
         return rejson(1,'查询成功',$data,$arr);
      }else{
      return rejson(0,'查询失败',[]);   
      }  
    }



// 有分页查询列表 
  public function com_artlist_page()
      {  
        $curPage = input('curPage')?input('curPage'):1;
        $listnum = input('listnum')?input('listnum'):10;  
       //对codeid 进行解密；
        $arr = explain_codeid(input('codeid')); 
        $tbn = $arr['tbn'];  
  
        $where=[];
        $user=user_auth();
 
           //各表不同，关键字对应字段不同，走配置化
        if(input('keyword')){
           $keyword= $arr['kws']==''?'keyword':$arr['kws'];
          $where[$keyword] = ['like','%'.input('keyword').'%'];
        }
        if(input('status')){$where['status'] = ['=',input('status')];}
        if(input('cateid')){$where['cateid'] = ['in',input('cateid')];}
        //hsl
        if(input('articleid')){$where['articleid'] = ['=',input('articleid')];}
        if(input('uid')){$where['uid'] = ['=',input('uid')];}
        if(input('id')){$where['id'] = ['=',input('id')];}
        if(input('pid')){$where['pid'] = ['=',input('pid')];}
        if(input('hot')){$where['hot'] = ['=',input('hot')];}
        if($arr['tbn']=='user'){$order = ['certifytime' => 'desc'];}else{
        $order=['createtime' => 'desc'];
      }
        
        $data = get_datalist_page($tbn,$where,$curPage,$listnum,$order);

        if(count($data['datalist'])==0){return rejson('0','无数据');}
  
        //对数据进行处理
        if($tbn=='comment'||$tbn=='module_news'){
        $data['datalist']=datas_handle($data['datalist'],$user['id'],$tbn);
      }

        //对结果进行加密主键id为codeid；
       $data['datalist'] = set_codeid($data['datalist'],$arr['num']);  
      
    //查询对应的用户
      $uids = getids($data['datalist'],'uid'); 
      $uwhere['id'] = ['in',$uids];
      $field = 'nickname,id,headimg,city,usertype';
      $arr2 = get_datalist('user',$uwhere,$field); 
      $data['datalist'] = jointwoarr($data['datalist'],$arr2,'uid','id');




        if(count($data['datalist'])>0){
           return rejson(1,'查询成功',$data,$arr);
        }else{
           return rejson(0,'查询失败',['datalist'=>[],'total'=>0],$arr); 
        }  
      }














/*****************************wyl代码**************************************/

/**
 *首页接口 
 * @return   arr
 * @author  wyl  
 */
  public function base_select(){  

    $arr = [
    'version'=>'1.0',
    'huanjing'=>'',
    'system'=>'',
    'thinkver'=>'5.0.5',
    'phpver'=>'5.6',
    'msqlver'=>'5.5',
    'upload'=>'10M',


    'sitename'=>'YSframe',
    'team'=>'宇杉团队',
    'www'=>'www.ysframe.com',
    'address'=>'深圳市宝安区45区华丰裕安商务大厦',
    'qq'=>'181984609',
    'qun'=>'',
    'us'=>'0755-23011786'
    ];
    return rejson(1,'',$arr);
  }



/**
 *站点设置
 * @return   arr
 * @author  wyl  
 */
  public function website_select(){  
    
    $arr = get_datalist('system_setting'); 
    $re = array(); 
    foreach ($arr as $key => $value) {
      $re[$value['name']] = $value['value'];
    }
    return rejson(1,'',$re);
  }


/**
 *站点设置更新
 * @return   arr
 * @author  wyl  
 */
  public function website_update(){
  $arr = input('post.');  
    foreach ($arr as $key => $value) {
      $where['name'] = ['=',$key]; 
      $content = ['value'=>$value];
      update_one('system_setting',$where,$content); 
    }
    return rejson(1,'更新成功！');
  }


/**
 *数据库备份创建
 * @return   arr
 * @author  wyl  
 */
  public function database(){   
       $type=input("tp");
       $name=input("name");
       $sql=new \org\Baksql(config("database"));
       switch ($type)
        {
        case "backup": //备份
         $r = $sql->backup();
         if($r['code']==1){
          $data = ['createtime'=>time(),'name'=>$r['name']];
          insert_data('dbbase',$data);
          return rejson(1,$r['msg']);
         }else{
           return rejson(0,$r['msg']);
         }
         
          break;  
        case "dowonload": //下载
          $sql->downloadFile($name);
          break;  
        case "restore": //还原
          $re = $sql->restore($name);
          return rejson($re['code'],$re['msg']);
          break; 
        case "del": //删除
          $re = $sql->delfilename($name);
          return rejson($re['code'],$re['msg']);
          break;          
        default: //获取备份文件列表
            $data = $sql->get_filelist();
            return rejson(1,'数据库列表',$data);
          
        }
        
    }


/**
 *菜单增加
 * @param    string   $address     "json/pc/admin/menu.json" 
 * @return   arr
 * @author  wyl  
 */
public function menu_insert(){   
   
  return rejson(1,'插入成功',$data);
}

/**
 *菜单删除
 * @param    string   $address     "json/pc/admin/menu.json" 
 * @return   arr
 * @author  wyl  
 */
public function menu_del(){ 
  if(!input('id')){return rejson(0,'参数错误');}
  $ids = input('id');
  $arr = explode(',',$ids); 
  //查找下面是否有子栏目
  $v = 0;
  foreach ($arr as $key => $value) {
    $where['isdel'] = ['=','0']; 
    $where['pid'] = ['=',$value];
    $checkhasson = get_data('system_menu',$where); 
    if($checkhasson){$v++;}
   } 
   if($v>0){return rejson(0,'请先删除子栏目');}
  $re = delete_data('system_menu','id',$arr);
  $code = $re?1:0; 
  $msg = $re?'菜单删除成功':'菜单删除失败';
  return rejson($code,$msg);
}



/**
 *菜单修改更新
 * @param    string   $address     "json/pc/admin/menu.json" 
 * @return   arr
 * @author  wyl  
 */
public function menu_update(){ 
  if(!input('id')){return rejson(0,'参数错误');}
   
  $msg = $re?'菜单更新成功':'菜单更新失败';
  return rejson($code,$msg);
}



/**
 *菜单查询列表或者一条
 * @param    string   $address     "json/pc/admin/menu.json" 
 * @return   arr
 * @author  wyl  
 */
public function menu_select(){  
    if(input('id')){
      $where['id']=['=',input('id')];
      $data = get_data('system_menu',$where);
    }else{
      $sort = 'listorder asc';
      $list = get_datalist('system_menu',[],'','',$sort);
      foreach ($list as $key => &$value) {
        $value['namestr'] = $value['name'];
        $value['namelip'] = $value['name'];
      }
      $data = array();
        foreach ($list as $key => &$value) {
            if($value['pid']==0){
                $data[] = $value;
                $value['hasc'] = 0;
                $value['cststr'] = '';
                $value['cstlip'] = '';
                $data = getSons($value,$list,$data);
            } 
        } 
    } 
    $data = set_codeid($data,48);
  return rejson(1,'查询成功',$data);
}


/**
 *菜单查询列表或者一条
 * @param    string   $address     "json/pc/admin/menu.json" 
 * @return   arr
 * @author  wyl  
 */
public function menutree_select(){   
      $sort = 'listorder asc';
      $list = get_datalist('system_menu',[],'','',$sort);
      $arr = [];
      foreach ($list as $key => $value) { 
        if($value['pid']==0){
          $arr[] = $value;
        } 
      }


      foreach ($arr as $key => &$value) { 
        foreach ($list as $k => $val) {
          if($value['id']==$val['pid']){
            $value['chllist'][] = $val;
          }  
        } 

        foreach ($value['chllist'] as $key => &$v) { 
        foreach ($list as $k => $va) {
          if($v['id']==$va['pid']){
            $v['chllist'][] = $va;
          }  
        } 
      }     

         


      } 
   
  return rejson(1,'查询成功',$arr);
}






/**
 *导航栏目增加
 * @param    string   $address     "json/pc/admin/menu.json" 
 * @return   arr
 * @author  wyl  
 */
public function nav_insert(){   
   
  return rejson(1,'插入成功',$data);
}

/**
 *导航栏目删除
 * @param    string   $address     "json/pc/admin/menu.json" 
 * @return   arr
 * @author  wyl  
 */
public function nav_del(){ 
  if(!input('id')){return rejson(0,'参数错误');}
  $ids = input('id');
  $arr = explode(',',$ids); 
  //查找下面是否有子栏目
  $v = 0;
  foreach ($arr as $key => $value) {
    $where['isdel'] = ['=','0']; 
    $where['pid'] = ['=',$value];
    $checkhasson = get_data('category',$where); 
    if($checkhasson){$v++;}
   } 
   if($v>0){return rejson(0,'请先删除子栏目');}
  $re = delete_data('category','id',$arr);
  $code = $re?1:0; 
  $msg = $re?'菜单删除成功':'菜单删除失败';
  return rejson($code,$msg);
}



/**
 *导航栏目修改更新
 * @param    string   $address     "json/pc/admin/menu.json" 
 * @return   arr
 * @author  wyl  
 */
public function nav_update(){ 
  if(!input('id')){return rejson(0,'参数错误');}
   
  $msg = $re?'菜单更新成功':'菜单更新失败';
  return rejson($code,$msg);
}



/**
 *导航栏目查询列表或者一条
 * @param    string   $address     "json/pc/admin/menu.json" 
 * @return   arr
 * @author  wyl  
 */
public function nav_select(){  
    if(input('id')){
      $where['id']=['=',input('id')];
      $data = get_data('category',$where);
    }else{ 
      $sort = 'listorder asc'; 
      $list = get_datalist('category',[],'','',$sort);
      foreach ($list as $key => &$value) {
        $value['namestr'] = $value['name'];
        $value['namelip'] = $value['name'];
      }
       
      $data = array();
        foreach ($list as $key => &$value) { 
            if($value['pid']==0){
                $data[] = $value;
                $value['hasc'] = 0;

                $value['cststr'] = '';
                $value['cstlip'] = '';
                $data = getSons($value,$list,$data);
            }
        } 
    } 
    $data = set_codeid($data,5); 
  return rejson(1,'查询成功',$data);
}



/**
 *导航栏目查询列表或者一条
 * @param    string   $address     "json/pc/admin/menu.json" 
 * @return   arr
 * @author  wyl  
 */
public function nav_selectcontent(){  
    if(input('id')){
      $where['id']=['=',input('id')];
      $data = get_data('category',$where);
    }else{ 
      $sort = 'listorder asc';
      $where['id']=['not in','1,43,44,46,47,62,63,64'];
      $list = get_datalist('category',$where,'','',$sort);
      foreach ($list as $key => &$value) {
        $value['namestr'] = $value['name'];
        $value['namelip'] = $value['name'];
      }
       
      $data = array();
        foreach ($list as $key => &$value) { 
            if($value['pid']==0){
                $data[] = $value;
                $value['hasc'] = 0;

                $value['cststr'] = '';
                $value['cstlip'] = '';
                $data = getSons($value,$list,$data);
            }
        } 
    } 
    $data = set_codeid($data,5); 
  return rejson(1,'查询成功',$data);
}


/**
 *导航栏目查询列表或者一条
 * @param    string   $address     "json/pc/admin/menu.json" 
 * @return   arr
 * @author  fxx  
 */
public function nav_select2(){  
    if(input('id')){
      $where['id']=['=',input('id')];
      $data = get_data('category',$where);
    }else{ 
      $sort = 'listorder asc';
      $where='code="pvideo" or code="svideo" or code="video"';
      $list=Db::name('category')->where($where)->select();
      foreach ($list as $key => &$value) {
        $value['namestr'] = $value['name'];
        $value['namelip'] = $value['name'];
      }
      $data = array();
        foreach ($list as $key => &$value) { 
            if($value['pid']==0){
                $data[] = $value;
                $value['hasc'] = 0;
                $value['cststr'] = '';
                $value['cstlip'] = '';
                $data = getSons($value,$list,$data);
            }
        } 
    } 
    $data = set_codeid($data,5); 
  return rejson(1,'查询成功',$data);
}
/**
 *导航栏目查询列表或者一条
 * @param    string   $address     "json/pc/admin/menu.json" 
 * @return   arr
 * @author  fxx  
 */
public function nav_select3(){  
    if(input('id')){
      $where['id']=['=',input('id')];
      $data = get_data('category',$where);
    }else{ 
      $sort = 'listorder asc';
      $a=Db::name('category')->select();
      $b=array();
      foreach ($a as $key => $value) {
        if($value['code']=="view"){
          $b=$value['id'];
        }
      }
      $where='code="view" or pid='.$b.'';
      $list=Db::name('category')->where($where)->select();
      foreach ($list as $key => &$value) {
        $value['namestr'] = $value['name'];
        $value['namelip'] = $value['name'];
      }
      $data = array();
        foreach ($list as $key => &$value) { 
            if($value['pid']==0){
                $data[] = $value;
                $value['hasc'] = 0;
                $value['cststr'] = '';
                $value['cstlip'] = '';
                $data = getSons($value,$list,$data);
            }
        } 
    }
    
    $data = set_codeid($data,5);
    unset($data[0]);
    foreach ($data as $key => $value) {
      $temp[]=$value;
    }
  return rejson(1,'查询成功',$temp);
}



/**
 *导航栏目查询列表或者一条
 * @param    string   $address     "json/pc/admin/menu.json" 
 * @return   arr
 * @author  hsl 
 */
public function nav_select4(){  
    if(input('id')){
      $where['id']=['=',input('id')];
      $data = get_data('category',$where);
    }else{ 
      $sort = 'listorder asc';
      $a=Db::name('category')->select();
      $b=array();
      foreach ($a as $key => $value) {
        if($value['code']=="activity"){
          $b=$value['id'];
        }
      }
      $where='code="activity" or pid='.$b.'';
      $list=Db::name('category')->where($where)->select();
      foreach ($list as $key => &$value) {
        $value['namestr'] = $value['name'];
        $value['namelip'] = $value['name'];
      }
      $data = array();
        foreach ($list as $key => &$value) { 
            if($value['pid']==0){
                $data[] = $value;
                $value['hasc'] = 0;
                $value['cststr'] = '';
                $value['cstlip'] = '';
                $data = getSons($value,$list,$data);
            }
        } 
    } 
    unset($data[0]);
    $data = set_codeid($data,5); 
  return rejson(1,'查询成功',$data);
}




















/**
 *插入数据库表格
 * @param    string   $address     "json/pc/admin/menu.json" 
 * @return   arr
 * @author  wyl  
 */
public function dbtable_insert(){ 
    $tbn = 'ys_module_'.input('tbname');
    if(tableexist($tbn)){ 
          $msg = '数据表'.input('tbname').'已存在';
          return rejson(0,$msg); 
      }
  $default_values = config('default_values'); 
  $createTableSql=createAddTableSql($tbn, $default_values,input('tbdesc')); 
  $re = Db::query($createTableSql); 
  return rejson(1,'数据表'.$tbn.'创建成功',$re);

}

/**
 *删除数据表
 * @param    string   $address     "json/pc/admin/menu.json" 
 * @return   arr
 * @author  wyl  
 */
public function dbtable_del(){ 
   if(input('type')=='drop'){
        $re = Db::query('DROP TABLE IF EXISTS `'.input('name').'`;');
      return rejson(1,'删除成功',$re);
   }else if(input('type')=='clean'){
      $re = Db::query('TRUNCATE TABLE '.input('name').';');
      return rejson(1,'清除成功',$re);   
   }else{
    return rejson(0,'操作失败');  
   }

}

/**
 *查询数据库表格
 * @param    string   $address     "json/pc/admin/menu.json" 
 * @return   arr
 * @author  wyl  
 */
public function dbtable_select(){ 
     $list  = Db::query('SHOW TABLE STATUS');
         $list  = array_map('array_change_key_case', $list);
         foreach ($list as $key => &$value) {
          $value['data_length'] = format_bytes($value['data_length']); 
         }
  return rejson(1,'查询成功',$list);
}


/**
 *插入数据库表字段
 * @param    string   $address     "json/pc/admin/menu.json" 
 * @return   arr
 * @author  wyl  
 */
public function char_insert(){
  //表名
  //字段名
  //长度
  //备注
$arr = [
  'input'=>'varchar(255)',
  'textarea'=>'text',
  'cleareditor'=>'text',
  'editor'=>'text',
  'radio'=>'tinyint(1)',
  'checkbox'=>'varchar(255)',
  'select'=>'varchar(255)',
  'datetime'=>'int(10)',
  'input'=>'varchar(255)',
];

if(!input('type')){return rejson(0,'字段类型不能为空');}
if(!input('tbname')){return rejson(0,'表名不能为空');}
if(!input('Field')){return rejson(0,'字段名不能为空');} 
if(!input('Comment')){return rejson(0,'备注不能为空');}

if(input('Default')){
  $def = 'Default '.input('Default');
}else{
  $def = 'not null';  
}

  $re = Db::query('alter table  `'.input('tbname').'` add `'.input('Field').'` '.$arr[input('type')].' '.$def.' comment "'.input('Comment').'"');
  return rejson(1,'操作成功',$re);
}

/**
 *删除数据库表字段
 * @param    string   $address     "json/pc/admin/menu.json" 
 * @return   arr
 * @author  wyl  
 */
public function char_del(){ 
  $re = Db::query('alter table '.input('tbname').' drop column '.input('Field'));
  return rejson(1,'查询成功',$re);
}

/**
 *查询数据库表格
 * @param    string   $address     "json/pc/admin/menu.json" 
 * @return   arr
 * @author  wyl  
 */
public function char_select(){ 
  $re = Db::query('show full fields  from '.input('name'));
  return rejson(1,'查询成功',$re);
}



























































//获取详情聊天纪录
public function get_chat_detail(){
  if(!input('id1')){return rejson('0','参数未设置');}
  if(!input('id2')){return rejson('0','参数未设置');}
  $curPage = input('curPage')?input('curPage'):1;
  $listnum = input('listnum')?input('listnum'):10;


  $id1=input('id1');
  $id2=input('id2');
  $sql="SELECT * from ys_chatlog WHERE from_id =".$id1." AND to_id=".$id2." OR from_id=".$id2." AND to_id=".$id1;
  $data=DB::name('ys_chatlog')->query($sql);

  foreach ($data as $key => &$value) {
    $where['id']=$value['from_id'];
    $field='nickname';
    $d=get_data('user',$where,$field);
    $value['from_name']=$d['nickname'];
    $where['id']=$value['to_id'];
    $dd=get_data('user',$where,$field);
    $value['to_name']=$dd['nickname'];
  }


      $result=fenye($data,$listnum);
            
      if ($curPage>ceil(count($data)/$listnum)) {
        return rejson('0','该页无数据,页数超出范围');
      }

      $re['pages']=ceil(count($data)/$listnum);
      $re['datalist']=$result[$curPage-1];
      $re['total']=count($data);
     

      if($re){
        return rejson('1','成功',$re);
      }else{
        return rejson('0','失败');
      }
}








//获取最新聊天纪录
public function get_chat(){

      $keyword='';

      $curPage = input('curPage')?input('curPage'):1;
      $listnum = input('listnum')?input('listnum'):10;

      if(input('keyword')){
        $keyword=input('keyword');
      }
 
      if(!input('uid')){return rejson('0','uid未设置');}

      $uid=input('uid');


      if($keyword){
      $sql="SELECT A.* FROM ys_chatlog A,
      (SELECT timeline, MAX(timeline) max_time FROM ys_chatlog where from_id=".$uid." or to_id=".$uid." GROUP BY chatuser) B
      WHERE  A.timeline = B.max_time and content|from_name like'".$keyword."'
      GROUP BY timeline
      ORDER BY timeline desc";
      }else{
      $sql="SELECT A.* FROM ys_chatlog A,
      (SELECT timeline, MAX(timeline) max_time FROM ys_chatlog where from_id=".$uid." or to_id=".$uid." GROUP BY chatuser) B
      WHERE  A.timeline = B.max_time 
      GROUP BY timeline
      ORDER BY timeline desc";
      }


      
      $data=DB::name('ys_chatlog')->query($sql);
      if(!$data){return rejson('0','无数据');}


      if($data){
        foreach ($data as $key => $value) {
          $field='nickname,usertype';
          $from_id=$value['from_id'];
          $to_id=$value['to_id'];
          $w['id']=$from_id;
          $d1=get_data('user',$w,$field);
          $w['id']=$to_id;
          $d2=get_data('user',$w,$field);
          if($d1['usertype']==2){//from_id==2
            $field='nickname';
            $w['id']=$to_id;
            $userinfo=get_data('user',$w,$field);
            $value['nickname']=$userinfo['nickname'];
            $temp[]=$value;
          }elseif ($d2['usertype']==2) {//to_id==2
            $field='nickname';
            $w['id']=$from_id;
            $userinfo=get_data('user',$w,$field);
            $value['nickname']=$userinfo['nickname'];
            $temp[]=$value;
          }
        }
      }


      if(!$temp){return rejson('0','无数据');}

      $result=fenye($temp,$listnum);
            
      if ($curPage>ceil(count($temp)/$listnum)) {
        return rejson('0','该页无数据,页数超出范围');
      }


      $where2['tbname']=['=','module_news'];
      $data2=get_data('system_conf',$where2);
      $num=$data2['tbnum'];



      $re['pages']=ceil(count($temp)/$listnum);
      $re['datalist']=set_codeid($result[$curPage-1],$num);
      $re['total']=count($temp);



      if($re){
        return rejson('1','查询成功',$re);
      }else{
        return rejson('0','查询失败');
      }
      
}





//回收调查问卷
// public function question_recovery(){
//   if(!input('qid')){return rejson('0','qid未设置');}
//   $where['id']=input('qid');

//   $data['status']=3;
//   $re=update_one('module_question',$where,$data);
//   if($re){
//     return rejson('1','修改成功');
//   }else{
//     return rejson('0','修改失败');
//   }
// }




//问卷每道题的详情
public function question_detail_each(){
  if(!input('tid')){return rejson('0','tid未设置');}
  $curPage = input('curPage')?input('curPage'):1;
  $listnum = input('listnum')?input('listnum'):10;

  if(input('keyword')){
  $where['answer'] = ['like','%'.input('keyword').'%'];
}

  $tid=input('tid');
  $where['tid']=$tid;

  $data=get_datalist('module_question_answer',$where);
  if(!$data){return rejson('0','查询失败1');}
  foreach ($data as $key => &$value) {
    $w['id']=$value['uid'];
    $field='school,grade,class,nickname';
    $d=get_data('user',$w,$field);
    if(!$d){return rejson('0','无此用户');}

    $value['school']=$d['school'];
    $value['grade']=$d['grade'];
    $value['class']=$d['class'];
    $value['nickname']=$d['nickname'];
    $ww['uid']=$value['uid'];
    $field='createtime';
    $dd=get_data('module_question_info',$ww,$field);
    //if(!$dd){return rejson('0','查询失败2');}
    $value['createtime']=setdate($dd['createtime'],1);

  }

        $result=fenye($data,$listnum);
            
      if ($curPage>ceil(count($data)/$listnum)) {
        return rejson('0','该页无数据,页数超出范围');
      }

      $re['pages']=ceil(count($data)/$listnum);
      $re['datalist']=$result[$curPage-1];
      $re['total']=count($data);
     

      if($re){
        return rejson('1','成功',$re);
      }else{
        return rejson('0','失败');
      }
}







//问卷问题详情
public function question_detail(){
  if(!input('qid')){return rejson('0','qid未设置');}
  $qid=input('qid');
  $curPage = input('curPage')?input('curPage'):1;
  $listnum = input('listnum')?input('listnum'):10;

  $sql="SELECT uid,tid,type,answer from ys_module_question_answer WHERE tid in(
        SELECT id from ys_module_question_title
        WHERE qid=".$qid.")
        ORDER BY tid ASC
        ";
  $data=DB::name('ys_module_question_answer')->query($sql);

  $where['qid']=$qid;
  if(!$data){return rejson('0','无数据');}

  $dataT=get_datalist('module_question_title',$where);
  if(!$dataT){return rejson('0','无数据');}

  foreach ($dataT as $key => &$value) {
    $sql="SELECT count(answer) takenum from   ys_module_question_answer
          where tid=".$qid;
    $d=DB::name('ys_module_question_answer')->query($sql);
    if(!$d)$value['takenum']=0;
    $value['takenum']=$d[0]['takenum'];
    $we['tid']=$value['id'];
    $field='answer';
    $option=get_datalist('module_question_answer',$we,$field);
    if($option){
    $array=array();
    $arr=explode(',',$value['option']);
    $optioncount=count($arr);
    $value['optioncount']=$optioncount;
    $value['option']=$arr;  
    for ($i=0; $i < $optioncount; $i++) { 
      $array[]=0;
    }

    foreach ($option as $key2 => $value2) {

    
    $ar=explode(',',$value2['answer']);

    foreach ($arr as $k => &$v) {
      foreach ($ar as $k2 => $v2) {
        if($v==$v2){
          $array[$k]++;
        }
      }
    }
    
    $value['optionnum']=$array;
   }
  }
}

      $result=fenye($dataT,$listnum);
            
      if ($curPage>ceil(count($dataT)/$listnum)) {
        return rejson('0','该页无数据,页数超出范围');
      }

      $re['pages']=ceil(count($dataT)/$listnum);
      $re['datalist']=$result[$curPage-1];
      $re['total']=count($dataT);
     

      if($re){
        return rejson('1','成功',$re);
      }else{
        return rejson('0','失败');
      }
  //return json(['code'=>'1','msg'=>'查询成功','title'=>$dataT,'data'=>$data]);
}




//新增推荐人交易记录
public function add_user_order(){
    if(!input('id')){return rejson('0','id未设置');}
    $data['pid']=input('id');
    if(input('content')){$data['content']=input('content');}
    if(input('dealtime')){$data['dealtime']=strtotime(input('dealtime'));}
    if(input('price')){$data['price']=input('price');}
    $data['createtime']=time();

    $userdata = Db::name('user')->where('id','=',input('uid'))->find();
    $pointbili = Db::name('system_setting')->where('name','=','POINT_BILI')->find();
    $point= (int)$userdata['point'] + (int)input('price')*(int)$pointbili['value'];

    Db::name('user')->update(array('point' => $point,'id'=>input('uid')));

    $field='studentname';
    $where['id']=input('id');
    $pointstudent=get_data('recommend',$where,$field);

    $pointdata  = array('uid' => input('uid'),'activity'=>input('content'),'getpoints'=>(int)input('price')*(int)$pointbili['value'],'createtime'=>time(),'studentname'=>$pointstudent['studentname'],'point'=>$point,'fromid'=>$userdata['fromid']);

    insert_data('points',$pointdata);
    $re=insert_data('user_order',$data);
    if($re){
      return rejson('1','数据添加成功');
    }else{
      return rejson('0','数据添加失败');
    }
}



//查询用户的黑名单
public function black_list(){
  $temp=array();
  $curPage = input('curPage')?input('curPage'):1;
      $listnum = input('listnum')?input('listnum'):10;
  $arr = explain_codeid(input('codeid')); 
      $where =[];
if(input('keyword')){
   $keyword= $arr['kws']==''?'keyword':$arr['kws'];
  $where[$keyword] = ['like','%'.input('keyword').'%'];}
  $field='id,nickname,blackuser,blacktime';
  $data=get_datalist('user',$where,$field);
  foreach ($data as $key => $value) {
    if($value['blackuser']&&$value['blacktime']){
      $arr=explode(',',$value['blackuser']);
      $arrt=explode(',',$value['blacktime']);
      foreach ($arr as $k => $v) {
        $field='nickname';
        $where['id']=['=',$v];
        $d=get_data('user',$where,$field);
        $value['blackid']=$v;
        $value['blackuser']=$d['nickname'];
        $value['blacktime']=$arrt[$k];
        $temp[]=$value; 
      }
    }
  }
  if($temp){
    return rejson('1','查询成功',$temp);
  }else{
    return rejson('0','查询失败');
  }
}





//取消拉黑
public function cancle_black(){
  if(!input('uid')){return rejson('0','uid未设置');}
  if(!input('blackid')){return rejson('0','blackuid未设置');}
  
  $uid=input('uid');
  $blackid=input('blackid');
  $where['id']=['=',$uid];
  $field='blackuser,blacktime';
  $data=get_data('user',$where,$field);
  if(isset($data['blackuser'])&&isset($data['blacktime'])){
    if($data['blackuser']&&$data['blacktime']){
      $arr=explode(',',$data['blackuser']);
      $arrt=explode(',',$data['blacktime']);
      foreach ($arr as $key => $value) {
        if($value==$blackid){
          unset($arr[$key]);
          unset($arrt[$key]);
        }
      }
      $data['blackuser']=implode(',', $arr);
      $data['blacktime']=implode(',',$arrt);
    }
  }
  $re=update_one('user',$where,$data);
  if($re){
    return rejson('1','取消拉黑成功');
  }else{
    return rejson('0','用户不在黑名单里');
  }
}







/*****************************xubo代码**************************************/

/**
 *“登录日志”查询接口
 * @param    string   $address     "json/pc/admin/menu.json" 
 * @return   arr
 * @author  xubo  
 */
  public function log_select()
  {   
    $_POST = json_decode(file_get_contents('php://input'),true);
         //当前位置的页码数，如：点击“2”时，跳转到第二栏这个页面，点击“3”时，跳转到第三栏这个页面
        $curPage = $_POST['curPage']?$_POST['curPage']:1;
        //每页显示的“登录日志”条数(若接受不到，则显示为10条)
        $listnum = $_POST['listnum']?$_POST['listnum']:10;

        //查询数据库中的“登录日志”的总条数——count()方法
        $total = Db::name('user_log')->count();
                     

        //计算出总的页码数（如：每页显示10条数据，一共有N条数据，则需要多少页）
        $pages = ceil($total/$listnum);


        //page：用于显示页码数；limit：用于设置每页的最多“登录日志”数量
        //$data变量显示了该页码下的数据条数
        $data = Db::name('user_log')->page($curPage)->limit($listnum)->order('Id','asc')->select();

        //循环数组：修改数据库中的“登录时间”格式,并赋值给$value，但并不存入数据库
        foreach ($data as $key => &$value) {
            //时间格式转换
            $value['logintime']=date("Y-m-d",$value['logintime']);
                    }
            //total：数据的总条数; listnum：每页的数据条数; page：页码数 ; 
            //datalist:该页码下的数据条数
        $rd=['pages'=>$pages,'datalist'=>$data,'total'=>$total,'listnum'=>$listnum];
        return rejson(1,"success",$rd);
  }

 

/**
 *"会员管理"查询接口
 * @param    string   $address     "json/pc/admin/menu.json" 
 * @return   arr
 * @author  xubo
 */
  public function vip_select()
  {   
        $username=input('username');
        $email=input('email');
        $phone=input('phone');
        $where=null;

        if($username){
          $where .= ' and username like "%'.$username.'%"';
        }
       
        if($email){
            $where .= ' and phone='.$phone;
        }
        if($phone){
            $where .= ' and phone='.$phone;
        }
        if($where){
          $where=substr_replace($where,'',1,3);
          // return json($where);
        }
        $rd = Db::name('user')->where($where)->order('uid','desc')->select();
        // $rd['data'] = $list;
        return rejson(1,'success',$rd);
        
    

  }



/**
 *"订单管理"查询接口——分页查询
 * @param    string   $address     "json/pc/admin/menu.json" 
 * @return   arr
 * @author  xubo
 */
    public function order_select()
    { 

      
        $rd=Db::name('order')->order('id asc')->select();
        return rejson(1,'success',$rd);

    }

     



/**
 *"订单管理"删除接口
 * @param    string   $address     "json/pc/admin/menu.json" 
 * @return   arr
 * @author  xubo
 */
    public function order_del()
    { 
        
        $result = Db::name('order')->delete(input('id'));
        if(!$result){
           return rejson(0,'fail',[]);
        }
        return rejson(1,'success',[]);

    }




/**
 *"评论管理"查询接口
 * @param    string   $address     "json/pc/admin/menu.json" 
 * @return   arr
 * @author  xubo
 */

public function comment_select()
  {   

    $curPage = input('curPage')?input('curPage'):1;
      $listnum = input('listnum')?input('listnum'):10;  
     //对codeid 进行解密；
      $arr = explain_codeid(input('codeid')); 
      $tbn = $arr['tbn'];
      $where = [];
    //各表不同，关键字对应字段不同，走配置化
    if(input('keyword')&&$arr['kws']!=''){
       $keyword = $arr['kws'];
       $where[$keyword] = ['like','%'.input('keyword').'%'];}
 // p($where);
       $order=['createtime' => 'desc'];
 $data = get_datalist_page($tbn,$where,$curPage,$listnum,$order);
$c=0;
        foreach ($data['datalist'] as $key => &$value) {
          $c = $c+1;
          $value['level'] =$c;
        }

//查询对应的用户
  $uids = getids($data['datalist'],'uid'); 
  $uwhere['id'] = ['in',$uids];
  $field = 'username,nickname,id';
  $arr2 = get_datalist('user',$uwhere,$field); 
  $data['datalist'] = jointwoarr($data['datalist'],$arr2,'uid','id');

//拿数组的链接id；
$arids = getids($data['datalist'],'articleid');
$awhere['id'] = ['in',$arids];
$field = 'title,id';
$arr3 = get_datalist('module_news',$awhere,$field); 
// p($arr3);
$data['datalist'] = jointwoarr($data['datalist'],$arr3,'articleid','id'); 
 
  //set_codeid已对时间进行了转换             
        $data['datalist'] = set_codeid($data['datalist'],$arr['num']); 
        return rejson(1,"success",$data);
  }




/**
 *"意见反馈"查询接口
 * @param    string   $address     "json/pc/admin/menu.json" 
 * @return   arr
 * @author  xubo
 */

// public function feed_select()
//   {   

//     $arr = explain_codeid(input('codeid')); 
//     $where = [];
//     $where['isdel'] = ['=',0];
//     if(input('content')){
//       $where['content'] = ['like','%'.input('content').'%']; 
//     }
//     //hsl   反馈不需要审核
//     //$where['status'] = ['=',input('status')];
//         $curPage = input('curPage')?input('curPage'):1;
//         $listnum = input('listnum')?input('listnum'):10;
//         $total = Db::name('feed')->where($where)->count();
//         $pages = ceil($total/$listnum);
//         $data = Db::name('feed')->where($where)->page($curPage)->limit($listnum)->select();
//         $data2 = Db::name('user')->select();
//         foreach ($data as $key => $value) {
//           foreach ($data2 as $key1 => $val) {
//             if($value['uid']==$val['id']){
//               $data[$key]['username']=$val['username'];
//             }
//           }
//         }
//         //set_codeid已对时间进行了转换             
//         $datalist = set_codeid($data,$arr['num']);
//         $rd=['pages'=>$pages,'total'=>$total,'listnum'=>$listnum,'datalist'=>$datalist];
//         return rejson(1,"success",$rd);
//   }





//增加积分
//公共方法
/** 
 *宇杉ajax示例方法——增  增c、删d、改u、查r 
 * @return   arr
 * @author  wyl <181984609@qq.com>
 */
public function point_editadd()
{
    if(!input('codeid')){return rejson(0,'参数错误');}  
    $p = input('post.');
    if(count($p)==1){return rejson(0,'参数不齐全');} 
    $arr = explain_codeid(input('codeid')); 
    //id值为空，这里是插入
    $content = real_data_noid($p,$arr);
    $content['data']=admin_edittime($content['data']);
    $content['puublishname']=session('adminuser')['username'];
    $content['data']['createtime']=time();
    $content['data']['updatetime'] =time();
    $userdata = Db::name('user')->where('id','=',$content['data']['uid'])->find();
    // $pointbili = Db::name('ys_system_setting')->where('name','=','POINT_BILI')->find();
    $content['data']['point'] = (int)$userdata['point'] + (int)$content['data']["getpoints"];
    $content['data']['fromid'] =$userdata['fromid'];
    Db::name('user')->update(array('point' => $content['data']['point'],'id'=>$userdata['id']));
    $re = insert_data($arr['tbn'],$content['data']);    
    if($re){
      return rejson(1,'提交成功！'); 
    }else{
      return rejson(0,'提交失败！'); 
    } 
}







//公共方法
/** 
 *宇杉ajax示例方法——增  增c、删d、改u、查r 
 * @return   arr
 * @author  wyl <181984609@qq.com>
 */
public function volume_editadd()
    {
     if(!input('codeid')){return rejson(0,'参数错误');}  
     $p = input('post.');
     if(count($p)==1){return rejson(0,'参数不齐全');} 
  
    $arr = explain_codeid(input('codeid'));
    //$user=user_auth();
 
  if($arr['idv']){
    //id值为不为空，这里是编辑更新
    $content= real_data($p,$arr);
    unset($content['data']['createtime']);
    //$content['data']['uid'] = $user['id']; 
 
       $where[$arr['id']]=['=',$arr['idv']]; 
       $re = update_one($arr['tbn'],$where,$content['data']); 
 
    //审核通过后，去生成 试听券码； 
       //审批未通过
       if ($re&&$content['data']['status']=='2') {
         $thisnew = Db::name('volumeapply')->where('id','=',$content['data']['id'])->find();
         $thisnew['time'] = date("Y-m-d",$thisnew['createtime']);
         newnotice('您于'.$thisnew['time'].'申请'.$thisnew['numbers'].'张试听券,因为'.$content['data']['reason'].',审核不通过',$thisnew['uid']); 
       }
 
    $finddata =  get_data($arr['tbn'],$where);
if($content['data']['status']==1&&$finddata){
  //去查有没有这个券
  $wh['aid'] = ['=',$finddata['id']];
  $rev = get_data('volume',$wh);
 // p($rev);
  if(!$rev){
    $num = $finddata['numbers']; 
    for ($i=0; $i < $num; $i++) { 
      $data = ['testnum'=>cutcode(time(),6),'createtime'=>time(),'aid'=>$finddata['id'],'uid'=>$finddata['uid']];
      insert_data('volume',$data); 
    }

  }
}


  }else{
    //id值为空，这里是插入
    $content = real_data_noid($p,$arr); 
    $content['data']['createtime']=time();  
    $content['data']['uid'] = $user['id'];

    $re = insert_data($arr['tbn'],$content['data']);  

    




  } 
  if($re){
    return rejson(1,'提交成功！'); 
    }else{
        return rejson(0,'提交失败！'); 
    } 
    }









































































  



































































































































/*****************************gaopan代码**************************************/

/**
 *测试接口
 * @param    string   $address     "json/pc/admin/menu.json" 
 * @return   arr
 * @author  wyl  
 */
  public function bb_select()
  {  
    return rejson(1,input('keyword'));
  }


/**
    * 报名列表删除
    * @return [type] [description]
    */
    public function userlog_delete()
    { 
      $arr=input('id');
      $del = delete_data('audition','id',$arr); 
      if($del){
        return rejson(1,'删除成功！'); 
      }else{
        return rejson(0,'删除失败！'); 
      } 
    }

/**
    * 报名列表发送短信
    * @return [type] [description]
    */
    public function userlog_editadd()
    { 
    $p = input('post.');
    $arr = Db::name('audition')->where('id','=',$p['id'])->find();
    $content = '';
    if($arr['audition']==1){
        $content = '恭喜您通过初审，报名成功！编号为：'.$arr['username'].'。网络海选请通过链接注册梦想直.播账号按照参赛标准提交海选视频。赛事实时动态，请关注红纽扣传媒官方微信。赛事详询：0755-22956651。';
    }
    if($arr['audition']==2){
        $content = '恭喜您通过初审，报名成功！编号为：'.$arr['username'].'。现场海选时间与地点届时将短信通知。赛事实时动态，请关注红纽扣传媒官方微信。赛事详询：0755-22956651。';
    }
    $rearr=getphcode($arr['telephone'],$content);
    $arr['hassend']=$arr['hassend']+1;
    $re = update_one('audition',$arr['id'],$arr);  
    if($re){
      return rejson(1,'成功',$rearr); 
    }else{
      return rejson(0,'失败',$rearr); 
    } 
    }























































































/*****************************xiaoxi代码**************************************/

/**
 *测试接口
 * @param    string   $address     "json/pc/admin/menu.json" 
 * @return   arr
 * @author   xiaoxi 
 */
  public function dd_select()
  {  
    return rejson(1,input('keyword'));
  }






public function test(){
  $time1='1493400000';
  $time2=time();
  return json(to_one_time($time1,$time2));


}

public function feed_select()
    { 
        $rd = array('code'=>1,'msg'=>'success','data'=>array());
        $rd['data'] = Db::name('category')->where('id','=',input('id'))->find();
        return json($rd);
    }


public function feed_update()
    { 
      $rd = array('code'=>1,'msg'=>'success','data'=>array());
        $_POST = json_decode(file_get_contents('php://input'),true);
        $_POST['createtime'] = time();
        $result = Db::name('category')->update($_POST);
        if(!$result){
            $rd['code'] = 0;
            $rd['msg'] = 'fail';
        }
        return json($rd);
    } 




}