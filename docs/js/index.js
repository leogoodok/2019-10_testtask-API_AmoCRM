/**
 *  Скрипт кнопки прокрутки страницы вверх
 */
jQuery(function($){$(window).scroll(function(){if($(this).scrollTop()!=0){$("#butToTop").fadeIn();}else{$("#butToTop").fadeOut();}});$("#butToTop").click(function(){$("body,html").animate({scrollTop:0},800);});});
