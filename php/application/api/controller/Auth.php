<?php
namespace app\api\controller;
use think\Controller;
use think\Request;
use think\View;
use think\Session;
use think\Db;
use think\Loader;
use think\cache\driver\Redis;
class Auth extends Init
{
    public function getcodeid()
    {
        return json(set_comview());
    }


    //公共方法
    // 无分页查询列表
  public function com_list()
      {
      if(!input('codeid')){return rejson(0,'参数错误');} 
      //对codeid进行解析； 
      $arr = explain_codeid(input('codeid'));
      $tbn=$arr['tbn'];
      $where = array();
      $sort=null;
      $uid=user_auth()['id'];
         //各表不同，关键字对应字段不同，走配置化
      if(input('keyword')){
         $keyword= $arr['kws']==''?'keyword':$arr['kws'];
        $where[$keyword] = ['like','%'.input('keyword').'%'];
      }

       if(input('myid')==1){$where['uid']=['=',$uid];}
       if(input('title')){$where['title'] = ['like','%'.input('title').'%'];}
       if(input('pid')){$where['pid'] = ['=',input('pid')];}
       if(input('cateid')){$where['cateid'] = ['in',input('cateid')];}
         if(input('articleid')){$where['articleid'] = ['=',input('articleid')];}
         //hsl
       if(input('id')){$where['id'] = ['in',input('id')];}
       if(input('qid')){$where['qid'] = ['=',input('qid')];}
       if(input('teacherid')){$where['teacherid'] = ['=',input('teacherid')];}
       if($tbn=='module_news'){
         $where['visible'] = 1;
         if(input('time')==='0'||input('time')==='1'){//0--往期+进行中  1--预告
            $where=live_handle($where,input('time'));
            $sort='livestart ASC';
         }
      }


      if(input('hot')){
        $where['hot'] = ['like','%'.input('hot').'%'];
      }

    if($tbn=='module_news'||$tbn=='comment'){
        $where['approved']=['=','2'];
    }


      //调用模型层接口
      if(input('sort')){
        $sort=input('sort');
        $data = get_datalist($arr['tbn'],$where,'',input('top'),$sort);
      }else{
        $data = get_datalist($arr['tbn'],$where,'',input('top'),$sort);
      }
        
		if(input('codetype')=='video'){
			//查询父级 
			$pc = Db::name('category')->where(' code="pvideo" or code="svideo" ')->select(); 
			$cw['pid'] = ['<>','0'];
			$cw['code'] = ['=','video']; 
			$cc = Db::name('category')->where($cw)->select(); 
			$newarr = [];
 			foreach ($cc as $key => $value) {
 				foreach ($pc as $k => $val) {
 					 if($value['pid']==$val['id']){
 					 	$value['pcatename'] = $val['name'];
 					 	$value['catename'] = $value['name']; 
 					 	$newarr[] = $value;
 					 }
 				}
 			}
 		 
			 $data = get_datalist($arr['tbn'],$where,'',input('top'),'');
			 foreach ($data as $key => &$value) {
			 	 foreach ($newarr as $k => $val) {
			 	 	 if($value['cateid']==$val['id']){
			 	 	 	$value['pcatename'] = $val['pcatename'];
 					 	$value['catename'] = $val['catename']; 
			 	 	 }
			 	 }
			 } 
		}



      //对数据进行处理
       if($tbn=='comment'||$tbn=='module_news'){
        $data=datas_handle($data,user_auth()['id'],$tbn);
      }
     
      //对返回去的记录进行加密codeid，方便后面再次调用公共接口； 
      $datalist = set_codeid($data,$arr['num']); 
        if(count($datalist)>0){ 
          $arr['post'] = input('post.');
           $arr['where'] = $where;
           return rejson(1,'查询成功',$datalist,$arr);
        }else{

        return rejson(0,'查询失败',[],$arr);   
        } 
      }
  
