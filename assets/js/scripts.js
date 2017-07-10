function isTouchDevice(){
    return true == ("ontouchstart" in window || window.DocumentTouch && document instanceof DocumentTouch);
}

function parallax() {
	if( jQuery('.page-title > .container').length && jQuery('#tt-slider').length<1 ){
		var scrollPosition = jQuery(window).scrollTop();
		if( jQuery('.page-title > .container').eq(0).offset().top<120 ){
			jQuery('.page-title > .container').eq(0).css('opacity',((100 - scrollPosition) *0.01));
		}
	}
}


function detectIE() {
    var ua = window.navigator.userAgent;
    var msie = ua.indexOf('MSIE ');
    var trident = ua.indexOf('Trident/');

    if (msie > 0) {
        // IE 10 or older => return version number
        return parseInt(ua.substring(msie + 5, ua.indexOf('.', msie)), 10);
    }

    if (trident > 0) {
        // IE 11 (or newer) => return version number
        var rv = ua.indexOf('rv:');
        return parseInt(ua.substring(rv + 3, ua.indexOf('.', rv)), 10);
    }

    // other browser
    return false;
}



function getContainerWidth(){
	var $container_width = jQuery('body').hasClass('boxed') ? jQuery('body > .layout-wrapper').width() : jQuery(window).width();
	$container_width = jQuery('.layout-wrapper').hasClass('right-section') ? jQuery('.layout-wrapper.right-section').width() : $container_width;
	return $container_width;
}


function fix_product_height(){
	jQuery('.product-image-hover').each(function(){
		var $this = jQuery(this);
		$this.height( $this.width() );
	});
}


