
// $(function(){
// 	$(".top_l li").hover(function(){
// 	var $index=$(this).index(); 
// 	$(".show").eq($index).fadeIn().siblings().hide();
//     $(this).addClass("active").siblings().removeClass("active");
	

// },function(){

// })
// $(".top_l li").click(function(){
// 	var $index=$(this).index(); 
// 	$(".show").eq($index).fadeIn().siblings().hide();
//     $(this).addClass("active").siblings().removeClass("active");
// })

// })
window.onlyNumber = function(obj){
  //得到第一个字符是否为负号
  var t = obj.value.charAt(0); 
  //先把非数字的都替换掉，除了数字和. 
  obj.value = obj.value.replace(/[^\d\.]/g,''); 
  //必须保证第一个为数字而不是. 
  obj.value = obj.value.replace(/^\./g,''); 
  //保证只有出现一个.而没有多个. 
  obj.value = obj.value.replace(/\.{2,}/g,'.'); 
  //保证.只出现一次，而不能出现两次以上 
  obj.value = obj.value.replace('.','$#$').replace(/\./g,'').replace('$#$','.');
  //如果第一位是负号，则允许添加
  if(t == '-'){
  obj.value = '-'+obj.value;
  }
}

$(function(){
	// $("#ed").click(function(){
	// 	$('.edcount_lc').css("display","block");
	// 	$(".mjcount_lc").css("display","none")
	// })
	// $("#mj").click(function(){
	// 	$('.edcount_lc').css("display","none");
	// 	$(".mjcount_lc").css("display","block")
	// })
	// $("#ed_2").click(function(){
	// 	$('.edcount_lc').css("display","none");
	// 	$(".mjcount_lc").css("display","block")
	// })
	// $("#mj_2").click(function(){
	// 	$('.edcount_lc').css("display","block");
	// 	$(".mjcount_lc").css("display","none")
	// })
	// $("#ed_3").click(function(){
	// 	$('.edcount_lc').css("display","none");
	// 	$(".mjcount_lc").css("display","block")
	// })
	// $("#mj_3").click(function(){
	// 	$('.edcount_lc').css("display","block");
	// 	$(".mjcount_lc").css("display","none")
	// })
	// $("#ed_4").click(function(){
	// 	$('.edcount_lc').css("display","none");
	// 	$(".mjcount_lc").css("display","block")
	// })
	// $("#mj_4").click(function(){
	// 	$('.edcount_lc').css("display","block");
	// 	$(".mjcount_lc").css("display","none")
	// })
	
	// $("#sq_sh").click(function(){
	// 	$('.edcount_lc').css("display","block");
	// 	$(".mjcount_lc").css("display","none")
	// })
	// $("#sh_sq").click(function(){
	// 	$('.edcount_lc').css("display","none");
	// 	$(".mjcount_lc").css("display","block")
	// })

	// tab移动切换页面
	$(".top_l li").click(function() {
		var $index=$(this).index();
			var $index=$(this).index(); 
	    $(".show_lc").eq($index).fadeIn().siblings().hide();
		$(this).addClass("active_lc").siblings().removeClass("active_lc");
    var mok = $(".show_lc").eq($index);
    setbenchmark(mok);
		 setrate();
	});


//a链接效果
$('.setactive').on('click',function(){
   $(this).addClass('active').siblings().removeClass('active');
});

 

//navtab切换 
 $('.litnavbox .litnavtitle').on('mousemove',function(){
  var index = $(this).index();
  var pr = $(this).parents('');
  pr.find('.litnavitem').removeClass('active');
  pr.find('.litnavitem').eq(index).addClass('active');  
        });







 $("#goTopBtn").click(function(){
   var sc=$(window).scrollTop();
   $('body,html').animate({scrollTop:0},500);
 })







//tab 切换
 $('.tabnav li').on('mousemove',function(){
    var _this = this;
    // $(_this).siblings().removeClass('active');
    // setTimeout(function(){   },500);
            // $(_this).addClass('active'); 
            $('.index-banner').addClass('active');
 
            
        });

 $('.tabnav li').on('mouseout',function(){
            $(this).removeClass('active'); 
             // $('.index-banner').removeClass('active');
        });
 $('.tabnav').on('mouseleave',function(){ 
        $('.index-banner').removeClass('active');
        });


       $(document).scroll(function() {
            if($(document).scrollTop()>50){
                $('#changetopnav').addClass('otherclass');
                $('.flowfoot').addClass('footnav');
                $('.flowlip').addClass('active');
                $('.celan').addClass('active');
            }else{
                $('#changetopnav').removeClass('otherclass');
                 $('.flowfoot').removeClass('footnav');
                 $('.flowlip').removeClass('active');
                 $('.celan').removeClass('active');
            }
        });


$('.footclose').on('click',function(){
   $('.flowfoot').addClass('nofootnav');
   $('.flowlip').addClass('activeno');
});





        var swiper = new Swiper('.swiper-container', {
        pagination: '.swiper-pagination',
        paginationClickable: true,
        nextButton: '.swiper-button-next',
        prevButton: '.swiper-button-prev',
        autoplay : 5000,
        loop : true,
    }); 


//城市选择
    $("#city").click(function (e) {
                      SelCity(this,e);
                        console.log("inout",$(this).val(),new Date())
                    });


//动画效果
 function testAnim(x) { 
    $('#animationSandbox').removeClass().addClass(x + ' animated').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){ 
      $(this).removeClass(); 
    });
}

  $('.maani').on('mouseover',function(e){ 
      e.preventDefault();  
      testAnim('bounce'); 
    });
  

