/**
 * @package    HikaShop for Joomla!
 * @version    2.3.1
 * @author     hikashop.com
 * @copyright  (C) 2010-2014 HIKARI SOFTWARE. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
var hikashop_ratings = new Class({
	Implements : Options,
	options : {
		showSelectBox : false,
		container : null,
		defaultRating : null,
		id : 'hikashop_vote_'
	},
	selectBox : null,
	container : null,
	initialize : function(selectBox, options) {
		// set the custom options
		this.setOptions(options);
		// set the selectbox
		this.selectBox = $(selectBox);
		// hide the selectbox
		if (!this.options.showSelectBox && selectBox.nodeName.toLowerCase() == 'select' && typeof(jQuery)!='undefined' && jQuery().chosen) {
			setTimeout(function(){
				var id = selectBox.getAttribute('id') + '_chzn';
				//this.selectBox.setStyle('display', 'none');
				if(document.getElementById(id) != null) {
					document.id(id).dispose();
					try{ jQuery(id+'-chzn').remove(); }catch(e){}
				}
			}, 50);
		}
		// set the container
		this.setContainer();
		// add stars
		var max = selectBox.getAttribute('data-max');
		if(max) {
			for(var i = 1; i <= max; i++)
				this.createStar(null, i);
		} else
			this.selectBox.getElements('option').each(this.createStar.bind(this));

		// bind events
		this.container.addEvents({
			mouseover : this.mouseOver.bind(this),
			mouseout : this.mouseOut.bind(this),
			click : this.click.bind(this)
		});
		// bind change event for selectbox if shown
		if (this.options.showSelectBox) {
			this.selectBox.addEvent('change', this.change.bind(this));
		}
		// set the initial rating
		this.setRating(this.options.defaultRating);
	},

	// set the container from options or create default
	setContainer : function() {
		if (document.getElementById(this.options.container)) {
			this.container = document.getElementById(this.options.container);
			return;
		}
		this.createContainer();
	},

	// create the html container for the rating stars
	createContainer : function() {
		this.container = new Element('div', {
			'class': 'ui-rating'
		}).inject(this.selectBox, 'after');

		//this.container = document.getElementById("hikashop_vote_stars").innerHTML = "<div id='ui-rating' class='ui-rating'></div>" ;
	},
	// create the html rating stars
	createStar : function(el, value) {
		if(el)
			value = el.getAttribute('value');
		var e = new Element('a', {
			id : this.options.id + '_' + value,
			'class' : 'ui-rating-star ui-rating-empty',
			title : '' + value,
			value : value
		});
		e.inject(this.container);
	},
	// handle mouseover event
	mouseOver : function(e) {
		if (!e.target)
			e.target = e.srcElement;
		$(e.target).addClass('ui-rating-hover');
		var c = $(e.target).getPrevious();
		while(c) {
			c.addClass('ui-rating-hover');
			c = c.getPrevious();
		}
	},
	// handle mouseout event
	mouseOut : function(e) {
		if (!e.target)
			e.target = e.srcElement;
		$(e.target).removeClass('ui-rating-hover');
		var c = $(e.target).getPrevious();
		while(c) {
			c.removeClass('ui-rating-hover');
			c = c.getPrevious();
		}
	},
	// handle click event
	click : function(e) {
		if (!e.target)
			e.target = e.srcElement;
		var rating = e.target.getAttribute('title').replace('', '');
		var from = this.selectBox.getAttribute('id');
		this.setRating(rating);
		this.selectBox.set({value: rating});
		//send the id of the view which send the vote ( mini / form )
		hikashop_send_vote(rating, from);
	},
	// handle change event
	change : function(e) {
		var rating = $(e.target).get('value');
		this.setRating(rating);
	},
	// set the current rating
	setRating : function(rating) {
		// use selected rating if none supplied
		if (!rating) {
			rating = this.selectBox.getAttribute('value');
			// use first rating option if none selected
			if (!rating) {
				//rating = this.selectBox.getElement('option[value!=]').getAttribute('value');
				rating = 0;
			}
		}
		// get the current selected rating star
		var current = this.container.getElement('a[title=' + rating + ']');

		// highlight current and previous stars in yellow
		if(current && rating != 0) {
			current.set({'class': 'ui-rating-star ui-rating-full'});
			var c = current.getPrevious();
			while(c) {
				c.set({'class': 'ui-rating-star ui-rating-full'});
				c = c.getPrevious();
			}

			// remove highlight from higher ratings
			var c = current.getNext();
			while(c) {
				c.set({'class': 'ui-rating-star ui-rating-empty'});
				c = c.getNext();
			}
		}
		// synchronize the rate with the selectbox
		this.selectBox.set({value: rating});
	}
});
if(MooTools.version == '1.12') {
	hikashop_ratings.implement(new Options);
}

/* Vote initialization */
window.hikashop.ready(function(){
	var d = document, el = null, r = null;
	var voteContainers = d.getElementsByName('hikashop_vote_rating');
	if(voteContainers.length == 0)
		return;
	for(var i=0; i < voteContainers.length; i++) {
		el = d.getElementById(voteContainers[i].id);
		if(!el.getAttribute("data-type"))
			continue;
		r = new hikashop_ratings(el,{
			id : 'hikashop_vote_rating_'+el.getAttribute("data-type")+'_'+el.getAttribute("data-ref"),
			showSelectBox : false,
			container : null,
			defaultRating :  el.getAttribute("data-rate")
		});
	}
});
