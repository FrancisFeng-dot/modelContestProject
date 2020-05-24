/**
 * @description this files description.
 * @author daisy
 * @data 2017/6/30
 * @email 13971738871@163.com
 */
$(document).ready(function () {
  $('.warp').height(window.innerHeight)
  $('nav>a').click(function () {
    $(this).addClass('active').siblings().removeClass('active')
  })
  $(".scroll1").click(function(){
    $('html,body').animate({scrollTop:$('#match').offset().top
    }, 1000)
  });
  $(".scroll2").click(function(){
    $('html,body').animate({scrollTop:$('#invite02').offset().top
    }, 1000)
  });
  $(".scroll3").click(function(){
    $('html,body').animate({scrollTop:$('#event03').offset().top
    }, 1000)
  });
  $(".scroll4").click(function(){
    $('html,body').animate({scrollTop:$('#event04').offset().top
    }, 1000)
  });
  var autoChange = setInterval(function () {

    for (var i=0; i < $('.banner>img').length ; i++){
      $('.banner>img').fadeOut(2000).eq(i).fadeIn(2000);
      if ( i = $('.banner>img').length){
        i=0;
      }
    }
  },2000)

  autoChange
})