$('.hidebutton').on('click',function(){ 
      $('.flowfoot').removeClass('acitve');  
    });


//首页左侧 右侧内容 tab切换 
 $('.tabtitle').on('click',function(){
            var index = $(this).index();
            $(this).addClass('active').siblings().removeClass('active'); 
            var $parent = $(this).parents('.tabbox');
            $parent.find('.tabitem').removeClass('active');
            $parent.find('.tabitem').eq(index).addClass('active');
            
        })









//数字自动增长
function NumberGrow(element, options) {
    options = options || {};
 
    var $this = $(element),
        time = options.time || $this.data('time'),//总时间
        num = options.num || $this.data('value'),//要显示的真实数值
        step = num * 16 / (time * 1000),//每16ms增加的数值
        start = 0,//计数器
        interval,//定时器
        old = 0;
 
    //每帧不能超过16ms，所以理想的interval间隔为16ms
    //step为每16ms增加的数值
 
    interval = setInterval(function () {
        start = start + step;
        if (start >= num) {
            clearInterval(interval);
            interval = undefined;
            start = num;
        }
 
        var t = Math.floor(start);
 
        //t未发生改变的话就直接返回
        //避免调用text函数，提高DOM性能
        if (t == old) {
            return;
        }
 
        old = t;
        $this.text(old);
    }, 16);
}

    $('[data-ride="numberGrow"]').each(function () {
       
            new NumberGrow(this);
       
    });






























///////////////////////////////////////////////////////////////////////////////////////////////





//基准利率表
// 1、房贷5年 4.75   房贷5以上 4.9   fangdai
// 2、公积5年 2.75   公积5以上 3.25  gongji
// 3、组合2.75、3.25、4.75、4.9      zuhe
// 4、本息4.9                        benxi
// 5、本金4.9                        benjin
// 6、工资
 

//默认为房贷基准利率；
window.benchmark=4.75;//最新基准利率
window.tempbenchmark = 4.9; //备用基准利率

//重置期限时候，重置基准利率 
$('.qixian').on('change',function(){ 
    var mokuaibox = $(this).parents('.mokuaibox'); 
    setbenchmark(mokuaibox);
    setrate();
});


function setbenchmark(mokuaibox){
  var years = mokuaibox.find('.qixian').val();
   if(mokuaibox.attr('ktype')=='fangdai'){
        window.benchmark = years==5?4.75:4.9;
    }
    if(mokuaibox.attr('ktype')=='gongji'){
        window.benchmark = years==5?2.75:3.25;
    }
    if(mokuaibox.attr('ktype')=='zuhe'){
        window.benchmark = years==5?2.75:3.25;
        window.tempbenchmark = years==5?4.75:4.9;
    }
    if(mokuaibox.attr('ktype')=='benxi'){
        window.benchmark = 4.9;
    }
    if(mokuaibox.attr('ktype')=='benjin'){
        window.benchmark = 4.9;
    }

    console.log(benchmark);
     console.log(tempbenchmark);
}
 
