import Foundation from"foundation-sites";import Swiper from"swiper";import $ from"jquery";$(document).foundation(),$(document).ready((function(i){i(".swiper-container").each((function(){new Swiper(this,{pagination:{el:i(this).find(".swiper-pagination"),dynamicBullets:!1},navigation:{nextEl:i(this).find(".swiper-button-next"),prevEl:i(this).find(".swiper-button-prev")},spaceBetween:30,autoHeight:!0,slidesPerView:3,loop:!1})}))}));