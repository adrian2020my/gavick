// IE checker
function gkIsIE() {
  var myNav = navigator.userAgent.toLowerCase();
  return (myNav.indexOf('msie') != -1) ? parseInt(myNav.split('msie')[1]) : false;
}
//
var page_loaded = false;
//
window.addEvent('load', function() {
	//
	page_loaded = true;
	// smooth anchor scrolling
	new SmoothScroll(); 
	// style area
	if(document.id('gkStyleArea')){
		document.id('gkStyleArea').getElements('a').each(function(element,i){
			element.addEvent('click',function(e){
	            e.stop();
				changeStyle(i+1);
			});
		});
	}
	// K2 font-size switcher fix
	if(document.id('fontIncrease') && document.getElement('.itemIntroText')) {
		document.id('fontIncrease').addEvent('click', function() {
			document.getElement('.itemIntroText').set('class', 'itemIntroText largerFontSize');
		});
		
		document.id('fontDecrease').addEvent('click', function() {
			document.getElement('.itemIntroText').set('class', 'itemIntroText smallerFontSize');
		});
	}
	// search
	if(document.id('gkSearchBtn')) {
		document.id('gkSearchBtn').addEvent('click', function() {
			if(document.id('gkSearch').hasClass('active')) {
				document.id('gkSearch').addClass('hide');
				
				setTimeout(function() {
					document.id('gkSearch').removeClass('active');
					document.id('gkSearch').removeClass('hide');
					document.id('gkSearch').setStyle('display', 'none');
				}, 350);
			} else {
				document.id('gkSearch').setStyle('display', 'block');
				
				setTimeout(function() {
					document.id('gkSearch').addClass('active');
				}, 50);
			}
		});
	}
	// login popup
	if(document.id('gkPopupLogin') || document.id('gkPopupCart')) {
		var popup_overlay = document.id('gkPopupOverlay');
		popup_overlay.setStyles({'display': 'block', 'opacity': '0'});
		popup_overlay.fade('out');

		var opened_popup = null;
		var popup_login = null;
		var popup_login_h = null;
		var popup_login_fx = null;
		var popup_cart = null;
		var popup_cart_h = null;
		var popup_cart_fx = null;

		if(document.id('gkPopupLogin') && document.getElement('.gkLogin')) {
			popup_login = document.id('gkPopupLogin');
			popup_login_fx = new Fx.Morph(popup_login, {duration:500, transition: Fx.Transitions.Circ.easeInOut}).set({'opacity': 0, 'margin-top': -50 }); 
			document.getElement('.gkLogin').addEvent('click', function(e) {
				new Event(e).stop();
				popup_overlay.setOpacity(0.01);
				popup_login.setStyle('display', 'block');
				popup_overlay.setStyle('height', document.body.getScrollSize().y);
				popup_overlay.fade(0.45);

				setTimeout(function() {
					popup_login_fx.start({'opacity': 1, 'margin-top': 0});
					opened_popup = 'login';
				}, 450);

				(function() {
					if(document.id('modlgn-username')) {
						document.id('modlgn-username').focus();
					}
				}).delay(600);
			});
		}
		
		if(document.id('gkPopupCart') && document.id('gkCartCounter')) { 
            var btn = document.id('gkCartCounter');
           
         	popup_cart = document.id('gkPopupCart');
            popup_cart.setStyle('display', 'block');
            popup_cart_h = popup_cart.getElement('.gkPopupWrap').getSize().y;
            popup_cart_fx = new Fx.Morph(popup_cart, {duration:500, transition: Fx.Transitions.Circ.easeInOut}).set({'opacity': 0, 'margin-top': -50 }); 
            var wait_for_results = true;
            var wait = false;
            
            btn.addEvent('click', function(e) {
                new Event(e).stop();        
                
                if(!wait) {
                    new Request.HTML({
                        url: $GK_URL + 'index.php?tmpl=cart',
                        onRequest: function() {
                            btn.addClass('loading');
                            wait = true;
                        },
                        onComplete: function() {
                            var timer = (function() {
                                if(!wait_for_results) {
                                    popup_cart.setStyle('display', 'block');
                                    popup_cart_fx.start({'opacity': 1, 'margin-top': 0});
                                    opened_popup = 'cart';
                                    wait_for_results = true;
                                    wait = false;
                                    clearInterval(timer);
                                    btn.removeClass('loading');
                                }
                            }).periodical(200);
                        },
                        onSuccess: function(nodes, xml, text) {
                            document.id('gkAjaxCart').innerHTML = text;
                            popup_cart.setStyle('display', 'block');
                            popup_cart_fx = new Fx.Morph(popup_cart, {duration:200, transition: Fx.Transitions.Circ.easeInOut}).set({'opacity': 0, 'margin-top': -50 }); 
                            wait_for_results = false;
                            wait = false;
                        }
                    }).send();
                }
            });
            
            if(btn) {
            	var counter = document.id('gkCartCounter');
            	var gkCartDataRequest = function() {
          	 		new Request.HTML({
      	 		        url: $GK_URL + 'index.php?tmpl=json',
      	 		        onSuccess: function(nodes, xml, text) {
      	 		            document.id('gkCartCounter').set('html', text);   
      	 		        }
          	 		}).send();    
      	 		} 
      	 		gkCartDataRequest();
      	 		
      	 		if(typeof Virtuemart !== 'undefined') {
	      	 		Virtuemart.sendtocart = function (form) {
	      	 			if (Virtuemart.addtocart_popup ==1) {
	      	 				Virtuemart.cartEffect(form);
	      	 				setTimeout(function() {
	      	 					gkCartDataRequest();
	      	 					document.id('gkCartCounter').addClass('highlighted');
	      	 					
	      	 					setTimeout(function() {
	      	 						document.id('gkCartCounter').removeClass('highlighted');
	      	 					}, 600);
	      	 				}, 1000);
	      	 			} else {
	      	 				form.append('<input type="hidden" name="task" value="add" />');
	      	 				form.submit();
	      	 			}
	      	 		};
      	 		}
      	 		
      	 		cursor_inside_popup_cart = false;
      	 		
      	 		document.id('gkPopupCart').addEvent('mouseenter', function() {
      	 			cursor_inside_popup_cart = true;	
      	 		});
      	 		
      	 		document.id('gkPopupCart').addEvent('mouseleave', function() {
      	 			cursor_inside_popup_cart = false;	
      	 		});
      	 		
      	 		document.body.addEvent('click', function() {
      	 			if(opened_popup == 'cart' && !cursor_inside_popup_cart) {
	      	 			popup_cart_fx.start({ 'opacity': 0 });
	      	 			opened_popup = false;
	      	 			setTimeout(function() {
	      	 				popup_cart.setStyle('display', 'none');
	      	 			}, 500);
      	 			}
      	 		});
            }
    	}

		popup_overlay.addEvent('click', function() {
			if(opened_popup == 'login')	{
				popup_overlay.fade('out');
				popup_login_fx.start({
					'opacity' : 0,
					'margin-top' : -50
				});
				setTimeout(function() {
					popup_login.setStyle('display', 'none');
				}, 450);
			}
		});

		// Magic zoom selectors
		var main_image = false;
		var main_image_thumbnail = false;
		var product_details = false;
		
		if(document.getElement('.main-image')) {
			main_image = document.getElement('.main-image');
			main_image_thumbnail = document.getElement('.main-image > a');
			product_details = document.getElement('.productDetails');
		}
		
		if(document.getElement('.hikashop_product_main_image_subdiv')) {
			main_image = document.getElement('.hikashop_product_main_image_subdiv');
			main_image_thumbnail = document.getElement('.hikashop_product_main_image_subdiv > a');
			product_details = document.getElement('.productDetails');
		}
		
		if(main_image) {
			var thumbnail = main_image_thumbnail;
			var gkZoom = new Element('div', { 'id': 'gkZoom'});
			
			var gkPreview = new Element('div', { 'id': 'gkPreview'});
			gkPreview.setStyles({'left': '-99999px'});
			var gkPreviewSrc = new Element('img', {'src': thumbnail.getElement('img').src });
			
			if(document.body.getSize().x > document.body.getProperty('data-tablet-width')) {
				var gkZoomWidth = parseInt(document.body.getProperty('data-zoom-size'), 10);
				var gkZoomHeight = parseInt(document.body.getProperty('data-zoom-size'), 10);
			} else {
				var gkZoomWidth = thumbnail.getSize().x/3;
				var gkZoomHeight = thumbnail.getSize().x/3;
				var prevSize = product_details.getSize().x/2-20;
				gkPreview.setStyles({'width': prevSize, 'height' : prevSize});
			}
			
			gkZoom.inject(thumbnail, 'bottom');
			gkPreview.inject(document.body, 'bottom');
			gkPreviewSrc.inject(gkPreview, 'bottom');
			gkZoom.setStyles({'width': gkZoomWidth, 'height': gkZoomHeight});
			
			var scale = gkPreview.getSize().x/gkZoomWidth;
			
			var offset = {};
			var touch = {};
			var thumb = {};
			
			if(document.body.getSize().x > document.body.getProperty('data-tablet-width')) {
				thumbnail.addEvents({
					mouseenter: function(){
					    gkPreviewSrc.src = thumbnail.getElement('img').src;
					    gkPreviewSrc.setStyles({'width': scale*thumbnail.getSize().x, 'height': scale*thumbnail.getSize().y});
					    gkPreview.addClass('active');
					    gkPreview.setStyles({'left': thumbnail.getCoordinates().left+thumbnail.getSize().x+20, 'top': thumbnail.getCoordinates().top});
					    gkZoom.addClass('active');
					},
					mousemove: function(e){
						var pos = this.getPosition( );
						//
						offset.x = (e.page.x - pos.x)-gkZoomWidth/2;
						offset.y = (e.page.y - pos.y)-gkZoomHeight/2;
						// validation 
						if(offset.x < 0) offset.x = 0;
						if(offset.y < 0) offset.y = 0;
						if(offset.x > thumbnail.getSize().x-gkZoomWidth) offset.x = thumbnail.getSize().x-gkZoomWidth;
						if(offset.y > thumbnail.getSize().y-gkZoomHeight) offset.y = thumbnail.getSize().y-gkZoomHeight-3;
						gkZoom.setStyles({'left' : offset.x, 'top' : offset.y});
						offset.bx = offset.x*scale;
						offset.by = offset.y*scale+6;
						// validation 
						if(offset.bx < 0) { offset.bx = 0; }
						if(offset.by < 0) { offset.by = 0; }
						if(offset.bx > gkPreviewSrc.getSize().x-gkZoomWidth*scale+12*scale) offset.bx = gkPreviewSrc.getSize().x-gkPreview.getSize().x;
						if(offset.by > gkPreviewSrc.getSize().y-gkZoomHeight*scale+12*scale) offset.by = gkPreviewSrc.getSize().y-gkPreview.getSize().y;
						gkPreviewSrc.setStyles({'right': offset.bx, 'bottom' : offset.by});
					},
					mouseleave: function() {
						gkPreview.removeClass('active');
						gkZoom.removeClass('active');
					}
				});
			} else if(document.body.getProperty('data-tablet-width') > document.body.getSize().x && document.body.getSize().x > document.body.getProperty('data-mobile-width')) {
				thumbnail.addEvent('touchstart', function(e) {
					gkPreviewSrc.src = thumbnail.getElement('img').src;
					var scale = gkPreview.getSize().x/gkZoomWidth;
					gkPreviewSrc.setStyles({'width': scale*thumbnail.getSize().x, 'height': scale*thumbnail.getSize().y});
					gkPreview.addClass('active');
					gkPreview.setStyles({'left': thumbnail.getCoordinates().left+thumbnail.getSize().x+20, 'top': thumbnail.getCoordinates().top});
					gkZoom.addClass('active');
				});
				
				thumbnail.addEvent('touchmove', function(e) {
					thumb.x = thumbnail.getPosition().x;
					thumb.y = thumbnail.getPosition().y;
					touch.x = e.targetTouches[0].pageX;
					touch.y = e.targetTouches[0].pageY;
					//
					if(touch.x > thumb.x && touch.x < thumb.x+thumbnail.getSize().x && touch.y > thumb.y && touch.y < thumb.y+thumbnail.getSize().y) {
						e.preventDefault();
						offset.x = (touch.x - thumb.x)-gkZoomWidth/2;
						offset.y = (touch.y - thumb.y)-gkZoomHeight/2;
						// validation 
						if(offset.x < 0) offset.x = 0;
						if(offset.y < 0) offset.y = 0;
						if(offset.x > thumbnail.getSize().x-gkZoomWidth) offset.x = thumbnail.getSize().x-gkZoomWidth+12;
						if(offset.y > thumbnail.getSize().y-gkZoomHeight) offset.y = thumbnail.getSize().y-gkZoomHeight+12;
						gkZoom.setStyles({'left' : offset.x, 'top' : offset.y});
						offset.bx = offset.x*scale+6;
						offset.by = offset.y*scale+6;
						// validation 
						if(offset.bx < 0) { offset.bx = 0; }
						if(offset.by < 0) { offset.by = 0; }
						if(offset.bx > gkPreviewSrc.getSize().x-gkZoomWidth*scale+12*scale) offset.bx = gkPreviewSrc.getSize().x-gkPreview.getSize().x;
						if(offset.by > gkPreviewSrc.getSize().y-gkZoomHeight*scale+12*scale) offset.by = gkPreviewSrc.getSize().y-gkPreview.getSize().y;
						gkPreviewSrc.setStyles({'right': offset.bx, 'bottom' : offset.by});
					} 
				});
		
				thumbnail.addEvent('touchend', function(e) {
					gkPreview.removeClass('active');
					gkZoom.removeClass('active');
				});
			}
		}		
	}
});
// image overlays
window.addEvent('domready', function() {
	[
		'.img-fulltext-left',
		'.img-fulltext-right',
		'.img-fulltext-none'
	].each(function(selector, i) {
		document.getElements(selector).each(function(img, j) {
			img.addClass('gkImageWrapperOverlayWrap');
			var overlay = new Element('span', { 
				'class': 'gkImageWrapperOverlay nohover',
				'html': '<span></span>' 
			});
			overlay.inject(img, 'bottom');
		});
	});
	
	[
		'.overlay .nspImageWrapper',
		'.itemImage',
		'.img-intro-left',
		'.img-intro-right',
		'.img-intro-none'
	].each(function(selector, i) {
		document.getElements(selector).each(function(img, j) {
			img.addClass('gkImageWrapperOverlayWrap');
			var overlay = new Element('span', { 
				'class': 'gkImageWrapperOverlay',
				'html': '<span><span></span></span>' 
			});
			overlay.inject(img, 'bottom');
		});
	});
})