//初始化计算结果
function initialization(){
  $(".y_yg1").html("");
  $(".y_lx1").html("");
  $(".y_bx1").html("");
  $(".y_yg2").html("");
  $(".y_ygj").html("0");
  $(".y_lx2").html("");
  $(".y_bx2").html("");
  $(".y_ys1").html("");
  $(".y_ys2").html("");
  $(".y_sum_x").html("");
  
}

//初始化利率
function setrate(){
  $('.lilvcheck').each(function(index,el){
    var mk = $(el).parents('.mokuaibox');
    var val = $(el).val()
    mk.find('.lilv').val(toThousands(parseFloat((benchmark*val).toFixed(4)))); 
 

  });


    $('.lilvcheckzuhe1').each(function(index,el){
    var mk = $(el).parents('.mokuaibox');
    var val = $(el).val(); 
mk.find('.lilvzuhe1').val(toThousands(parseFloat((benchmark*val).toFixed(4)))); 
  });


    $('.lilvcheckzuhe2').each(function(index,el){
    var mk = $(el).parents('.mokuaibox');
    var val = $(el).val(); 
mk.find('.lilvzuhe2').val(toThousands(parseFloat((tempbenchmark*val).toFixed(4)))); 
  });


}

setrate();

//获取首付比例 
$('.xingzhi').on('change',function(){
    var mokuaibox = $(this).parents('.mokuaibox'); 
   if(mokuaibox.find(".xingzhi").val()==1)mokuaibox.find(".shoufu").val("0.3");
   if(mokuaibox.find(".xingzhi").val()==2)mokuaibox.find(".shoufu").val("0.6");
});
 

  //贷款计算
function count(mokuaibox){
//设置基准利率
setbenchmark(mokuaibox);

//方式0，按额度，1安面积
var fangshi = mokuaibox.find('.jstype').find('input:radio:checked').val();
var monney = 0;
if(fangshi==0){
monney = mokuaibox.find('.daikuanjine').val()*10000; 
}else{
 monney =mokuaibox.find(".pingfang").val()*mokuaibox.find(".mianji").val()*(1-mokuaibox.find(".shoufu").val());//贷款金额

}


//面积
// var pingfang = mokuaibox.find('.pingfang').val();  
// var mianji = mokuaibox.find('.mianji').val();  
// var xingzhi = mokuaibox.find('.xingzhi').val();  
// var shoufu = mokuaibox.find('.shoufu').val();  
var qixian = mokuaibox.find('.qixian').val();  
  var lilv = mokuaibox.find('.lilv').val(); 







  var month=qixian*12;//贷款月数
  var monthlyRate=0;//月利率
  
  if(lilv>0)monthlyRate=lilv/12/100;
  // else if($("#y_rated").val()>0)monthlyRate=benchmark*$("#y_rated").val()/12/100;
  if(!(monneyCount()>0)){initialization();return};
  monthCount();
  mokuaibox.find(".y_yg1").html(toThousands(parseFloat(monneyCount().toFixed(2))));
  mokuaibox.find(".y_lx1").html(toThousands(parseFloat((monneyCount()*month-monney).toFixed(2))));
  mokuaibox.find(".y_bx1").html(toThousands(parseFloat((monneyCount()*month).toFixed(2))));
              
  mokuaibox.find(".y_yg2").html(toThousands(parseFloat(monneyFirstMonth().toFixed(2))));
  mokuaibox.find(".y_ygj").html(toThousands(parseFloat(decline().toFixed(2))));
  mokuaibox.find(".y_lx2").html(toThousands(parseFloat((monneyTotal()-monney).toFixed(2))));
  mokuaibox.find(".y_bx2").html(toThousands(parseFloat((monneyTotal()).toFixed(2))));

  mokuaibox.find(".y_sum_x").html(toThousands(parseFloat((monney).toFixed(2))));
  
  
  //贷款月数计算
  function monthCount(){
    mokuaibox.find(".y_ys1").html(month);
    mokuaibox.find(".y_ys2").html(month);
  }
  
  //等额本息还款_计算月供
  function monneyCount(){
    return monney*monthlyRate*Math.pow(monthlyRate+1,month)/(Math.pow(monthlyRate+1,month)-1);
  }
  //等额本息还款_计算月供
  // function monneyCount(){
  //   return monney*monthlyRate*Math.pow(monthlyRate+1,month)/(Math.pow(monthlyRate+1,month)-1);
  // }
  
  
  //等额本金还款_首月还款额
  function monneyFirstMonth(){
    return monney/month+monney*monthlyRate;
  }
  //等额本金还款_递减金额
  function decline(){
    return monney/month*monthlyRate;
  }
  //等额本金还款_总还款额
  function monneyTotal(){
    return ((monney/month+monney*monthlyRate)+monney/month*(1+monthlyRate))/2*month;
  }
  
  
}



