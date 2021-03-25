import Foundation from 'foundation-sites';
//import Swiper from 'swiper';
import $ from "jquery";
// window.$ = $;

$(document).foundation();

$(document).ready(function($) {

  console.log('app.js');

  // Scroll Hide Navbar
  var prev = 0;
  var $window = $(window);
  var nav = $('.site-header');

  $window.on('scroll', function(){
    var scrollTop = $window.scrollTop();
    if (scrollTop < 0) {
      console.log('nav.removeClass("hidden")');
      nav.removeClass('hidden');
      return;
    } else {
      console.log('toggleClass nav-hidden');
      nav.toggleClass('hidden', scrollTop > prev);
    }
    prev = scrollTop;
  });
});