(function($) {
	"use strict";


	/* Document Ready */
	$(document).ready(function(){

		var $ie_version = detectIE();
		if( $ie_version!==false ){
			$('html').addClass('oldie ie'+$ie_version);
		}

		$('p').each(function(){
			if( $(this).html()=='' ){ $(this).remove(); }
		});

		/* Fixing Header when has Top Slider */
		var $top_slider = $('.slider-fullscreen');
		var has_topslider = false;
		if( $top_slider.length>0 ){
			has_topslider = true;
			$('#header').css({'position': 'relative'});

			$('#header_spacing').height(0);
			$('.admin-bar #header').css({ 'margin-top': '0px' });
		}
		else{
			
			/*	Header height calculator
			================================================== */
			if( $('.header-transparent').length>0 ){
				$('#header_spacing').height(0);
				
				if( $('#tt-slider').length<1 ){
					$('section.section').eq(0).css({
						'padding-top': +parseInt($('section.section').eq(0).css('padding-top'))+$('#header').height()+'px'
					});
				}

				/** It works if enabled sticky menu. */
				if( $('.header-transparent').hasClass('navbar-fixed-top') ){
					$(window).on('scroll', function(){
						var scrollTop = $(window).scrollTop();
						if( scrollTop > $('#header').height()+50 ){
							if( !$('#header').hasClass('stickymenu') ){
								$('#header').addClass('stickymenu');
							}
						}
						else{
							if( $('#header').hasClass('stickymenu') ){
								$('#header').removeClass('stickymenu');
							}
						}
					});
				} // end sticky transparent menu
				
			}
			else{
				if( $('.navbar-fixed-top').length>0 ){
					$('#header_spacing').height( $('#header').height()-1 );
					$(window).resize(function(){
						$('#header_spacing').height( $('#header').height());
					});
				}
				else{
					$('#header_spacing').height(0);
				}
			}
			
		}


		$(window).on('scroll', function() {
			parallax();

			var scrollTop = $(window).scrollTop();
			var topbar_h = $('#top_bar').length>0 ? $('#top_bar').outerHeight() : 0;
			if( $(window).width()>600 && $('.navbar-fixed-top').length>0 ){
				if( has_topslider ){
					var wpbarh = $('#wpadminbar').length>0 ? $('#wpadminbar').height() : 0;
					var diff_wpbar_topbar = topbar_h-wpbarh;
					if( $top_slider.height()+diff_wpbar_topbar < scrollTop-wpbarh ){
						/* in here */
						$('#header_spacing').height( $('#header').height() );
						$('#header').addClass('stickymenu');
						$('#header').css({'position': 'fixed', 'margin-top': -diff_wpbar_topbar+'px' });
					}
					else{
						/* out of scroll */
						$('#header_spacing').height(0);
						$('#header').removeClass('stickymenu');
						$('#header').css({'position': 'static', 'margin-top': '0px' });
					}
				}
				else{
					if( topbar_h>0 ){
	                    // enabled topbar
	                    if( scrollTop < topbar_h ){
	                        $('#header').css({ 'top': -scrollTop+'px' });
	                        
	                        if( !$('.header-transparent').hasClass('navbar-fixed-top') ){
	                            $('#header').removeClass('stickymenu');
	                        }
	                    }
	                    else{
	                        $('#header').css({ 'top': -topbar_h+'px' });
	                        
	                        if( !$('.header-transparent').hasClass('navbar-fixed-top') ){
	                            $('#header').addClass('stickymenu');
	                        }
	                    }
	                }
	                else{
	                    // disabled topbar
	                    var header_height_no_sticky = 0;
	                    var hheight = header_height_no_sticky==0 ? $('#header').height() : header_height_no_sticky;
	                    if( scrollTop < hheight ){
	                        $('#header').css({ 'top': -scrollTop+'px' });
	                        
	                        if( !$('.header-transparent').hasClass('navbar-fixed-top') ){
	                            $('#header').removeClass('stickymenu');
	                        }
	                    }
	                    else{
	                        $('#header').css({ 'top': -topbar_h+'px' });
	                        
	                        if( !$('.header-transparent').hasClass('navbar-fixed-top') ){
	                            $('#header').addClass('stickymenu');
	                        }
	                    }
	                }
				}
			}
			else{
				$('#header').css({ 'top': '0px' });
			}
		});


		/*	Replacing one page menu with Main menu
		================================================== */
		if( $('#onepage-menu').length>0 ){
			if($('#one_page_menu').find('li').length>0) {
				$('#header').find('ul.navbar-nav').html( $('#one_page_menu').html() ).attr('id', 'one-page-menu').css('display','inline-block');
			}
		}


		/* Mobile Menu */
		if( $('#mobile-menu-wrapper nav').length<1 && $('.navmenu-cell').length>0 ){
			var $navmenu = $('.navmenu-cell').clone();
			$navmenu.attr('class', '').attr('role', '').attr('style', '').attr('id', 'mobile-menu');
			$navmenu.find('.header-search').remove();
			$navmenu.find('ul').attr('class', '');

			$navmenu.find('li.mega-menu').each(function(){
				var $li = $(this);
				var $megamenu_item = $li.find('>ul');
				$li.find('.menu-column').each(function(){
					$megamenu_item.append('<li><a href="javascript:;"><b>'+ $(this).find('>h3').html() +'</b></a></li>');
					$(this).find('.menu-item').each(function(){
						$megamenu_item.append('<li>'+ $(this).html() +'</li>');
					});
					$(this).remove();
				});
				$li.find('>ul > li').eq(0).remove();
			});

			$('#mobile-menu-wrapper .mobile-menu-content').append($navmenu);

		}	

		var fixMobileMenu = function(){
			$('.slidemenu-push.smenu-push-toLeft').height( $(window).height() );
			$(window).resize(function(){
				$('.slidemenu-push.smenu-push-toLeft').height( $(window).height() );
			});
		};

		$('#mobile-menu-handler').on('click', function(){
			$('body').toggleClass('smenu-push-toLeft');
			$('.mobile-menu-content').toggleClass('smenu-push-toLeft');
			fixMobileMenu();
		});
		$('body').append('<div id="smenu-overlay"></div>');
		$('#smenu-overlay').on('click', function(){
			$('body').removeClass('smenu-push-toLeft');
			$('.mobile-menu-content').removeClass('smenu-push-toLeft');
			$('#mobile-cart-wrapper').removeClass('smenu-push-toLeft');
		});
		$('.mobile-menu-content.slidemenu-push li a').on('click', function(){
			if( $(this).parent().find('>ul').length>0 ){
				$(this).parent().find('>ul').slideToggle();
				$(this).parent().toggleClass('smenu-open');
				return false;
			}
			$('#smenu-overlay').trigger("click");
		});


		/* Mobile Shopping Cart */
		if( $('#mobile-cart-wrapper').length>0 ){
			var $ul = $('#mobile-cart-wrapper').find('.cart_list');
			var $li1 = $('<li class="total"></li>').append( $ul.parent().find('.total') );
			var $li2 = $('<li class="buttons"></li>').append( $ul.parent().find('.buttons') );
			$ul.append($li1).append($li2);
			$('#mobile-cart-wrapper').find('.mobile-cart-tmp nav').append($ul);

			$('#mobile-cart-handler').on('click', function(){
				$('body').toggleClass('smenu-push-toLeft');
				$('#mobile-cart-wrapper').toggleClass('smenu-push-toLeft');
				fixMobileMenu();
			});
		}


		/* Search icon event */
		$('#header .header-search .search-icon').click(function(){
			$(this).parent().find('.search-form').show();
			$(this).parent().find('.search-form input[type=text]').focus();
		});
		$(document).click(function(event){
			var $target = $(event.target);
			var $p = $target.parent();
			if( $p.hasClass('search-icon') || $p.hasClass('header-search') || $p.hasClass('input-group') ){ }
			else{
				$('#header .header-search .search-form').hide();
			}
		});	


		/*	Onepage Local Scroll
		================================================== */
		if( $('#onepage-menu').length>0 ){
			$('#onepage-menu').find('a').tooltip({
				'selector': '',
				'placement': 'left',
				'container':'body'
			});

			$('#onepage-menu,#one-page-menu:not(.custom)').find('a').click(function(){
				var $this = $(this);
				var id = '#post-'+$this.data('id');
				if( $('#post-title-'+$this.data('id')).length>0 ){
					id = '#post-title-'+$this.data('id');
				}
				var $wpbar = $('#wpadminbar').length >0 ? $('#wpadminbar').height() : 0;
				$.scrollTo( $(id), 500, { offset: -($('#header').height()-$wpbar)+10 } );
			});

			$(window).scroll(function () {

				var $wpbar = $('#wpadminbar').length >0 ? $('#wpadminbar').height() : 0;
				var header_offset = $('#header').height()-$wpbar;

				$('#onepage-menu').find('a').each(function(){
					var data_id = $(this).data('id');
					var $target = $('#post-'+data_id);
					$target = $('#post-title-'+data_id).length>0 ? $('#post-title-'+data_id) : $target;
					if( $target.offset().top-header_offset < $(window).scrollTop() ){
						// Adding class for side bullets
						$('#onepage-menu').find('a').parent().removeClass('active');
						$(this).parent().addClass('active');
						// Adding classs for main menu
						$('#one-page-menu').find('a').parent().removeClass('active');
						$('#one-page-menu').find('a[data-id="'+data_id+'"]').parent().addClass('active');
					}				
				});
				
			});
		}
		else{
			if( $('div.sidebar').length>0 ){
				if( $('div.sidebar').hasClass('sidebar-left') )
					$('body').addClass('has-sidebar-left')
				else
					$('body').addClass('has-sidebar-right')
			}
		}

		if( ($('.page-title.section').length<1 || !$('.page-title.section').prev().hasClass('header')) && $('.tt-slider').length<1 ){
			$('body').addClass('no-title-section');
		}


		/*	Pretty Photo
		================================================== */
		$('.gallery a').addClass('lightbox');
		$("a[rel^='prettyPhoto'],a.prettyPhoto,a.lightbox,.blox-element.prettyPhoto>a,.blox-element.lightbox>a").prettyPhoto({deeplinking:false,social_tools:false});



	    // Go to top arrow
	    $('span.gototop').click(function() {
	        $('body,html').animate({scrollTop: 0}, 600);
	    });

	    $(window).scroll(function(){
	        if( $(window).scrollTop() > 500 ){
	            $('.gototop').addClass('show');
	        }
	        else{
	            $('.gototop').removeClass('show');
	        }
	    });

		/*	Bootstrap JS
		================================================== */
		$('[data-toggle="tooltip"]').tooltip();
		$('[data-toggle="popover"]').popover();


		$('.affix-element').each(function(){
			var $this = $(this);
			$this.affix({
				offset: {
					top: 300,
					bottom: 10
				}
			});
		});
		
		
		/*	Check menu hasChildren
		================================================== */
		if( $('.main-menu ul').length>0 ){
			$('.main-menu ul').eq(0).find('li').each(function(){
				var $this = $(this);
				if( $this.find('ul').length > 0 ){
					$this.addClass('has-children');
				}
			});
		}



		/* Fix Loop iFrame size
		===================================================*/
		$('.grid-loop').each(function(){
			var $this = $(this);
			$this.find('.entry-media iframe').each(function(){
				var $media = $(this).parent();
				var $iframe = $(this);
				$iframe.width($media.width()).height( parseInt($media.width()*350/600) );

				$(window).resize(function(){
					$iframe.width($media.width()).height( parseInt($media.width()*350/600) );
				});
			});
		});


		/* Fix Embed Video Height
		===================================================*/
		$("section.primary").fitVids();



		/*	Swiper Slider
		================================================== */
		$('.swipy-slider').each(function(index){
			var $this = $(this);
			$this.find('.swiper-slide,.swiper-slide img').css({ 'width':'100%', 'display':'block' });
			$this.find('.swiper-pagination').addClass('swipy-paginater'+index);
			var $swiper = $this.swiper({
								mode:'horizontal',
								loop: true,
								keyboardControl: false,
								paginationClickable: true,
								resizeReInit: true,
								calculateHeight: true,
								pagination: '.swipy-paginater'+index
							});

			$this.fadeIn('fast');
			$this.find('.swiper-control-prev').click(function(){
				$swiper.swipePrev();
			});
			$this.find('.swiper-control-next').click(function(){
				$swiper.swipeNext();
			});

			if( $this.parent().hasClass('gallery_viewport') && $this.parent().parent().parent().find('.button').length>0 ){
				$this.parent().parent().parent().find('.button').click(function(){
					$swiper.swipeTo(0);
				});
			}
		});




		/* Portfolio Slider
		===================================================*/
		$('.portfolio-slider').each(function(){
			var $this = $(this).find('.swiper-container');
			var xr16x6 = 0.375;
			var xr16x9 = 0.5625;
			xr16x6 = $this.width()<960 ? xr16x9 : xr16x6;
			var h = $this.width()*xr16x6;
			h = h>640 ? 640 : h;
			$this.find('.swiper-wrapper').height(h);

			var $swiper = $this.swiper({
								mode:'horizontal',
								loop: true,
								keyboardControl: true,
								paginationClickable: true,
								resizeReInit: true,
								pagination: '.swiper-pagination',
								onSlideChangeEnd: function(swiper, direction){
									if( !$this.find('.swiper-slide.video').hasClass('swiper-slide-active') ){
										$this.find('.swiper-slide.video').html( $this.find('.swiper-slide.video').html() );
									}
								}
							});
			$this.find('.swiper-control-prev').click(function(){
				$swiper.swipePrev();
			});
			$this.find('.swiper-control-next').click(function(){
				$swiper.swipeNext();
			});

			if( $this.hasClass('layout-sidebar') )
				$this.find('iframe').width( $this.width() ).height( $this.width()*xr16x6 );
			else
				$this.find('iframe').height(h).width(h*1.777);
			$this.find('.video-wrapper').show();


			$(window).resize(function(){
				var xr16x6 = 0.375;
				var xr16x9 = 0.5625;
				xr16x6 = $this.width()<960 ? xr16x9 : xr16x6;
				var h = $this.width()*xr16x6;
				h = h>640 ? 640 : h;
				$this.find('.swiper-wrapper').height(h);
				$this.find('.swiper-slide').height(h);
				
				if( $this.hasClass('layout-sidebar') )
					$this.find('iframe').width( $this.width() ).height( $this.width()*xr16x6 );
				else
					$this.find('iframe').height(h).width(h*1.777);
				
			});
		});

		
		/* Carousel Swiper Slider
		====================================*/
		$('.blox-carousel.swiper-container').each(function(){
			var $this = $(this);
			var $autoplay = $(this).data('duration');
			var column = 1;
			
			if( $this.width() > 939){ column = 4; }
			else if( $this.width() > 422 ){ column = 3; }
			else if( $this.width() > 400 ){ column = 2; }

			$('.woocommerce.swiper-container ul.products li.product').css({
				'margin': 'auto',
				'padding': '15px'
			});

			if( $this.hasClass('woocommerce') ){
				$this.find('li').each(function(){
					$(this).removeClass('last first')
								.addClass('swiper-slide')
								.addClass('col-md-3 col-sm-6 col-xs-12');
				});
			}
			
			var swipe_option = {slidesPerView: column, calculateHeight: true};
			if( $autoplay != '0'){
				jQuery.extend(swipe_option, {autoplay: $autoplay});
			}

			var $carousel = $this.swiper(swipe_option);

			$this.find('.carousel-control-prev').click(function(){
				$carousel.swipePrev();
			});
			$this.find('.carousel-control-next').click(function(){
				$carousel.swipeNext();
			});

			// fix title position
			$this.find('article.entry.hover').each(function(){
	            var $title = $(this).find('.entry-title h2');
	            $title.css({ 'margin-top': parseInt( $(this).height()/2-60-$title.height()/2 ) });
	        });

	        $(window).load(function(){
	        	$carousel.reInit();
	        });

			$(window).resize(function(){
				if( $this.width() > 939){ $carousel.params.slidesPerView = 4; }
				else if( $this.width() > 422 ){ $carousel.params.slidesPerView = 3; }
				else if( $this.width() > 400 ){ $carousel.params.slidesPerView = 2; }
				else{ $carousel.params.slidesPerView = 1; }

				// fix title position
				$this.find('article.entry.hover').each(function(){
					var $title = $(this).find('.entry-title h2');
					$title.css({ 'margin-top': parseInt( $(this).height()/2-60-$title.height()/2 ) });
				});
			});
		});

		


		/* Fullwidth Carousel Swiper Slider
		====================================*/
		$('.fullwidth-carousel').each(function(){
			var $this = $(this);
			var $autoplay = $(this).data('duration');
			var column = 1;

			var $container_width = getContainerWidth();
			var lw_ofleft = $('body > .layout-wrapper').offset().left;
			
			$this.width( $container_width )
				.css({ 'margin-left': -$this.offset().left+lw_ofleft });
			
			
			$this.find('.blox-element.grid-loop').css({ 'margin-bottom': '0px' });
			$this.find('.grid-loop article').css({ 'margin-bottom': '0px' });
			$this.find('.entry-media').css({ 'margin-bottom': '0px' });
			
			
			if( $this.width() > 939){ column = 4; }
			else if( $this.width() > 422 ){ column = 3; }
			else if( $this.width() > 400 ){ column = 2; }

			var swipe_option = {slidesPerView: column, calculateHeight: true};
			if( $autoplay != '0'){
				jQuery.extend(swipe_option, {autoplay: $autoplay});
			}

			var $carousel = $this.swiper(swipe_option);

	        $this.animate({ 'opacity': 1 }, 1000);

			$(window).resize(function(){
				var $container_width = getContainerWidth();
				var lw_ofleft = $('body > .layout-wrapper').offset().left;

				$this.width( $container_width ).css({ 'margin-left': '0' });
				$this.css({ 'margin-left': -$this.offset().left+lw_ofleft });

				if( $this.width() > 939){ $carousel.params.slidesPerView = 4; }
				else if( $this.width() > 422 ){ $carousel.params.slidesPerView = 3; }
				else if( $this.width() > 400 ){ $carousel.params.slidesPerView = 2; }
				else{ $carousel.params.slidesPerView = 1; }

				// fix title position
				$this.find('article.entry.hover').each(function(){
					var $title = $(this).find('.entry-title h2');
					$title.css({ 'margin-top': parseInt( $(this).height()/2-60-$title.height()/2 ) });
				});
			});
		});


		
		
		/*  Fullwidth Portfolio Masonry
	    ================================================== */
	    $('.fullwidth-portfolio').each(function(){
	        var $this = $(this);

	        var container_width = getContainerWidth();
			var lw_ofleft = $('body > .layout-wrapper').offset().left;

	        $this.find('.masonry-container').width( container_width ).css({ 'margin-left': -$this.offset().left+lw_ofleft });

	        var $col = parseInt( $this.attr('data-column') );

	        if( $this.width() > 939){  }
			else if( $this.width() > 422 ){ $col = 3; }
			else if( $this.width() > 400 ){ $col = 2; }
			else{ $col = 1; }

	        $this.find('.post_filter_item').width( container_width/$col )
	                .css({
	                    'float': 'left'
	                });

	        $this.css({ 'margin-bottom': '0px' });
	        $this.find('article.entry').css({ 'margin-bottom': '0px' });
	        $this.find('.entry-media').css({ 'margin-bottom': '0px' });

	        $this.animate({ 'opacity': 1 }, 1000);

	        $this.find('article.entry.hover').each(function(){
	            var $title = $(this).find('.entry-title h2');
	            $title.css({ 'margin-top': parseInt( $(this).height()/2-60-$title.height()/2 ) });
	        });

			$(window).resize(function(){
				var container_width = getContainerWidth();
				var lw_ofleft = $('body > .layout-wrapper').offset().left;

	            $this.find('.masonry-container').width( container_width ).css({ 'margin-left': '0' });
	            $this.find('.masonry-container').css({ 'margin-left': -$this.offset().left+lw_ofleft });

	            if( $this.width() > 939){ $this.find('.post_filter_item').width( container_width/$col ); }
	            else if( $this.width() > 422 ){ $this.find('.post_filter_item').width( container_width/3 ); }
	            else if( $this.width() > 400 ){ $this.find('.post_filter_item').width( container_width/2 ); }
	            else{ $this.find('.post_filter_item').width( container_width ); }

	            $this.find('article.entry.hover').each(function(){
	                var $title = $(this).find('.entry-title h2');
	                $title.css({ 'margin-top': parseInt( $(this).height()/2-60-$title.height()/2 ) });
	            });
	        });
	    });


		

		
		/* Woocommerce Ajax Complete Request */
		$(document).ajaxComplete(function( event, request, settings ) {
			if( typeof settings.data != 'undefined' && (settings.data.indexOf('action=woocommerce_get_refreshed_fragments')>-1 || settings.data.indexOf('action=woocommerce_add_to_cart')>-1) ){
				var response = request.responseJSON;

				if( typeof response.fragments!=='undefined' && typeof response.fragments['div.widget_shopping_cart_content']!=='undefined' ){
					var cart = response.fragments['div.widget_shopping_cart_content'];
					$('.woocommerce-shcart').each(function(){
						var $this = $(this);
						$this.find('.shcart-content').html( cart );

						var count = 0;
						//var total = $this.find('.shcart-content').find('.total .amount').html();
						$this.find('.shcart-content').find('.quantity').each(function(){
							var $quant = $(this).clone();
							$quant.find('.amount').remove();
							count += parseInt($quant.text());
						});

						$this.find('.shcart-display .total-cart').html( count );
					});
				}
				
			}
		});

		/*
		$('.woocommerce-shcart').each(function(){
			var $this = $(this);
			$this.find('.shcart-display').hover(
				function(){
					$this.find('.shcart-content').slideDown();
				},
				function(){
					$this.find('.shcart-content').slideUp();
				}
			);
		});
		*/
		



		/*	Animation with Waypoints
		================================================== */
		var animate_start = function($this){
			$this.find('.animate').each(function(i){
				var $item = $(this);
				var animation = $item.data("animate");

				$item.waypoint(function(direction){
					$item.css({
						'-webkit-animation-delay': (i*0.1)+"s",
						'-moz-animation-delay': (i*0.1)+"s",
						'-ms-animation-delay': (i*0.1)+"s",
						'-o-animation-delay': (i*0.1)+"s",
						'animation-delay': (i*0.1)+"s"
					});
					$item.removeClass('animate').addClass('animated '+animation).one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
						$(this).removeClass(animation+' animated');
					});
				},
				{
					offset: '88%',
					triggerOnce: true
	            });
			});
		};
		$('.blox-row').each(function(){
	        var $this = $(this);
	        animate_start( $this );
		});



		/*	Counter Element
		================================================== */
		$('.blox-counter').each(function(){
			var $this = $(this);
			if( $this.hasClass('counter-count') ){
				$this.find('.counter-number').counterUp({
					delay: 10,
					time: 1000
				});
			}
			else if( $this.hasClass('counter-scroll') ){
				$this.waypoint(function(){
					$this.find('.numeric').each(function(){
						var $numeric = $(this);
						var $ul = $numeric.find('ul');
						var $li_count = $ul.find('li').length;
						$ul.animate({ 'top': '-'+$ul.height()/$li_count*($li_count-1)+'px' }, 1500);
					});
				},
				{offset:"100%", triggerOnce:!0 });
			}
		});

	});



	/* Window Load */
	$(window).load(function(){
		/* Fix menu position */
		/*
		var menuHeight = ($('#header').find('.navbar-header').height() - $('#header').find('.mainmenu').height())/2;
		menuHeight = parseInt(menuHeight);
		$('#header').find('.mainmenu').css({ 'margin-top': menuHeight+'px' });
		*/

		if( $('.navbar-fixed-top').length>0 ){
			if( $('.navbar-fixed-top').hasClass('header-transparent') ){
				$('#header_spacing').height(0);
			}
			else{
				$('#header_spacing').height( $('#header').height()-1 );
				$(window).resize(function(){
					$('#header_spacing').height( $('#header').height());
				});
			}
		}
		

		
		fix_product_height();
		$(window).resize(function(){
			fix_product_height();
		});

		$('ul.products:not(.swiper-wrapper)').each(function(){
			var $product = $(this);
			$product.isotope({
	            itemSelector : 'li.product',
	            layoutMode: 'fitRows'
	        });

	        $(window).resize(function(){
				$product.isotope('layout');
			});
		});
		

		/* init Skrollr Parallax
		==================================================*/
		if( !isTouchDevice() ){
			$(window).stellar({
				horizontalScrolling: false,
				responsive: true
			});
		}


		/*  Fullwidth Portfolio Masonry
	    ================================================== */
	    $('.fullwidth-portfolio').each(function(){
	        var $this = $(this);
	        var $portfolio_masonry = $this.find('.masonry-container');
	        var masonry_item = '.post_filter_item';

	        $portfolio_masonry.isotope({
	            itemSelector : masonry_item
	        });

	        $this.find('.portfolio-filter ul li a').click(function(){
	            var $filter = $(this);
	            var filter = $filter.attr('data-filter');

	            $this.find('.portfolio-filter ul li a').removeClass('active');
	            $filter.addClass('active');
	            
	            $this.find('.portfolio-filter h3').html( $filter.html() );
	            filter = filter=='all' ? '*' : '.'+filter;
	            $portfolio_masonry.isotope({ filter: filter });
	        });

	        /** Fix title Position */
	        $this.find('article.entry.hover').each(function(){
	            var $title = $(this).find('.entry-title h2');
	            $title.css({ 'margin-top': parseInt( $(this).height()/2-60-$title.height()/2 ) });
	        });
	    });



	    /** Fix Fullwidth Carousel Title Position */
		$('.fullwidth-carousel').each(function(){
			var $this = $(this);
			$this.find('article.entry.hover').each(function(){
				var $title = $(this).find('.entry-title h2');
				$title.css({ 'margin-top': parseInt( $(this).height()/2-60-$title.height()/2 ) });
			});
		});
	});

})(jQuery);