//千分位格式化
function toThousands(num) {
  if((num+"").indexOf("\.")>0)
  return (num + '').substring(0,(num+"").indexOf("\.")).replace(/\d{1,3}(?=(\d{3})+(\.\d*)?$)/g, '$&,')+(num + '').substring((num+"").indexOf("\."));
  else return (num + '').replace(/\d{1,3}(?=(\d{3})+(\.\d*)?$)/g, '$&,');
}




$(".ed").click(function(){
  var mokuaibox = $(this).parents('.mokuaibox'); 
   mokuaibox.find('.edfangshi').removeClass('dpn');
   mokuaibox.find(".mjfangshi").addClass('dpn');
   initialization()
  });

  $(".mj").click(function(){
    var mokuaibox = $(this).parents('.mokuaibox'); 
    mokuaibox.find('.mjfangshi').removeClass('dpn');
   mokuaibox.find(".edfangshi").addClass('dpn');
   initialization()
  })


$(".lilvcheck").change(function(){
  var val = $(this).val();
  var kg = $(this).parents('.kuang_lc'); 
  kg.find('.lilv').val(toThousands(parseFloat((benchmark*val).toFixed(4)))); 
  });


// 贷款 计算器相关
$('.jisuan').on('click',function(){
    var mokuaibox = $(this).parents('.mokuaibox'); 
count(mokuaibox); 
});



// 组合贷款 计算器相关
$('.jisuanzuhe').on('click',function(){
    var mokuaibox = $(this).parents('.mokuaibox'); 
countzuhe(mokuaibox); 
});
















$(".lilvcheckzuhe1").change(function(){
  var val = $(this).val();
  var kg = $(this).parents('.kuang_lc'); 
  kg.find('.lilvzuhe1').val(toThousands(parseFloat((benchmark*val).toFixed(4)))); 
  });


$(".lilvcheckzuhe2").change(function(){
  var val = $(this).val();
  var kg = $(this).parents('.kuang_lc'); 
  kg.find('.lilvzuhe2').val(toThousands(parseFloat((tempbenchmark*val).toFixed(4)))); 
  });


  //贷款计算
