import Foundation from 'foundation-sites';
//import Swiper from 'swiper';
import $ from "jquery";
// window.$ = $;

$(document).foundation();

$(document).ready(function($) {

  // Scroll Hide Navbar
  var prev = 0;
  var $window = $(window);
  var nav = $('.site-header');

  $window.on('scroll', function(){
    var scrollTop = $window.scrollTop();
    if (scrollTop < 0) {
      nav.removeClass('hidden');
      return;
    } else {
      nav.toggleClass('hidden', scrollTop > prev);
    }
    prev = scrollTop;
  });
});
