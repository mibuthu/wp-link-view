/*
 * 	Easy Slider 1.7 - jQuery plugin
 *	written by Alen Grakalic	
 *	http://cssglobe.com/post/4004/easy-slider-15-the-easiest-jquery-plugin-for-sliding
 *
 *	Copyright (c) 2009 Alen Grakalic (http://cssglobe.com)
 *	Dual licensed under the MIT (MIT-LICENSE.txt)
 *	and GPL (GPL-LICENSE.txt) licenses.
 *
 *	Built for jQuery library
 *	http://jquery.com
 *
 */
 
/*
 *	markup example for $("#slider").easySlider();
 *	
 * 	<div id="slider">
 *		<ul>
 *			<li><img src="images/01.jpg" alt="" /></li>
 *			<li><img src="images/02.jpg" alt="" /></li>
 *			<li><img src="images/03.jpg" alt="" /></li>
 *			<li><img src="images/04.jpg" alt="" /></li>
 *			<li><img src="images/05.jpg" alt="" /></li>
 *		</ul>
 *	</div>
 *
 */

(function($) {
	$.fn.easySlider = function(options){
		// default configuration properties
		var defaults = {
			prevId: 		'prevBtn',
			prevText: 		'Previous',
			nextId: 		'nextBtn',
			nextText: 		'Next',
			controlsShow:	true,
			controlsBefore:	'',
			controlsAfter:	'',
			controlsFade:	true,
			firstId: 		'firstBtn',
			firstText: 		'First',
			firstShow:		false,
			lastId: 		'lastBtn',
			lastText: 		'Last',
			lastShow:		false,
			vertical:		false,
			speed: 			800,
			auto:			false,
			pause:			2000,
			continuous:		false,
			numeric: 		false,
			numericId: 		'controls'
		};

		var options = $.extend(defaults, options);

		this.each(function() {
			var obj = jQuery(this);
			var s = jQuery("li", obj).length;
			var w = jQuery("li", obj).width();
			var h = jQuery("li", obj).height();
			var clickable = true;
			obj.width(w);
			obj.height(h);
			obj.css("overflow","hidden");
			var ts = s-1;
			var t = 0;
			jQuery("ul", obj).css('width',s*w);

			if(options.continuous){
				jQuery("ul", obj).prepend(jQuery("ul li:last-child", obj).clone().css("margin-left","-"+ w +"px"));
				jQuery("ul", obj).append(jQuery("ul li:nth-child(2)", obj).clone());
				jQuery("ul", obj).css('width',(s+1)*w);
			};

			if(!options.vertical) jQuery("li", obj).css('float','left');

			if(options.controlsShow){
				var html = options.controlsBefore;
				if(options.numeric){
					html += '<ol id="'+ options.numericId +'"></ol>';
				} else {
					if(options.firstShow) html += '<span id="'+ options.firstId +'"><a href=\"javascript:void(0);\">'+ options.firstText +'</a></span>';
					html += ' <span id="'+ options.prevId +'"><a href=\"javascript:void(0);\">'+ options.prevText +'</a></span>';
					html += ' <span id="'+ options.nextId +'"><a href=\"javascript:void(0);\">'+ options.nextText +'</a></span>';
					if(options.lastShow) html += ' <span id="'+ options.lastId +'"><a href=\"javascript:void(0);\">'+ options.lastText +'</a></span>';
				};
				html += options.controlsAfter;
				jQuery(obj).after(html);
			};

			if(options.numeric){
				for(var i=0;i<s;i++){
					jQuery(document.createElement("li"))
						.attr('id',options.numericId + (i+1))
						.html('<a rel='+ i +' href=\"javascript:void(0);\">'+ (i+1) +'</a>')
						.appendTo(jQuery("#"+ options.numericId))
						.click(function(){
							animate(jQuery("a",jQuery(this)).attr('rel'),true);
						});
				};
			} else {
				jQuery("a","#"+options.nextId).click(function(){
					animate("next",true);
				});
				jQuery("a","#"+options.prevId).click(function(){
					animate("prev",true);
				});
				jQuery("a","#"+options.firstId).click(function(){
					animate("first",true);
				});
				jQuery("a","#"+options.lastId).click(function(){
					animate("last",true);
				});
			};

			function setCurrent(i){
				i = parseInt(i)+1;
				jQuery("li", "#" + options.numericId).removeClass("current");
				jQuery("li#" + options.numericId + i).addClass("current");
			};

			function adjust(){
				if(t>ts) t=0;
				if(t<0) t=ts;
				if(!options.vertical) {
					jQuery("ul",obj).css("margin-left",(t*w*-1));
				} else {
					jQuery("ul",obj).css("margin-left",(t*h*-1));
				}
				clickable = true;
				if(options.numeric) setCurrent(t);
			};

			function animate(dir,clicked){
				if (clickable){
					clickable = false;
					var ot = t;
					switch(dir){
						case "next":
							t = (ot>=ts) ? (options.continuous ? t+1 : ts) : t+1;
							break;
						case "prev":
							t = (t<=0) ? (options.continuous ? t-1 : 0) : t-1;
							break;
						case "first":
							t = 0;
							break;
						case "last":
							t = ts;
							break;
						default:
							t = dir;
							break;
					};
					var diff = Math.abs(ot-t);
					var speed = diff*options.speed;
					if(!options.vertical) {
						p = (t*w*-1);
						jQuery("ul",obj).animate(
							{ marginLeft: p }, 
							{ queue:false, duration:speed, complete:adjust }
						);
					} else {
						p = (t*h*-1);
						jQuery("ul",obj).animate(
							{ marginTop: p }, 
							{ queue:false, duration:speed, complete:adjust }
						);
					};

					if(!options.continuous && options.controlsFade){
						if(t==ts){
							jQuery("a","#"+options.nextId).hide();
							jQuery("a","#"+options.lastId).hide();
						} else {
							jQuery("a","#"+options.nextId).show();
							jQuery("a","#"+options.lastId).show();
						};
						if(t==0){
							jQuery("a","#"+options.prevId).hide();
							jQuery("a","#"+options.firstId).hide();
						} else {
							jQuery("a","#"+options.prevId).show();
							jQuery("a","#"+options.firstId).show();
						};
					};

					if(clicked) clearTimeout(timeout);
					if(options.auto && dir=="next" && !clicked){;
						timeout = setTimeout(function(){
							animate("next",false);
						},diff*options.speed+options.pause);
					};
				};
			};
			// init
			var timeout;
			if(options.auto){;
				timeout = setTimeout(function(){
					animate("next",false);
				},options.pause);
			};
			if(options.numeric) setCurrent(0);

			if(!options.continuous && options.controlsFade){
				jQuery("a","#"+options.prevId).hide();
				jQuery("a","#"+options.firstId).hide();
			};
		});
	};
})(jQuery);