function countzuhe(mokuaibox){
//设置基准利率
setbenchmark(mokuaibox);
 
var monney1 = 0;
var monney2 = 0;
// if(fangshi==0){}else{
//  monney =mokuaibox.find(".pingfang").val()*mokuaibox.find(".mianji").val()*(1-mokuaibox.find(".shoufu").val());//贷款金额

// }
monney =mokuaibox.find('.sydaikuanjine').val()*10000 ; 
monney1 = mokuaibox.find('.daikuanjine').val()*10000 ; 



//面积
// var pingfang = mokuaibox.find('.pingfang').val();  
// var mianji = mokuaibox.find('.mianji').val();  
// var xingzhi = mokuaibox.find('.xingzhi').val();  
// var shoufu = mokuaibox.find('.shoufu').val();  
var qixian = mokuaibox.find('.qixian').val();  
  var lilv1 = mokuaibox.find('.lilvzuhe1').val()/12/100; ;//公积金贷款月利率
  var lilv2 = mokuaibox.find('.lilvzuhe2').val()/12/100; ;//商业贷款月利率






  var month=qixian*12;//贷款月数
  var monthlyRate=lilv1;//月利率
  var monthlyRate1=lilv2;//月利率
  if(!(monney>0&&monney1>0&&month>0&&monthlyRate>0&&monthlyRate1>0)){initialization();return;}
 
  monthCount();
  mokuaibox.find(".y_yg1").html(toThousands(parseFloat(monneyCount().toFixed(2))));
  mokuaibox.find(".y_lx1").html(toThousands(parseFloat((monneyCount()*month-monney).toFixed(2))));
  mokuaibox.find(".y_bx1").html(toThousands(parseFloat((monneyCount()*month).toFixed(2))));
              
  mokuaibox.find(".y_yg2").html(toThousands(parseFloat(monneyFirstMonth().toFixed(2))));
  mokuaibox.find(".y_ygj").html(toThousands(parseFloat(decline().toFixed(2))));
  mokuaibox.find(".y_lx2").html(toThousands(parseFloat((monneyTotal()-monney).toFixed(2))));
  mokuaibox.find(".y_bx2").html(toThousands(parseFloat((monneyTotal()).toFixed(2))));

  mokuaibox.find(".y_sum_x").html(toThousands(parseFloat((monney).toFixed(2))));
  
  
  //贷款月数计算
  function monthCount(){
    mokuaibox.find(".y_ys1").html(month);
    mokuaibox.find(".y_ys2").html(month);
  }
  
  //等额本息还款_计算月供
  function monneyCount(){
    return monney*monthlyRate*Math.pow(monthlyRate+1,month)/(Math.pow(monthlyRate+1,month)-1);
  }
 

  //贷款月数计算
  function monthCount(){
   mokuaibox.find(".y_ys1").html(month);
    mokuaibox.find(".y_ys2").html(month);
  }
  
  //等额本息还款_计算月供
  function monneyCount(){
    return  monney*monthlyRate*Math.pow(monthlyRate+1,month)/(Math.pow(monthlyRate+1,month)-1)   +  monney1*monthlyRate1*Math.pow(monthlyRate1+1,month)/(Math.pow(monthlyRate1+1,month)-1);;
  }
  
  
  
  //等额本金还款_首月还款额
  function monneyFirstMonth(){
    return monney/month+monney*monthlyRate   +   monney1/month+monney1*monthlyRate1 ;
  }
  //等额本金还款_递减金额
  function decline(){
    return monney/month*monthlyRate   +   monney1/month*monthlyRate1;
  }
  //等额本金还款_总还款额
  function monneyTotal(){
    return ((monney/month+monney*monthlyRate)+monney/month*(1+monthlyRate))/2*month   +   ((monney1/month+monney1*monthlyRate1)+monney1/month*(1+monthlyRate1))/2*month;
  }







  
  
}



// 全月应纳税额不超过1500元 3% 0
// 全月应纳税额超过1500元至4500元 10% 105
// 全月应纳税额超过4500元至9000元 20% 555
// 全月应纳税额超过9000元至35000元 25% 1005
// 全月应纳税额超过35000元至55000元 30% 2755
// 全月应纳税额超过55000元至80000元 35% 5505
// 全月应纳税额超过80000元 45% 13505
// 税前   工资计算税额
function shuie(money,shebao,jijin,qidian){ 
 var se = money - shebao -jijin - qidian;
 var rate = 0;
 var fei = 0;
 if(se<=1500){ rate = 0.03;fei=0}
 if(se>1500&&se<=4500){ rate = 0.10;fei=105}
 if(se>4500&&se<=9000){ rate = 0.20;fei=555}
 if(se>9000&&se<=35000){ rate = 0.25;fei=1005}
 if(se>35000&&se<=55000){ rate = 0.30;fei=2755}
 if(se>55000&&se<=80000){ rate = 0.35;fei=5505}
 if(se>80000){ rate = 0.45;fei=13505} 
 var shui = se * rate - fei;
 return {shui:shui,rate:rate};
}



 

//个人所得税起征点
var qizhengdian = 3500;


//社保额度
var shebaolv  = 0.08+0.02+0.005;

//社保基数算社保额
function shebaoedu(sbjishu){
return sbjishu * shebaolv;
}





// 月薪计算器计算器相关
$('.jisuanyuexin').on('click',function(){
    var mokuaibox = $(this).parents('.mokuaibox'); 
    //方式0，按额度，1安面积
var fangshi = mokuaibox.find('.jstype').find('input:radio:checked').val();
var yuexin = mokuaibox.find('.yuexin').val();

var sbjishu = mokuaibox.find('.sbjishu').val(); 
var shebao = shebaoedu(sbjishu);
var jijin = mokuaibox.find('.jijin').val();
var bili = mokuaibox.find('.bili').val(); 
var jijinzongshu = jijin*bili;


var monney = 0;
if(fangshi==0){ 
  var shui = shuie(yuexin,shebao,jijinzongshu,qizhengdian);
  // 税后工资 = 月薪- 税额 - 社保 - 公积金；
  var  shuihougongzi = yuexin - shui.shui -shebao - jijinzongshu;
  mokuaibox.find('.y_sh').html(shuihougongzi);
  mokuaibox.find('.y_sq1').html(yuexin);
  mokuaibox.find('.y_yl1').html(sbjishu*0.08);
  mokuaibox.find('.y_yb1').html(sbjishu*0.02);
  mokuaibox.find('.y_sy1').html(sbjishu*0.005);
    mokuaibox.find('.y_gjbl').html(bili*100);
    mokuaibox.find('.y_gjj').html(jijinzongshu);
  mokuaibox.find('.y_sdsbl').html(shui.rate*100);
  mokuaibox.find('.y_sds1').html(shui.shui);     
  //税前
  // var suodee = yuexin - 社保 - 税收 - 



}else{
//税后金额 

 btnCalc_onClick_shgz(mokuaibox);




}

});


