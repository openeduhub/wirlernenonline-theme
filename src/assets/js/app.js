import Foundation from 'foundation-sites';
import Swiper from 'swiper';
import $ from "jquery";
// window.$ = $;

$(document).foundation();

$(document).ready(function($) {

  // Initialize new Swiper on each .swiper-container
  $('.swiper-container').each(function() {
    new Swiper(this, {
      pagination: {
        el: $(this).find('.swiper-pagination'),
        dynamicBullets: false
      },
      navigation: {
        nextEl: $(this).find('.swiper-button-next'),
        prevEl: $(this).find('.swiper-button-prev')
      },
      spaceBetween: 30,
      autoHeight: true,
      slidesPerView: 3,
      loop: false
    })
  })

});