  // 有分页查询列表 
  public function com_list_page()
      {  
      
        $curPage = input('curPage')?input('curPage'):1;
        $listnum = input('listnum')?input('listnum'):10;  
       //对codeid 进行解密；
        $arr = explain_codeid(input('codeid')); 
        $user=user_auth();
    
        $tbn = $arr['tbn'];  
        $where=[];
         if($tbn=='module_news'||$tbn=='comment'){
        $where['approved']=['=','2'];
    }
           //各表不同，关键字对应字段不同，走配置化
        if(input('keyword')){
           $keyword= $arr['kws']==''?'keyword':$arr['kws'];
          $where[$keyword] = ['like','%'.input('keyword').'%'];
        }
        if(input('myid')==1){$where['uid']=['=',$user['id']];}
        if(input('status')){$where['status'] = ['=',input('status')];}
        if(input('is_black')){$where['is_black'] = ['=',input('is_black')];}
        //hsl
        if(input('cateid')){$where['cateid'] = ['in',input('cateid')];}
        //hsl
        if(input('articleid')){$where['articleid'] = ['=',input('articleid')];}
        if(input('uid')){$where['uid'] = ['=',input('uid')];}
        if(input('id')){$where['id'] = ['=',input('id')];}
        if(input('qid')){$where['qid'] = ['=',input('qid')];}
        if(input('hot')){$where['hot'] = ['like','%'.input('hot').'%'];}
        if(input('activity_status')==='0'||input('activity_status')){
          $actstatus=input('activity_status');
          $where=where_handle($where,$actstatus);
        }
         if($tbn=='module_news'){
          $where['visible'] = 1;
         if(input('time')==='0'||input('time')==='1'){
            $where=live_handle($where,input('time'));
         }
      }

$p = input('post.');
 if(isset($p['isapply'])){$where['isapply']=['=',input('isapply')];}




      if($arr['tbn']=='user'){$order = ['certifytime' => 'desc'];}else{
        $order=['createtime' => 'desc'];
      }
      if(input('name')=='point'){
        $where['uid']= input('uid');
      }

        $data = get_datalist_page($tbn,$where,$curPage,$listnum,$order);

      if($tbn=='recommend'){
        foreach ($data['datalist'] as $key => &$value) {
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


		if(input('codetype')=='video'){
			//查询父级 
			$pc = Db::name('category')->where(' code="pvideo" or code="svideo" ')->select(); 
			$cw['pid'] = ['<>','0'];
			$cw['code'] = ['=','video']; 
			$cc = Db::name('category')->where($cw)->select(); 
			$newarr = [];
 			foreach ($cc as $key => $value) {
 				foreach ($pc as $k => $val) {
 					 if($value['pid']==$val['id']){
 					 	$value['pcatename'] = $val['name'];
 					 	$value['catename'] = $value['name']; 
 					 	$newarr[] = $value;
 					 }
 				}
 			}
 		 
			 $data = get_datalist_page($arr['tbn'],$where,$curPage,$listnum,$order);
			 foreach ($data['datalist'] as $key => &$value) {
			 	 foreach ($newarr as $k => $val) {
			 	 	 if($value['cateid']==$val['id']){
			 	 	 	$value['pcatename'] = $val['pcatename'];
 					 	$value['catename'] = $val['catename']; 
			 	 	 }
			 	 }
			 }
		}



      if(input('name')=='point'){
          foreach ($data['datalist'] as $key => $values) {
            $userdata = Db::name('user')->where('id','=',$values['uid'])->find();
            //$data['datalist'][$key]['studentname'] = $userdata['nickname'];
            $data['datalist'][$key]['nowpoints'] = $userdata['point'];
          }
      }

      //对数据进行处理
       if($tbn=='comment'||$tbn=='module_news'){
        $data['datalist']=datas_handle($data['datalist'],$user['id'],$tbn);
      }

        //对结果进行加密主键id为codeid；
       $data['datalist'] = set_codeid($data['datalist'],$arr['num']);
        if(count($data['datalist'])>0){
           return rejson(1,'查询成功',$data,$arr);
        }else{
           return rejson(0,'查询失败',['datalist'=>[],'total'=>0]); 
        }  
      }

  
  // 无分页查询列表
  public function com_detail()
      {
      if(!input('codeid')){return rejson(0,'参数错误');} 
      //对codeid进行解析； 
      $arr = explain_codeid(input('codeid')); 
         
      $tbn = $arr['tbn']; 
      $where[$arr['id']]=['=',$arr['idv']];
      $user=user_auth();
      //调用模型层接口
      $data = DB::name($tbn)->where($where)->find(); 
       

      //对阅读量进行处理
      if($arr['tbn']=='module_news'){
        $data=viewnum_handle($data);
      }
      //p($data);
      //对数据进行处理
      if($tbn=='comment'||$tbn=='module_news'){
         $data=data_handle($data,$user['id'],$tbn);
      }


       if($arr['tbn']=='module_news'){//0--未参与 1--参与
          $uid=$user['id'];
 
            if (isset($data['takeuser'])) {//课程
              if ($data['takeuser']) {
                $data['isattend']=is_takein($data['takeuser'],$uid)?1:0;
              }else{$data['isattend']=0;}
            }

            if(isset($data['enrolluser'])){
              if($data['enrolluser']){
                  $data['canenroll']=is_takein($data['enrolluser'],$uid)?0:1;
                }
                else{$data['canenroll']=1;}
              }else{
                $data['canenroll']=1;
              }

             if($data['enrollnum']>=100){$data['canenroll']=0;}
             if($data['activitystime']<time()){$data['canenroll']=0;}
            }
       

        if($data>0){


         //对返回去的记录进行加密codeid，方便后面再次调用公共接口； 
         $datav = set_codeid([$data],$arr['num']); 

           return rejson(1,'查询成功',$datav[0],$arr);
        }else{
        return rejson(0,'查询失败',[]);   
        } 
      }    
  
  // 含用户信息的详情
  public function com_artdetail()
      {
      if(!input('codeid')){return rejson(0,'参数错误');} 
      //对codeid进行解析； 
      $arr = explain_codeid(input('codeid')); 
      $tbn = $arr['tbn']; 
      $user=user_auth();
      $where[$arr['id']]=['=',$arr['idv']];
       //  return json($arr);
      //调用模型层接口
      $data = get_data($tbn,$where);  
       
        if($data>0){
          //查询用户信息
          
         $uwhere['id'] = ['=',$data['uid']];
         $field = 'nickname,headimg,city,usertype';
         $arr2 = get_data('user',$uwhere,$field);
         $arr2['isblack']=2;

         //判断该用户是否为登陆用户的黑名单
         $where2['id']=['=',$user['id']];
         $field='blackuser';
         $arr3=get_data('user',$where2,$field);
         if($arr3){
           if(isset($arr3['blackuser'])){
              if($arr3['blackuser']){
                $a=explode(',',$arr3['blackuser']);
                foreach ($a as $key => $value) {
                  if($value==$data['uid'])
                    $arr2['isblack']=1;
                }
              }
           }
        }

         if($arr){
          $data =  array_merge($data, $arr2);
         } 
        //对数据进行处理
        if($tbn=='comment'||$tbn=='module_news'){
         $data=data_handle($data,$user['id'],$tbn);
      }

         //对返回去的记录进行加密codeid，方便后面再次调用公共接口； 
         $datav = set_codeid([$data],$arr['num']);  
           return rejson(1,'查询成功',$datav[0],$arr);
        }else{
        return rejson(0,'查询失败',[]);   
        } 
      }    
  


 /** 
 *宇杉公共方法——获取文章，以及头像公共接口
 * @param    {codeid:"asdfdsd"}  
 * @return   arr
 * @author  wyl <181984609@qq.com>
 */
    public function com_artlist()
    {
    if(!input('codeid')){return rejson(0,'参数错误');} 
    //对codeid进行解析； 
    $arr = explain_codeid(input('codeid')); 
    // p($arr); 
    $where = array();
    $tbn=$arr['tbn'];
    $user=user_auth();
   //各表不同，关键字对应字段不同，走配置化
 if($tbn=='module_news'||$tbn=='comment'){
        $where['approved']=['=','2'];
    }

if(input('keyword')){
   $keyword= $arr['kws']==''?'keyword':$arr['kws'];
  $where[$keyword] = ['like','%'.input('keyword').'%'];
}
 
if(input('myid')==1){$where['uid']=['=',$user['id']];}
if(input('title')){$where['title'] = ['like','%'.input('title').'%'];}
if(input('pid')){$where['pid'] = ['=',input('pid')];}
if(input('cateid')){$where['cateid'] = ['in',input('cateid')];}
if(input('hot')){$where['hot'] = ['=',input('hot')];}
if(input('id')){$where['id'] = ['=',input('id')];}
if(input('articleid')){$where['articleid'] = ['=',input('articleid')];}
      if ($tbn=='module_news') {
        $where['visible'] = 1;
      }
    //调用模型层接口
    $data = get_datalist($arr['tbn'],$where,'',input('top'));
    if(count($data)==0){return rejson('0','无数据');}

  //对数据进行处理
   if($tbn=='comment'||$tbn=='module_news'){

        $data=datas_handle($data,$user['id'],$tbn);
    }

  //对时间进行判断，是否是往期或预期活动
  // $data['']

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
        $user=user_auth();
        $where=[];
  if($tbn=='module_news'||$tbn=='comment'){
        $where['approved']=['=','2'];
    }
           //各表不同，关键字对应字段不同，走配置化
        if(input('keyword')){
           $keyword= $arr['kws']==''?'keyword':$arr['kws'];
          $where[$keyword] = ['like','%'.input('keyword').'%'];
        }
        if(input('status')){$where['status'] = ['=',input('status')];}
        if(input('is_black')){$where['is_black'] = ['=',input('is_black')];}
        if(input('cateid')){$where['cateid'] = ['in',input('cateid')];}
        //hsl
        if(input('articleid')){$where['articleid'] = ['=',input('articleid')];}
        if(input('uid')){$where['uid'] = ['=',input('uid')];}
        if(input('id')){$where['id'] = ['=',input('id')];}
        if(input('pid')){$where['pid'] = ['=',input('pid')];}
        if(input('hot')){$where['hot'] = ['=',input('hot')];}
        if(input('myid')==1){$where['uid']=['=',$user['id']];}
        if($arr['tbn']=='user'){$order = ['certifytime' => 'desc'];}else{
        $order=['createtime' => 'desc'];
      }
      if ($tbn=='module_news') {
        $where['visible'] = 1;
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
           return rejson(0,'查询失败',['datalist'=>[],'total'=>0]); 
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
      if(input('articleid')){
        Db::name('module_news')->where('id',input('articleid'))->update(['commentnum' => ['exp','commentnum-1']]);
      }

      if(count($codeid)==1){
        $codeid='';$codeid=input('codeid');
        $str = explain_codeid($codeid);
        $arr['tbn'] = $str['tbn'];
        $arr['id']=$str['id'];
        $arr['idv']=$str['idv'];

        $del = delete_data($arr['tbn'],$arr['id'],$arr['idv']);
          if($del){
            if($arr['tbn']=="comment"){
              $field='pid,articleid';
              $w['id']=$arr['idv'];
              $d=get_data('comment',$w,$field);
              if($d){
                if($d['pid']){
                  Db::name('comment')->where('id',$d['pid'])->update(['comnum' => ['exp','comnum-1']]);
                }

                // if($d['articleid']){
                // Db::name('module_news')->where('id',$d['articleid'])->update(['commentnum' => ['exp','commentnum-1']]);
                // }
              }
            }
            return rejson(1,'删除成功！'); 
          }else{
            return rejson(0,'删除失败！'); 
          } 
      }
      //p($codeid);
      
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








    //新增观点
  public function add_news(){

      $user=user_auth();
      $data['uid']=$user['id'];
      $tbn='module_news';
      $data['createtime']=time();
      $arr=array();

      if(input('cateid')){$data['cateid']=input('cateid');}     
      if(input('title')){$data['title']=input('title');}
      //if(input('description')){$data['description']=input('description');}
      if(input('thumb/a')){
        $arr=input('thumb/a');
        $data['thumb']=implode(',',$arr);
      }


      if(input('content')){$data['content']=input('content');}
      if(input('url')){$data['url']=input('url');}
      //if(input('isdel')){$data['isdel']=input('isdel');}
      if(input('position')){$data['position']=input('position');}


      $uwhere['id'] = ['=',$data['uid']];
      $field = 'nickname,headimg,city,usertype';
      $arr2 = get_data('user',$uwhere,$field);
      if(!$arr2){return rejson('0','获取不到用户信息');}
      $ar=array_merge($data,$arr2);

      //添加纪录
      $result=insert_data($tbn,$data);

      if($result){
        return rejson('1',"添加成功",$ar);
      }else{
        return rejson('0',"添加出错");
      }
  }

    //话题评论页-新增评论
    public function add_comment()
    { 
      $user=user_auth();
      $data=array();
      $tbn='comment';
      $data['createtime']=time();
      $data['thumbcount']=0;

      $data['uid']=$user['id'];

      if(input('articleid')){$data['articleid']=input('articleid');}
      if(input('pid')){$data['pid']=input('pid');}
      if(input('content')){$data['content']=input('content');} 
      if(input('status')){$data['status']=input('status');}

      if(input('comimg/a')){
        $arr=input('comimg/a');
        $data['comimg']=implode(',',$arr);
      }

      if(input('thumbcount')){$data['thumbcount']=input('thumbcount');}
      if(input('star')){$data['star']=input('star');}

      if(!(input('pid')||input('articleid'))){
        return rejson('0','pid或articleid不存在');
      }
      
      if(input('pid')){//对评论进行评论
        $where['id']=['=',input('pid')];
        $re=get_data($tbn,$where);
        if ($re) {//评论数
          Db::name('comment')->where('id',input('pid'))->update(['comnum' => ['exp','comnum+1']]);
        
          //添加通知信息
          $noticedata['user_id']=$user['id'];
          $noticedata['from_id']=$re['uid'];
          $noticedata['type']=1;
          $noticedata['content']=$user['nickname']."评论了您的评论";
          $noticedata['createtime']=time();
          insert_data('notice',$noticedata);
        }
      }

      if(input('articleid')){//对评论进行评论
        $where['articleid']=['=',input('articleid')];
        $re=get_data($tbn,$where);
        if ($re) {//评论数
          Db::name('comment')->where('id',input('articleid'))->update(['commentnum' => ['exp','commentnum+1']]);    
          //添加通知信息
          $noticedata['user_id']=$user['id'];
          $noticedata['from_id']=$re['uid'];
          $noticedata['type']=1;
          $noticedata['content']=$user['nickname']."评论了您的观点";
          $noticedata['createtime']=time();
          insert_data('notice',$noticedata);
        }
      }


      $uwhere['id'] = ['=',$data['uid']];
      $field = 'nickname,headimg,city,usertype';
      $arr2 = get_data('user',$uwhere,$field);

      $ar=array_merge($data,$arr2);


      //添加纪录
      $result=insert_data($tbn,$data);
      $ar['id']=$result;
      $ar['createtime']=time_handle(time());

      if($result){
        return rejson('1',"添加成功",$ar);
      }else{
        return rejson('0',"添加出错");
      }

    }

    //评论页-收藏
    public function add_college()
    {

      $user=user_auth();
      $uid=$user['id'];

      $tbn ='college'; 
      $data['createtime']=time();
      $temp=1;
      if(input('articleid'))$articleid=input('articleid');
      else{return rejson('0',' articleid不存在');}
      $where['articleid']=['=',$articleid];

      $data=get_data($tbn,$where);
      if (!$data) {//查不到数据则增加一条数据
        $newdata['createtime']=time();      
        $newdata['articleid']=$articleid;
        $newdata['collegeUser']=$uid;
        $result=insert_data($tbn,$newdata);
      }
      else if($data['collegeUser']){
          $re=explode(',',$data['collegeUser']);
          //遍历数组中是否已经有该id,如果存在则减掉
          foreach ($re as $key => $value) {
            if ($value==$uid) {
              $temp=0;
              unset($re[$key]);
            }
          }
          $data['collegeUser']=implode(',',$re);

          //如果不存在就加上
          if($temp==1){
            $data['collegeUser']=$data['collegeUser'].','.$uid;
          }
          $content['collegeUser']=$data['collegeUser'];

          $result=update_one('college',$where,$content);
      }else{
        $content['collegeUser']=$uid;
        $result=update_one('college',$where,$content);
      }

      $reType['collegeType']=$temp;
      if ($result) {
        return rejson('1','添加成功',$reType);
      }else{
      return rejson('0',"添加失败");
        }
    }

    //评论页-点赞
    public function add_zan()
    {
      $user=user_auth();
      $uid='';
      $zanType='1';

      $uid=$user['id'];

      if(!input('id')){
        return rejson('0','id不存在');
      }

        if(!input('type')){
            return rejson('0','type未设置(1--评论，2--news)');
        }
        switch (input('type')) {
            case '1':
                $tbn = 'comment';
                $noticedata['user_id']=$uid;
                $noticedata['from_id']=input('id');
                $noticedata['type']=2;
                $noticedata['content']=$user['nickname']."赞了您的评论";
                $noticedata['createtime']=time();
                insert_data('notice',$noticedata); 
                break;
 
            case '2':
                $tbn = 'module_news'; 
                $noticedata['user_id']=$uid;
                $noticedata['from_id']=input('id');
                $noticedata['type']=2;
                $noticedata['content']=$user['nickname']."赞了您的观点";
                $noticedata['createtime']=time();
                insert_data('notice',$noticedata); 
                break; 

            default:
                break;
        }

      $where['id']=['=',input('id')];

      $data=get_data($tbn,$where);
      if (!$data) {
        return rejson('0','数据不存在');
      }
        //判断是否为空
        if($data['zanUser']){
          $re=explode(',',$data['zanUser']);
          $ret=explode(',',$data['zanTime']);


          //遍历数组中是否已经有uid,如果存在则减掉
          foreach ($re as $key => $value) {
            if ($value==$uid) {
              $zanType='0';
              unset($re[$key]);
              unset($ret[$key]);
            }
          }

          $data['zanUser']=implode(',',$re);
          $data['zanTime']=implode(',',$ret);
          //如果不存在就加上
          if($zanType=='1'){
            $data['zanUser']=$data['zanUser'].','.$uid;
            $data['zanTime']=$data['zanTime'].','.time();
          }
        }else{//如果该字段无数据则直接添加
          $data['zanUser']=(String)$uid;
          $data['zanTime']=time();
        }

            //判断点赞人数
            $re=explode(',',$data['zanUser']);
            if(empty($re)){
                $thumbcount=0;
            }else{
                if($re[0]=='')$thumbcount=0;
                else$thumbcount=count($re);
            }
      
      $content['zanType']=$zanType;      
      $content['thumbcount']=$thumbcount;
      $content['zanUser']=$data['zanUser'];
      $content['zanTime']=$data['zanTime'];
      $content['uid']=$uid;
       
      $result=update_one($tbn,$where,$content);



      if ($result) {
        return rejson('1','添加成功',$content);
      }else{
      return rejson('0',"添加失败");
        }
    }


  
    //观点互动--用户主页
    public function opinion_userinfo(){

      $user=user_auth();
      $where=[];
      if(input('id')){$where['id']=['=',input('id')];}else{
        return rejson('0','id不存在');
      }   
      $field='nickname,tags,headimg,id';
      $data=get_data('user',$where,$field);
      $data['isblack']=2;
      //判断该用户是否为登陆用户的黑名单
         $where2['id']=['=',$user['id']];
         $field='blackuser';
         $arr3=get_data('user',$where2,$field);
         if($arr3){
           if(isset($arr3['blackuser'])){
              if($arr3['blackuser']){
                $a=explode(',',$arr3['blackuser']);
                foreach ($a as $key => $value) {
                  if($value==input('id'))
                    $data['isblack']=1;
                }
              }
           }
        }


      if ($data) {
        return rejson('1','查询成功',$data);
      }else{
      return rejson('0','查询失败');
      }
    }




    //报名表信息保存
    public function add_enroll(){

      $user=user_auth();
      $data['uid']=$user['id'];
      $uid=$user['id'];
      if(!input('articleid')){return rejson('0','articleid未设置');}
      $articleid=input('articleid');
      yscheck(input('post.'));

      $where['id']=['=',$articleid];
      $field='enrolluser';
      $eu=get_data('module_news',$where,$field);
      if($eu){
        if($eu['enrolluser']){
          $arr=explode(',',$eu['enrolluser']);
          foreach ($arr as $key => $value) {
            if($value==$user['id'])
              return rejson('0','用户已经报名过');
          }
          $eu['enrolluser']=$eu['enrolluser'].",".$uid;
        }else{
          $eu['enrolluser']=$user['id'];
        }
        update_one('module_news',$where,$eu);
      }
      
      $data['articleid']=input('articleid');
      $data['childName']=input('childName');
      $data['childSchool']=input('childSchool');
      $data['grade']=input('grade');

      $data['parentName']=input('parentName');
      $data['parentPhone']=input('parentPhone');
      $data['address']=input('address');
      $data['urgentContact']=input('urgentContact');

      $data['urgentPhone']=input('urgentPhone');
      $data['attention']=input('attention');
      $data['createtime']=time();
      $data['approved']=1;

      $re=insert_data('enroll',$data);
      if ($re) {
        Db::name('module_news')->where('id',input('articleid'))->update(['enrollnum'=>['exp','enrollnum+1']]);
        return rejson('1',"添加成功",$data);
      }else{
      return rejson('0',"添加失败");
      }
    }







    //获取订单页列表
    public function order_list(){
      //return json(input('status'));
      // if(input('status')==='0'){$status=input('status');}
      // else{return rejson('0','状态status未设置');}
      if(input('status')>0||input('status')==='0'){$status=input('status');}
      else{return rejson('0','状态status未设置');}


      $curPage = input('curPage')?input('curPage'):1;
      $listnum = input('listnum')?input('listnum'):10;

      if(input('keyword')){
        $where['order_num|order_name'] = array('like','%'.input('keyword').'%');
      }

      $user=user_auth();

      $status=intval($status);
      $uid=$user['id'];
      $where['user_id']=['=',$uid];
      $where['status']=['=',$status];
      $data=get_datalist('order',$where);


      foreach ($data as $key => &$value) {
        if (isset($value['order_time'])) {
          $value['order_time']=order_time_handle($value['order_time']);
        }
      }


      if(!$data){return rejson('0','无数据');}
      $result=fenye($data,$listnum);
            
      if ($curPage>ceil(count($data)/$listnum)) {
        return rejson('0','该页无数据,页数超出范围');
      }


      $where2['tbname']=['=','order'];
      $data2=get_data('system_conf',$where2);
      $num=$data2['tbnum'];



      $re['pages']=ceil(count($data)/$listnum);
      $re['datalist']=set_codeid($result[$curPage-1],$num);
      $re['total']=count($data);

      if ($re) {
        return rejson('1','查询成功',$re);
      }else{
        return rejson('0','无数据');
      }
    }






    //订单页信息保存
    public function add_order()
    {

      $uid=user_auth()['id'];
      $data['user_id']=$uid;

      if(input('class')==1){
        if(input('id')){$cid=input('id');}
        $where['id']=$cid;
        $d=get_data('class',$where);
        if ($d) {
          if($d['takeuser']){
            $arr=explode(',',$d['takeuser']);
            foreach ($arr as $key => $value) {
              if($value==$uid)
                return rejson('0','用户已参与过');
            }
            $d['takeuser']=$d['takeuser'].','.$uid;
          }else{
            $d['takeuser']=$uid;
          }
        }
        $re=update_one('class',$where,$d);
        if(!$re){return rejson('0','class表记录更新失败');}
      }



      if(input('cart_session_id')){$data['cart_session_id']=input('cart_session_id');}
      if(input('order_num')){$data['order_num']=input('order_num');}
      if(input('payer_email')){$data['payer_email']=input('payer_email');}
      if(input('payer_phone')){$data['payer_phone']=input('payer_phone');}

      if(input('payer_name')){$data['payer_name']=input('payer_name');}
      if(input('user_name')){$data['user_name']=input('user_name');}
      if(input('phone')){$data['phone']=input('phone');}
      if(input('country_id')){$data['country_id']=input('country_id');}
      if(input('country_name')){$data['country_name']=input('country_name');}
      
      if(input('city_id')){$data['city_id']=input('city_id');}
      if(input('city_name')){$data['city_name']=input('city_name');}
      if(input('delivery_id')){$data['delivery_id']=input('delivery_id');}
      if(input('delivery_name')){$data['delivery_name']=input('delivery_name');}
      if(input('delivery_num')){$data['delivery_num']=input('delivery_num');}
      if(input('province')){$data['province']=input('province');}
      if(input('city')){$data['city']=input('city');}

      if(input('area')){$data['area']=input('area');}
      if(input('address')){$data['address']=input('address');}
      if(input('comment')){$data['comment']=input('comment');}
      if(input('pay_type_id')){$data['pay_type_id']=input('pay_type_id');}
      if(input('pay_type_name')){$data['pay_type_name']=input('pay_type_name');}

      if(input('senders_name')){$data['senders_name']=input('senders_name');}
      if(input('country_region')){$data['country_region']=input('country_region');}
      if(input('senders_phone')){$data['senders_phone']=input('senders_phone');}
      if(input('transport_price')){$data['transport_price']=input('transport_price');}

      if(input('total_price')){$data['total_price']=input('total_price');}
      if(input('pay_price')){$data['pay_price']=input('pay_price');}
      if(input('status')){$data['status']=input('status');}
      if(input('is_show')){$data['is_show']=input('is_show');}
      if(input('order_time')){$data['order_time']=input('order_time');}


      if(input('pay_time')){$data['pay_time']=input('pay_time');}
      if(input('send_time')){$data['send_time']=input('send_time');}
      if(input('end_time')){$data['end_time']=input('end_time');}
      if(input('bill')){$data['bill']=input('bill');}
      if(input('bill_type')){$data['bill_type']=input('bill_type');}

      if(input('billname')){$data['billname']=input('billname');}
      if(input('isdel')){$data['isdel']=input('isdel');}
      if(input('order_name')){$data['order_name']=input('order_name');}


      $re=insert_data('order',$data);
      if ($re) {
        return rejson('1',"添加成功",$data);
      }else{
      return rejson('0',"添加失败");
      }
    }



    //个人中心--主页
    public function user_center_info(){
      
      $uid=user_auth()['id'];

      if(input('uid')){
          $uid=decode(input('uid'));
      }

      
      $field='nickname,city,worktype,usertype,vip,headimg,grade,applyteacher,full_name,id';    
      $where['id']=['=',$uid];
      $data=get_data('user',$where,$field);
     // p($data);
     
//查询此人的试听券
$wheret['uid'] = ['=',$uid];
$wheret['isapply'] = ['=','0'];
$data['volumenumber'] = countnum('volume',$wheret);


      if($data){
        // $data['id']=encode($uid);
        return rejson('1','查询成功',$data);
      }else{
        return rejson('0',' 查询失败');
      }

    }

    //个人中心--老师认证申请
    public function apply_teacher(){
      $uid=user_auth()['id'];
      $where['id']=['=',$uid];
      if(input('nickname')){$data['nickname']=input('nickname');}
      if(input('province')){$data['province']=input('province');}
      if(input('city')){$data['city']=input('city');}
      if(input('province_id')){$data['province_id']=input('province_id');}
      if(input('city_id')){$data['city_id']=input('city_id');}
      if(input('province_id')){$data['province_id']=input('province_id');}
      if(input('phone')){$data['phone']=input('phone');}
      if(input('school')){$data['school']=input('school');}
      if(input('grade')){$data['grade']=input('grade');}
      if(input('lesson')){$data['lesson']=input('lesson');}
      if(input('job')){$data['job']=input('job');}
      if(input('tags')){$data['tags']=input('tags');}
      if(input('worktype')){$data['worktype']=input('worktype');}



      $data['applyteacher']=3;//0：无申请 1：申请通过 2：申请不通过 3：申请中
      $re=update_one('user',$where,$data);
      if ($re) {
        return rejson('1','添加成功');
      }else{
        return rejson('0','添加失败');
      }
    }

    //个人信息--信息保存
    public function user_detail_update()
    {

      $uid=user_auth()['id'];

      $where['id']=['=',$uid];
      $field='usertype';
      $data=get_data('user',$where,$field);
      if(!$data){
        return rejson('0','无法查询出数据');
      }
      $NewData=array();
      switch ($data['usertype']) {
        case 0://0是家长
                    if(input('nickname')){$NewData['nickname']=input('nickname');}
                    if(input('province')){$NewData['province']=input('province');}
                    if(input('city')){$NewData['city']=input('city');}
                    if(input('grade')){$NewData['grade']=input('grade');}
                    if(input('phone')){$NewData['phone']=input('phone');}
                    if(input('school')){$NewData['school']=input('school');}
                    if(input('tags')){$NewData['tags']=input('tags');}
                    if(input('address')){$NewData['address']=input('address');}
                    if(input('headimg')){$NewData['headimg']=input('headimg');}
                    if(input('class')){$NewData['class']=input('class');}

                    if (!$NewData) {
                        return rejson('0','无新数据或无该字段');
                    }

            $re=update_one('user',$where,$NewData);
          break;
        
        case 1://1是老师
                    if(input('headimg')){$NewData['headimg']=input('headimg');}
                    if(input('nickname')){$NewData['nickname']=input('nickname');}
                    if(input('province')){$NewData['province']=input('province');}
                    if(input('city')){$NewData['city']=input('city');}
                    if(input('grade')){$NewData['grade']=input('grade');}
                    if(input('phone')){$NewData['phone']=input('phone');}
                    if(input('school')){$NewData['school']=input('school');}
                    if(input('tags')){$NewData['tags']=input('tags');}
                    if(input('lesson')){$NewData['lesson']=input('lesson');}
                    if(input('worktype')){$NewData['worktype']=input('worktype');}

                    if (!$NewData) {
                        return rejson('0','无新数据或无该字段');
                    }


            $re=update_one('user',$where,$NewData);
          break;

        case 2://2是顾问
                    if(input('headimg')){$NewData['headimg']=input('headimg');}
                    if(input('nickname')){$NewData['nickname']=input('nickname');}
                    if(input('full_name')){$NewData['full_name']=input('full_name');}
                    if(input('sex')){$NewData['sex']=input('sex');}
                    if(input('province')){$NewData['province']=input('province');}
                    if(input('city')){$NewData['city']=input('city');}
                    if(input('workyear')){$NewData['workyear']=input('workyear');}
                    if(input('phone')){$NewData['phone']=input('phone');}
                    if(input('tags')){$NewData['tags']=input('tags');}
                    if(input('servicecountry')){$NewData['servicecountry']=input('servicecountry');}
                    if(input('summary')){$NewData['summary']=input('summary');}
                    if (!$NewData) {
                        return rejson('0','无新数据或无该字段');
                    }

            //$field='nickname,fullname,sex,province,city,phone,workyear,tags,servicecountry,summary';
            $re=update_one('user',$where,$NewData);
          break;

        default:
          # code...
          break;
      }
      if ($re) {
        return rejson(1,'修改成功',$re);
      }else{
        return rejson(0,'无修改任何数据');
      }
    }




    //个人中心--(家长/老师/顾问)
    public function user_detail(){
        $uid=user_auth()['id'];

        $biaoshi=0;
        $where['id']=['=',$uid];
        $field='usertype';
        $data=get_data('user',$where,$field);
        if(!$data){
            return rejson('0','无法查询出数据');
        }
        switch ($data['usertype']) {
            case 0://0是家长
                    $field='vip,nickname,province,city,grade,phone,school,tags,address,usertype,headimg,address,applyteacher,worktype,class';
                break;
            
            case 1://1是老师
                    $field='nickname,province,city,phone,school,grade,lesson,worktype,tags,headimg,usertype';
                break;

            case 2://2是顾问


              $field='id,nickname,full_name,sex,province,city,phone,workyear,tags,servicecountry,summary,usertype,headimg,usertype,applyteacher,lesson,worktype';

              $biaoshi=1;

                break;

            default:
                # code...
                break;
        }
        $re=get_data('user',$where,$field);

        if ($re) {

          if($biaoshi){
              $where2['tbname']=['=','country'];
              $data2=get_data('system_conf',$where2);
              $num=$data2['tbnum'];
              $re['codeid']=jiami($num.'*');

              if(isset($re['servicecountry'])){
                if($re['servicecountry']){
                $arr=explode(',',$re['servicecountry']);
                foreach ($arr as $key => $value) {
                  $field='country';
                  $w['id']=['=',$value];
                  $d=get_data('country',$w,$field);

                  $country[]=$d['country'];
                  $countryid[]=$value;
                }
                $re['servicecountryid']=$countryid;
                $re['servicecountry']=$country;
              }else{
                $re['servicecountryid']=array();
                $re['servicecountry']=array();
              }
            }
          }

          // p($re);
           return rejson('1','成功',$re);
        }else{
          return rejson('0','失败');
        }
        
    }



    //个人中心--我的收藏
    public function user_college(){
       $uid=user_auth()['id'];
        $temp=array();
        $where=[];

        $curPage = input('curPage')?input('curPage'):1;
        $listnum = input('listnum')?input('listnum'):10;

        if(input('keyword')){
        $where['title|content'] = ['like','%'.input('keyword').'%'];
      }
      //('college',$where,$curPage,$listnum)
        $data=get_datalist('college');


        $remenids=get_child(44)?get_child(44):[44];
        $guandianids=get_child(43)?get_child(43):[43];
        $jiazhangids=get_child(46)?get_child(46):[46];
        $xueshengids=get_child(47)?get_child(47):[47];

        foreach ($data as $key => $value) {
         if (isset($value['collegeUser'])) {
         if($value['collegeUser']){
            $user=explode(',',$value['collegeUser']);
              foreach ($user as $k => $v) {
                  if ($v==$uid) {
                    $where['id']=['=',$value['articleid']];
                    if($d=get_data('module_news',$where)){
                      if(in_array($d['cateid'],$remenids)){$d['navtype']=1;}
                      else if(in_array($d['cateid'],$guandianids)){$d['navtype']=2;}
                      else if(in_array($d['cateid'],$jiazhangids)||in_array($d['cateid'],$xueshengids))
                        {$d['navtype']=3;}
                      else{$d['navtype']=0;}
                      $temp[]=$d;
                    }
                  }
              }
          }
        }
      }



      if(!$temp){return rejson('0','无数据');}
      $result=fenye($temp,$listnum);
            
      if ($curPage>ceil(count($temp)/$listnum)) {
        return rejson('0','该页无数据,页数超出范围');
      }

      $re['pages']=ceil(count($temp)/$listnum);
      $re['datalist']=set_codeid($result[$curPage-1],28);
      $re['total']=count($temp);

        if ($re) {
            return rejson('1','成功',$re);
        }else{
            return rejson('1','无收藏');
        }
    }


    //删除收藏
    public function delete_college(){
      $uid=user_auth()['id'];
      if(input('id')){$articleid=input('id');}

      $where['articleid']=['=',$articleid];
      $data=get_data('college',$where);

      $arr=explode(',',$data['collegeUser']);
      foreach ($arr as $key => &$value) {
          if ($value==$uid) {
            unset($arr[$key]);
          }
      }
      $data['collegeUser']=implode(',',$arr);

      $re=update_one('college',$where,$data);
      if ($re) {
        return rejson('1','删除成功');
      }else{
        return rejson('0','删除失败');
      }
    }

    //个人中心--大V申请
    public function user_apply(){

        $uid=user_auth()['id'];
        $where['id']=['=',$uid];
        $data['vip']=2;
        $result=update_one('user',$where,$data);

        if ($result) {
            return rejson('1','成功',$result);
        }else{
            return rejson('0','更新失败或已经是申请状态');
        }
    }    

 
 
    //调查问卷
    //传递参数[{id:1,answer:A、aa,type:1},{id:2,answer:A、aa,type:2}],id
    public function add_question(){

      $uid=user_auth()['id'];
      $t=0;
      $userdata=array();

      if(input('grade')){$userdata['grade']=input('grade');}
      if(input('school')){$userdata['school']=input('school');}
      if(input('province')){$userdata['province']=input('province');}
      if(input('city_name')){$userdata['city_name']=input('city_name');}

      if(input('id')){$qid=input('id');}
      if(input('data/a')){$data=input('data/a');}
      // $data[0]['id']=1;
      // $data[0]['answer']=['111','222'];
      // $data[0]['type']=1;
      // $data[1]['id']=2;
      // $data[1]['answer']=['1'=>'111','2'=>'222'];
      // $data[1]['type']=2;
      // $data[2]['id']=3;
      // $data[2]['answer']=['1'=>'111','2'=>'222'];
      // $data[2]['type']=3;
      foreach ($data as $key => $value) {

        $NewData['uid']=$uid;
        $NewData['qid']=$qid;
        $NewData['tid']=$value['id'];
        $NewData['type']=$value['type'];

        if(isset($value['answer'])){
          if($value['answer']){
            if(is_array($value['answer'])){
              $NewData['answer']=implode(',',$value['answer']);
            }
            else{
              $NewData['answer']=$value['answer'];
            }
          }
        }
        $temp[]=$NewData;
      }

// p($data);

      //用户参与的问卷情况
      $Qdata['qid']=$qid;
      $Qdata['uid']=$uid;
      $Qdata['createtime']=time();
      

      
      $where['id']=['=',$qid];
      $QUdata=get_data('module_question',$where);

       if ($QUdata['takeuser']) {
          $arr=explode(',',$QUdata['takeuser']);
          
          foreach ($arr as $key => $value) {
            if ($value==$uid) {
              $t=1;//表示用户已经参与过
            }else{
              $QUdata['takeuser']=$QUdata['takeuser'].','.$uid;
              $QUdata['takenum']=count($arr)+1;
            }
          }
        }else{
          $QUdata['takeuser']=$uid;
          $QUdata['takenum']=1;
          }
          
     

      if ($t==1) {return rejson('0','用户已参与过该调查问卷');}
      
      //用户信息
      $uwhere['id']=['=',$uid];

      if($userdata){
        $u=update_one('user',$uwhere,$userdata);
        if(!$u)return rejson('0','user数据更新失败');
      }

      //用户的调查问卷数据
      if($temp){
        foreach ($temp as $key => $value) {
         $r=insert_data('module_question_answer',$value);
         if(!$r)return rejson('0','module_question_answer数据插入失败');
        }
      }
      
      //用户参与的问卷情况
      if($Qdata){
        $re=insert_data('module_question_info',$Qdata);
        if(!$re)return rejson('0','module_question_info数据插入失败');
      }

      //记录问卷的参与用户
      if($QUdata){
        $rea=update_one('module_question',$where,$QUdata);     
        if(!$rea)return rejson('0','module_question数据更新失败');
      }

      return rejson('1','添加成功');
    }



    //获取调查问卷详情页
    public function question_list_detail(){
      if(!input('id')){return rejson('0','id未设置');}
      $qid=input('id');
      $uid=user_auth()['id'];
      $temp=array();
      $status=0;
      $data=Db::name('module_question_info info,ys_module_question q')->
      field('q.id qid,info.uid uid,info.createtime,q.title,q.summary,q.takeuser,q.status')->
      where('info.qid=q.id')->where('uid='.$uid)->where('qid='.$qid)->select();


      if(!$data){//未参与，返回问卷内容
        $datatitle=DB::name('module_question')->where('id='.$qid)->select();
        $dataT=Db::name('module_question q,ys_module_question_title t')->
        where('q.id=t.qid')->where('q.id='.$qid)->field('t.title,t.type,t.option,q.status,t.listorder,t.id tid,t.ismust')->order('t.listorder')->select();
        $temp=Db::name('module_question ')->where('id='.$qid)->field('title,summary')->select();
  
        if($dataT)
        $status=$dataT[0]['status'];

        foreach ($dataT as $key => &$value) {
          $arr=explode(',',$value['option']);
          $value['option']=$arr;
          unset($value['status']);
          $value['answer']=[];
          }
          $title=$datatitle;
// p($dataT);array_merge($temp,
        if (array_merge($temp,$dataT)) {
          return json(['code'=>'1','msg'=>'查询成功','status'=>$status,'title'=>$title,'data'=>$dataT]);
        }else{
          return rejson('0','查询失败或该问卷无内容');
        } 
      }else{

        $dataT=Db::name('module_question_answer a,ys_module_question_title t')->where('a.tid=t.id')->
        field('a.tid tid,t.type,a.uid uid,a.answer,t.option,t.ismust,a.isdone,t.content,t.listorder')->order('t.listorder')->where('uid='.$uid)->select();

        $status=$data[0]['status'];

        foreach ($data as $key => $value) {
          unset($status);
          if(isset($value['takeuser'])){
            if($value['takeuser']){
              $arr=explode(',',$value['takeuser']);
              foreach ($arr as $k => $v) {
                if($v==$uid)
                  $data['isdone']=1;
              }
            }
          }
        }


        foreach ($dataT as $key => &$value) {
          $arr=explode(',',$value['option']);
          $value['option']=$arr;

          $value['answer']=explode(',',$value['answer']);

        }
      }
      $title[]=$data[0];

      if($data&&$dataT){
        return json(['code'=>'1','msg'=>'查询成功','isdone'=>$data['isdone'],'status'=>$data[0]['status'],'data'=>$dataT,'title'=>$title]);
      }else{
        return rejson('0','获取失败');
      }
    }
    

    //获取调查问卷列表
    public function question_list(){
      $where=[];
      $curPage = input('curPage')?input('curPage'):1;
      $listnum = input('listnum')?input('listnum'):10;

      if(input('keyword')){
        $where['title'] = ['like','%'.input('keyword').'%'];
      }

      $uid=user_auth()['id'];
      $istake=1;//1是已参与，2是未参与
      if(input('istake')){$istake=input('istake');}
      $yes=array();//已参与问卷列表
      $no=array();//未参与问卷列表
      $data=get_datalist('module_question',$where);


      foreach ($data as $key => &$value) {
        $t=0;
        $value['createtime']=date('n月d日',$value['createtime']);
        $arr=explode(',',$value['takeuser']);
        foreach ($arr as $k => $v) {
          if($v==$uid)
            {$t=1;}
        }

        if($t==1){$yes[]=$value;}
        else{$no[]=$value;}

      }
      


    if ($istake==1) {//已参与问卷列表
        if(!$yes){return rejson('0','无数据');}

        $result=fenye($yes,$listnum);
            
      if ($curPage>ceil(count($yes)/$listnum)) {
        return rejson('0','该页无数据,页数超出范围');
      }

      $re['pages']=ceil(count($yes)/$listnum);
      $re['datalist']=$result[$curPage-1];
      $re['total']=count($yes);
      if($re)
        {
          return rejson('1','查询已参与问卷列表成功',$re);
        }else{
          return rejson('0','没有已参与问卷列表');
        }
    }else {//未参与问卷列表
      if(!$no){return rejson('0','无数据');}
      $result=fenye($no,$listnum);
            
      if ($curPage>ceil(count($no)/$listnum)) {
        return rejson('0','该页无数据,页数超出范围');
      }

      $re['pages']=ceil(count($no)/$listnum);
      $re['datalist']=$result[$curPage-1];
      $re['total']=count($no);

      if($re)
        {return rejson('1','查询未参与问卷列表成功',$re);}
      else{return rejson('0','没有未参与问卷列表');}
    }

  }




    //获取调查问卷内容页
    public function question_content(){

      if (!input('id')) {return rejson('0','id未定义');}
        $id=input('id');
      
      $data=Db::name('module_question q,ys_module_question_title t')->
      where('q.id=t.qid')->where('q.id='.$id)->field('t.title,t.type,t.option,q.status,t.listorder')->order('t.listorder')->select();
      $temp=Db::name('module_question ')->where('id='.$id)->field('title,summary')->select();
     
     if($data)
     $status=$data[0]['status'];

      foreach ($data as $key => &$value) {
        $arr=explode(',',$value['option']);
        $value['option']=$arr;
        unset($value['status']);
      } 
      if (array_merge($temp,$data)) {
        return json(['code'=>'1','msg'=>'查询成功','status'=>$status,'data'=>array_merge($temp,$data)]);
      }else{
        return rejson('0','查询失败或该问卷无内容');
      }
      
    }

    //调查问卷删除
    public function delete_question(){
       $uid=user_auth()['id'];
       if(input('id')){$qid=input('id');}


       $w['id']=['=',$qid];
       $d=get_data('module_question',$w);

       $arr=explode(',',$d['takeuser']);
       foreach ($arr as $key => &$value) {
         if ($value==$uid) {
            unset($arr[$key]);
         }
       }
       $d['takenum']=count($arr);
       $d['takeuser']=implode(',',$arr);

       $re=update_one('module_question',$w,$d);

       if(!$re)return rejson(0,'module_question更新失败');


       $data['isdel']=1;
       $where['qid']=['=',$qid];
       $where['uid']=['=',$uid];
       $re=update_one('module_question_answer',$where,$data);
       if(!$re){return rejson('0','module_question_answer更新失败');}

       $re=update_one('module_question_info',$where,$data);
       if(!$re){return rejson('0','module_question_info更新失败');}

       return rejson('1','删除成功');
    }

    //个人中心--我要推荐
    public function add_recommend(){
      $uid=user_auth()['id'];
      $field='nickname';
      $where['id']=['=',$uid];
      $userinfo=get_data('user',$where,$field);

//看有没有试听券，没有就不给插入；  拿出最前面的试听券
		$wt['uid'] = ['=',$uid]; 
		$wt['isapply'] = ['=','0'];
		$stq = get_data('volume',$wt);

if(input('is_apply')==1){  
		if($stq){
			//去更新
			$w['id'] = $stq['id'];

			$content = [
			'isapply'=>1,
			'studentname'=>input('studentname'),
			'studentage'=>input('studentage'), 
			'teacher'=>input('teacher'),
			'teacherid'=>input('teacherid'),
			'lesson'=>input('lesson'),
			'lessonid'=>input('lessonid'),
			'lesson'=>input('lesson'), 
			'timestr'=>input('timestr'), 
			"usetime"=>time()
			];  
			update_one('volume',$w,$content);
		}else{
			return rejson('0','您没有更多的试听券，请重新申请');
		}

}


      if($userinfo){
        $data['recommender']=$userinfo['nickname'];
        $data['uid']=$uid;
      }
      if(input('studentname')){$data['studentname']=input('studentname');}
      if(input('studentage')){$data['studentage']=input('studentage');}
      if(input('country')){$data['country']=input('country');}
      if(input('hopelesson')){$data['hopelesson']=input('hopelesson');}

      if(input('contact')){$data['contact']=input('contact');}
      if(input('needs')){$data['needs']=input('needs');}
       $data['is_apply']=input('is_apply'); 
      if(input('teacher')){$data['teacher']=input('teacher');}
      if(input('teacherid')){$data['teacherid']=input('teacherid');}
      if(input('lesson')){$data['lesson']=input('lesson');}
      if(input('timestr')){$data['timestr']=input('timestr');}
      $data['createtime']=time();

      $re=insert_data('recommend',$data);
















      if($re){
        return rejson('1','成功',$data);
      }else{
        return rejson('0','失败');
      }
    }








//公共方法
/** 
 *宇杉ajax示例方法——增  增c、删d、改u、查r 
 * @return   arr
 * @author  wyl <181984609@qq.com>
 */
public function insert_data()
{
    $p = input('post.');
    if(count($p)==1){return rejson(0,'参数不齐全');} 
    $p['createtime']=time();
    $sql=Db::name('audition')->select();
    foreach ($sql as $key => $value) {
      if($value['telephone']==$p['telephone']&&$value['isdel']==0){
        return rejson(0,'该手机号已注册，请勿重复提交');
      }
    }
    $verifycode = session('phcode');
    $aarr = explode('+',$verifycode);
    if($p['telephone']==$aarr[0]&&$p['verificate']!=$aarr[1]){
        return rejson(0,'请输入正确的验证码');
    };
    $p['username'] = generateusername();
    $content = '您的报名资料已收到，正在审核中，将在三日内以短信的形式通知您！赛事详询：0755-22956651。';
    $rearr=getphcode($p['telephone'],$content);
    //return rejson(0,$rearr['msg']);
    $re = insert_data('audition',$p); 
    if($re){
      return rejson(1,$re); 
    }else{
      return rejson(0,'提交失败！'); 
    } 
}


//发送短信验证码
/** 
 *宇杉ajax示例方法——增  增c、删d、改u、查r 
 * @return   arr
 * @author  wyl <181984609@qq.com>
 */
public function send_verify()
{
    $p = input('post.');
    $sql=Db::name('audition')->select();
    foreach ($sql as $key => $value) {
      if($value['telephone']==$p['telephone']&&$value['isdel']==0){
        return rejson(0,'该号码已注册');
      }
    }
    $content = 'verificate';
    $rearr=getphcode($p['telephone'],$content);
    if($rearr['code']>0){
      return rejson(1,'发送验证码成功'); 
    }else{
      return rejson(0,'发送验证码失败'); 
    } 
}





protected $User;     //微信用户对象 
    protected $appid; 
    protected $appsecret;
    /**
     * 获取全局的access_token方法
     * @return [type] [description]
     */
    public function getAccessToken(){
      $field = 'access_token,modify_time';
      $condition = array('token'=>TOKEN,'appid'=>Appid,'appsecret'=>AppSecret);
      // $data = M('wechat')->field($field)->where($condition)->find();
      // if($data['access_token'] && time()-$data['modify_time']<7000){
      //  $access_token = $data['access_token'];
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


public function index(){
    $signPackage = get_signature();//自定义分享方法,获取签名值数组 
   
    return $signPackage;
}
public function mobile_index(){
  $view = new View();
    $signPackage = get_signature();//自定义分享方法,获取签名值数组 
 
    $news = array("Title" =>"第十三届中国超级模特大赛", "Description"=>"第十三届中国超级模特大赛深圳赛区赛事介绍", "PicUrl" =>'http://www.hnk123.cn/img/hnklogo.jpg', "Url" =>'http://www.hnk123.cn/api.php/auth/mobile_index.html');
   $view->assign('signPackage',$signPackage);
   $view->assign('news',$news);
        return $view->fetch('mobile_index');}
public function mobile_indexb(){
  $view = new View();
    $signPackage = get_signature();//自定义分享方法,获取签名值数组 
 
    $news = array("Title" =>"第十三届中国超级模特大赛", "Description"=>"第十三届中国超级模特大赛深圳赛区赛事介绍", "PicUrl" =>'http://www.hnk123.cn/img/hnklogo.jpg', "Url" =>'http://www.hnk123.cn/api.php/auth/mobile_index.html');
   $view->assign('signPackage',$signPackage);
   $view->assign('news',$news);
        return $view->fetch('mobile_indexb');}
public function registration(){
  $view = new View();
    $signPackage = get_signature();//自定义分享方法,获取签名值数组 
 
    $news = array("Title" =>"第十三届中国超级模特大赛", "Description"=>"第十三届中国超级模特大赛深圳赛区报名", "PicUrl" =>'http://www.hnk123.cn/img/hnklogo.jpg', "Url" =>'http://www.hnk123.cn/api.php/auth/registration.html');
   $view->assign('signPackage',$signPackage);
   $view->assign('news',$news);
        return $view->fetch('registration');}

































































}