////////////////////////shgz
function btnCalc_onClick_shgz(mokuaibox)
{
    // clearResult_shgz();
    var income = parseFloat(mokuaibox.find(".yuexin").val());
    // if(isNaN(income)) {
    //     alert("无效的收入金额");
    //     $("#txtIncome")[0].focus();
    //     $("#txtIncome")[0].select();
    //     return;
    // }
    
    mokuaibox.find(".yuexin").val(income); 
    var sbjishu = mokuaibox.find('.sbjishu').val(); 
    var shebao = shebaoedu(sbjishu);
     mokuaibox.find('.y_yl1').html(sbjishu*0.08);
  mokuaibox.find('.y_yb1').html(sbjishu*0.02);
  mokuaibox.find('.y_sy1').html(sbjishu*0.005);
  mokuaibox.find('.y_sh').html(income);


var jijin = mokuaibox.find('.jijin').val();
var bili = mokuaibox.find('.bili').val(); 
var jijinzongshu = jijin*bili;
    mokuaibox.find('.y_gjbl').html(bili*100);
    mokuaibox.find('.y_gjj').html(jijinzongshu);



    var shebaogongjijin = jijinzongshu + shebao;


    var insure = parseFloat(shebaogongjijin);
    // if(isNaN(insure)) {
    //     alert("无效的各项社会保险费金额");
    //     $("#txtInsure")[0].focus();
    //     $("#txtInsure")[0].select();
    //     return;
    // }
    // mokuaibox.find(".sbjishu").val(insure);   



    var baseLine=qizhengdian;
    
    var taxableIncome = income - baseLine;
    // if(taxableIncome <=0){
    //     $("#txtTax")[0].value="0";
        
    //     $("#txtRealIncome")[0].value=(income +insure).toFixed(2);
    //     $("#txtIncome")[0].focus();
    //     $("#txtIncome")[0].select();
    //     return;  
    // }


    
    var R,Q;
    var A=taxableIncome;
    A=A.toFixed(2);
    
    if(A<=1455){R=0.03;Q=0;}
    else if(A>1455 && A<=4155){R=0.1;Q=105}
    else if(A>4155 && A<=7755){R=0.2;Q=555}
    else if(A>7755 && A<=27255){R=0.25;Q=1005}
    else if(A>27255 && A<=41255){R=0.3;Q=2755}
    else if(A>41255 && A<=57505){R=0.35;Q=5505}
    else{R=0.45;Q=13505;}  
    
    taxableIncome=(A - Q)/(1-R);
    A=taxableIncome.toFixed(2);    
    if(A<=1500){R=0.03;Q=0;}
    else if(A>1500 && A<=4500){R=0.1;Q=105}
    else if(A>4500 && A<=9000){R=0.2;Q=555}
    else if(A>9000 && A<=35000){R=0.25;Q=1005}
    else if(A>35000 && A<=55000){R=0.3;Q=2755}
    else if(A>55000 && A<=80000){R=0.35;Q=5505}
    else{R=0.45;Q=13505;} 
    var tax=A * R -Q;
    var realIncome=income +insure + tax;            
 
    
  mokuaibox.find('.y_sdsbl').html(R*100);
  mokuaibox.find('.y_sds1').html(tax);
    // $("#txtTax")[0].value=tax.toFixed(2);
    mokuaibox.find('.y_sq1').html(realIncome.toFixed(2));
    // $("#txtRealIncome")[0].value=realIncome.toFixed(2);
    // $("#txtIncome")[0].select();
}







});



