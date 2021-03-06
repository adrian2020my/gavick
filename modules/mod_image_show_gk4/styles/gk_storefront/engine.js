window.addEvent("load",function(){
	setTimeout(function() {
	    $$(".gkIsWrapper-gk_storefront").each(function(el){
	        var elID = el.getProperty("id");
	        var wrapper = document.id(elID);
	        var $G = $Gavick[elID];
	        var slides = [];
	        var links = [];
	        var imagesToLoad = [];
	        var swipe_min_move = 30;
	        var swipe_max_time = 500;
	        // animation variables
	        $G['animation_timer'] = false;
	        // blank flag
	        $G['blank'] = false;
	        // load the images
	        wrapper.getElements('figure').each(function(el,i){
	            var newImg = new Element('img',{
	                "title": el.getProperty('data-title'),
	                "class": 'gkIsSlide',
	                "style": 'z-index: ' + el.getProperty('data-zindex') + ';',
	                "src": el.getProperty('data-url')
	            });
	            links[i] = el.getProperty('data-link');
	            imagesToLoad.push(newImg);
	            newImg.inject(el, 'top');
	        });
	        //
	        var time = setInterval(function(){
	            var process = 0;                
	            imagesToLoad.each(function(el,i){
	                if(el.complete) process++;
	            });
	            
	            if(process == imagesToLoad.length){
	                clearInterval(time);
	                
	                wrapper.getElements('img.gkIsSlide').each(function(el,i){
	                    var newDiv = new Element('div', {
	                        "title": el.getProperty('title'),
	                        "class": 'gkIsSlide',
	                        "style": 'z-index: ' + el.getProperty('style') + '; background-image: url(\'' +  el.getProperty('src') + '\');', 
	                    });
	                    newDiv.inject(el, 'before');
	                });
	                
	                 wrapper.getElements('img.gkIsSlide').each(function(el,i){
	                 	el.dispose();
	                 });

	                setTimeout(function(){
	        			wrapper.getElement('.gkIsPreloader').setStyle('position', 'absolute');
	        			wrapper.getElement('.gkIsPreloader').fade('out');
	        			wrapper.getElement('figure').fade('in');
	        			wrapper.getElement('figure').addClass('active');
	        			wrapper.addClass('loaded');
	        			
	        			setTimeout(function() {
	        				wrapper.getElement('figure').addClass('activated');
	        			}, 50);
	        		}, 400);
	        		
        		    $G['actual_slide'] = 0;
        		    
        		    wrapper.getElements(".gkIsSlide").each(function(elmt,i){
        		        slides[i] = elmt;
        		    }); 
        		    
        		    setTimeout(function() {
        		        var initfig = slides[0].getParent().getElement('figcaption');
        		        if(initfig) {
        		        	initfig.set('morph', { duration: 250 });
        		        	initfig.morph({'opacity': [0, 1]});
        		        }
        		    }, 250);
        		    
        		    if($G['slide_links']){
        		        wrapper.getElements('.gkIsSlide').addEvent("click", function(e){ 
        		            window.location = links[$G['actual_slide']]; 
        		        });
        		        wrapper.getElements('.gkIsSlide').setStyle('cursor', 'pointer');
        		    }  
        		    
        		    wrapper.getElements('.gkIsPagination li').each(function(item, i) {
        		    	item.addEvent('click', function() {
        		    		if(i != $G['actual_slide']) {
        		    			$G['blank'] = true;
        		    			gk_storefront_autoanimate($G, wrapper, 'next', i);
        		    		}
        		    	});
        		    });            
        		    
        		    // auto-animation
        		    if($G['autoanim'] == 1) {	                	
        		    	$G['animation_timer'] = setTimeout(function() {
        		    		gk_storefront_autoanimate($G, wrapper, 'next', null);
        		    	}, $G['anim_interval']);
        		    }
        		    
        		    // pagination
    		    	var slide_pos_start_x = 0;
    		    	var slide_pos_start_y = 0;
    		    	var slide_time_start = 0;
    		    	var slide_swipe = false;
    		    	
    		    	wrapper.addEvent('touchstart', function(e) {
    		    		slide_swipe = true;
    		    		
    		    		if(e.changedTouches.length > 0) {
    		    			slide_pos_start_x = e.changedTouches[0].pageX;
    		    			slide_pos_start_y = e.changedTouches[0].pageY;
    		    			slide_time_start = new Date().getTime();
    		    		}
    		    	});
    		    	
    		    	wrapper.addEvent('touchmove', function(e) {
    		    		if(e.changedTouches.length > 0 && slide_swipe) {
    		    			if(
    		    				Math.abs(e.changedTouches[0].pageX - slide_pos_start_x) > Math.abs(e.changedTouches[0].pageY - slide_pos_start_y)
    		    			) {
    		    				e.preventDefault();
    		    			} else {
    		    				slide_swipe = false;
    		    			}
    		    		}
    		    	});
    		    	
    		    	wrapper.addEvent('touchend', function(e) {
    		    		if(e.changedTouches.length > 0 && slide_swipe) {					
    		    			if(
    		    				Math.abs(e.changedTouches[0].pageX - slide_pos_start_x) >= swipe_min_move && 
    		    				new Date().getTime() - slide_time_start <= swipe_max_time
    		    			) {
    		    				if(e.changedTouches[0].pageX - slide_pos_start_x > 0) {
    		    					$G['blank'] = true;
    		    					gk_storefront_autoanimate($G, wrapper, 'prev', null);
    		    				} else {
    		    					$G['blank'] = true;
    		    					gk_storefront_autoanimate($G, wrapper, 'next', null);
    		    				}
    		    			}
    		    		}
    		    	});
	            }
	        }, 500);
	    });
    }, 1000);
});

