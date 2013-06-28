/*!
 * jQuery ajaxify plugin
 * Author: mehdi.lahlou@free.fr
 * Licensed under GPLv2 license
 */

;(function ( $, window, document, undefined ) {

    /* Create the defaults once */
    var pluginName = 'ajaxify',
        defaults = {
			baseurl: '',
			ajaxurl: '',
			container: '',
			links_selector: '',
			loading_container: '',
			loading_html: '',
			loading_test_mode: false,
			pre_code: '',
			post_code: ''
		};

    /* The actual plugin constructor */
    function Plugin( element, options ) {
        this.element = element;
        
       /* merge options with default options */
        this.options = $.extend( {}, defaults, options);
        
        this.properties = {
			anim_finished : 0,
			new_content : '',
			content_received : 0,
			cache : {},
			url: '',
			plugins: [],
			first: true
		};
		
		this.$container = $(this.options.container);
		this.$loading_container = $(this.options.loading_container);
        
        this._defaults = defaults;
        this._name = pluginName;
        
        this.init();
    }

    Plugin.prototype.init = function () {
        /* Place initialization logic here */
        /* You already have access to the DOM element and */
        /* the options via the instance, e.g. this.element */
        /* and this.options */
        console.log(this.options.baseurl);
		
		this.$container.addClass('wp-ajax-container');
		
		$.address.state('/');
		$.address.strict(false);
		
		/* Preserve context when $.address calls handler function */
		$.address.change(this.address.bind(this));
		
		$(this.options.links_selector).address();
    };

    /* A really lightweight plugin wrapper around the constructor, */
    /* preventing against multiple instantiations */
    $.fn[pluginName] = function ( options ) {
        return this.each(function () {
            if (!$.data(this, 'plugin_' + pluginName)) {
                $.data(this, 'plugin_' + pluginName, 
                new Plugin( this, options ));
            }
        });
    }
    
    Plugin.prototype.address = function(event) {
		if (event.value) {
			var url = event.value;

			/* Link clicked */
			if (url.startsWith(this.options.baseurl)) {
				url = url.substr(this.options.baseurl.length);
				if(url == '') {
					url = '/';
				}
				this.properties.first = false;
			};
			
			if(!this.properties.first && url) {
				this.properties.url = url;
				console.log(this.properties.url);
				if (this.options.pre_code != '') {
					eval(stripslashes(this.options.pre_code));
				}
				this.properties.anim_finished=0;
				
				this.$container.addClass('out');
				
				this.$container.one('webkitTransitionEnd mozTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend',this.addPreloader.bind(this));
				
				this.properties.content_received=0;
				this.loadContent();
			}
		}
	}
    
    Plugin.prototype.addPreloader = function () {
		$loading = $(stripslashes(this.options.loading_html));
		$loading.css('position', 'absolute');
		/* Centering element on container */
		$loading.css('left', (this.$loading_container.offset().left + (this.$loading_container.width() / 2) - ($loading.width() / 2))+'px');
		$loading.css('top', (this.$loading_container.offset().top + (this.$loading_container.height() / 2) - ($loading.height() / 2))+'px');
		$loading.prependTo(this.$loading_container);
		this.properties.anim_finished=1; if(this.properties.content_received) this.showContent(this.properties.new_content);
	}
	
	Plugin.prototype.loadContent = function () {
		if ( this.properties.cache[ this.properties.url ]) {
				/* Since the element is already in the cache, it doesn't need to be
				 created, so instead of creating it again, let's just show it! */
				if (this.properties.anim_finished) {
					this.showContent(this.properties.cache[ this.properties.url ].html);
				} else {
					this.properties.new_content=this.properties.cache[ this.properties.url ].html;
					this.properties.content_received=1;
				}
			} else {
				/* Loading animation test mode*/
				if(this.options.loading_test_mode==true) {
					return;
				}
			
				$.post(
					this.options.ajaxurl,
					{
						action : 'wp-ajax-submit-url',
						url : this.properties.url
					},
					this.processJSON.bind(this),
					"json"
				).error(function(xhr, ajaxOptions, thrownError) { if(xhr.status=='404') {result={html:thrownError};processJSON(result,url);} });
			}
	}
	
	Plugin.prototype.processJSON = function (result) {
		this.properties.cache[this.properties.url]=result;
		if(this.properties.anim_finished) {
			this.showContent(result.html);
		} else {
			this.properties.new_content = result.html;
			this.properties.content_received = 1;
		}
	}
	
	Plugin.prototype.showContent = function (html) {
		this.$container.html(html);
		
		if (this.options.post_code != '') {
			eval(stripslashes(this.options.post_code));
		}
		
		this.$container.find(this.options.links_selector).address();
		/*alterForms($container);
		bindForms($container);*/

		this.$loading_container.find('.wp-ajax-preloader').remove();

		this.$container.removeClass('out');
	}
	
	/* Utilities */
	
	/* Preserve 'this' context on function calls */
	if (!Function.prototype.bind) {  
	  Function.prototype.bind = function (oThis) {  
		if (typeof this !== "function") {  
		  // closest thing possible to the ECMAScript 5 internal IsCallable function  
		  throw new TypeError("Function.prototype.bind - what is trying to be bound is not callable");  
		}  

		var aArgs = Array.prototype.slice.call(arguments, 1),   
			fToBind = this,   
			fNOP = function () {},  
			fBound = function () {  
			  return fToBind.apply(this instanceof fNOP  
									 ? this  
									 : oThis || window,  
								   aArgs.concat(Array.prototype.slice.call(arguments)));  
			};  

		fNOP.prototype = this.prototype;  
		fBound.prototype = new fNOP();  

		return fBound;  
	  };  
	}
	
	/* 'External link' jQuery selector */ 
	$.expr[':'].external = function(obj){
		return !obj.href.match(/^mailto\:/)
				&& (obj.hostname != location.hostname);
	};
	
	/* String.startsWith function, for browsers that don't implement it */
	if (typeof String.prototype.startsWith != 'function') {
	  String.prototype.startsWith = function (str){
		return this.slice(0, str.length) == str;
	  };
	}
	
	/* stripslashes php equivalent */
	function stripslashes (str) {
		/* +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net) */
		/* +   improved by: Ates Goral (http://magnetiq.com) */
		/* +      fixed by: Mick@el */
		/* +   improved by: marrtins    // +   bugfixed by: Onno Marsman */
		/* +   improved by: rezna */
		/* +   input by: Rick Waldron */
		/* +   reimplemented by: Brett Zamir (http://brett-zamir.me) */
		/* +   input by: Brant Messenger (http://www.brantmessenger.com/)    // +   bugfixed by: Brett Zamir (http://brett-zamir.me) */
		/* *     example 1: stripslashes('Kevin\'s code'); */
		/* *     returns 1: "Kevin's code" */
		/* *     example 2: stripslashes('Kevin\\\'s code'); */
		/* *     returns 2: "Kevin\'s code"    return (str + '').replace(/\\(.?)/g, function (s, n1) { */
		return (str + '').replace(/\\(.?)/g, function (s, n1) {
			switch (n1) {
			case '\\':
				return '\\';
			case '0':            return '\u0000';
			case '':
				return '';
			default:
				return n1;        }
		});
	}
	
	/* Detect css transitions, to avoid modernizr */
	var Detect = (function () {
		function cssTransitions () {
			var div = document.createElement("div");
			var p, ext, pre = ["", "ms", "O", "Webkit", "Moz"];
			for (p in pre) {
			  if (div.style[ pre[p] + "Transition" ] !== undefined) {
				ext = pre[p];
				break;
			  }
			}
			delete div;
			return ext;
		};
		return {
			"cssTransitions" : cssTransitions
		};
	}());

})( jQuery, window, document );