// function to set cookie
function setCookie(c_name, value, expire) {
	var exdate=new Date();
	exdate.setDate(exdate.getDate()+expire);
	document.cookie=c_name+ "=" +escape(value) + ((expire==null) ? "" : ";expires=" + exdate.toUTCString());
}
// Function to change styles
function changeStyle(style){
	var file1 = $GK_TMPL_URL+'/css/style'+style+'.css';
	var file2 = $GK_TMPL_URL+'/css/typography/typography.style'+style+'.css';
	new Asset.css(file1);
	new Asset.css(file2);
	Cookie.write('gk_storefront_j25_style', style, { duration:365, path: '/' });
}
/* VirtueMart addons */
window.addEvent('domready', function() {
        var tabs = document.id('product-tabs');
        // if tabs exists
        if(tabs && !tabs.hasClass('hikashop_tabs_ul')) {
                // initialization
                tabs.getElement('li').addClass('active');
                var contents = document.id('product-tabs-content');
                contents.getChildren('div').setStyle('display', 'none');
                contents.getElement('div').addClass('active');
                // add events to the tabs
                tabs.getElements('li').each(function(tab, i) {
                        tab.addEvent('click', function() {
                                var toggle = tab.getProperty('data-toggle');
                                contents.getChildren('div').removeClass('active');
                                contents.getElement('.' + toggle).addClass('active');
                                tabs.getElements('li').removeClass('active');
                                tab.addClass('active');                
                        });
                });
        }
});

window.addEvent('touchstart', function(e) {
        if(e.target.hasClass('modal') || e.target.hasClass('ask-a-question')) {
                window.location.href = e.target.getProperty('href');
        }
});