var gk_storefront_animate = function($G, wrapper, imgPrev, imgNext) {	
	var prevfig = imgPrev.getElement('figcaption');
	//
	if(prevfig) {
		prevfig.set('morph', { duration: 150 });
		prevfig.morph({'opacity': [1, 0]});
	}
	//
	imgNext.setProperty('class', 'animated');
	//imgPrev.fade('out');
	new Fx.Tween(imgPrev, {
		duration: $G['anim_speed'],
		onComplete: function() {
			imgPrev.setProperty('class', '');
		}
	}).start('opacity', 0);
	//imgNext.fade('in');
	new Fx.Tween(imgNext, {
		duration: $G['anim_speed'],
		onComplete: function() {			
			imgNext.setProperty('class', 'active');
			
			setTimeout(function() {
				imgNext.setProperty('class', 'active activated');
			}, 50);
			
			var nextfig = imgNext.getElement('figcaption');
			if(nextfig) {
				nextfig.set('morph', { duration: 150 });
				nextfig.morph({'opacity': [0, 1]});
			}
			if($G['autoanim'] == 1) {
				clearTimeout($G['animation_timer']);
				
				$G['animation_timer'] = setTimeout(function() {
					if($G['blank']) {
						$G['blank'] = false;
						clearTimeout($G['animation_timer']);
						
						$G['animation_timer'] = setTimeout(function() {
							gk_storefront_autoanimate($G, wrapper, 'next', null);
						}, $G['anim_interval']);
					} else {
						gk_storefront_autoanimate($G, wrapper, 'next', null);
					}
				}, $G['anim_interval']);
			}
		} 
	}).start('opacity', 1);
}; 

var gk_storefront_autoanimate = function($G, wrapper, dir, next) {
	var i = $G['actual_slide'];
	var imgs = wrapper.getElements('figure');
	
	if(next == null) {
		next = (dir == 'next') ? ((i < imgs.length - 1) ? i+1 : 0) : ((i == 0) ? imgs.length - 1 : i - 1); // dir: next|prev
	}
	
	gk_storefront_animate($G, wrapper, imgs[i], imgs[next]);
	$G['actual_slide'] = next;
	
	wrapper.getElements('.gkIsPagination li').removeClass('active');
	wrapper.getElements('.gkIsPagination li')[next].addClass('active');
};