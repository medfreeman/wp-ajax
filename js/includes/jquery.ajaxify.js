/*!
 * jQuery ajaxify plugin
 * Author: mehdi.lahlou@free.fr
 * Licensed under GPLv2 license
 * Requires jquery v1.7.2 min (http://www.jquery.com), jquery address v1.5 min (http://www.asual.com/jquery/address/), imagesloaded v3.0.1 min (https://github.com/desandro/imagesloaded), transition-events v0.2.1 min (https://github.com/ai/transition-events)
 */

;(function ( $, window, document, undefined ) {

    /* Create the defaults once */
    var pluginName = 'ajaxify',
        defaults = {
			baseurl: '',
			ajaxurl: '',
			container: '',
			container_class: '',
			links_selector: '',
			loading_container: '',
			loading_html: '',
			loading_position_container: '',
			loading_position: '',
			loading_test_mode: false,
			no_cache: new Array()
		};

    /* The actual plugin constructor */
    function Plugin( element, options, callback ) {
        this.element = element;
        
       /* merge options with default options */
        this.options = $.extend( {}, defaults, options);
        
        if(typeof(callback) != 'function') {
			this.callback = $.noop();
		} else {
			this.callback = callback;
		}
        
        this.properties = {
			anim_finished : 0,
			new_content : '',
			content_received : 0,
			cache : {},
			url: '',
			plugins: [],
			first: 2,
			processing: false,
			last_url: '',
			prefix: this.options.baseurl.split(window.location.hostname)[1]
		};
		
		this.$container = $(this.options.container);
		this.$loading_container = $(this.options.loading_container);
		this.$loading = $([]);
		
		this.initialCachingFunctions = [];
		this.beforeLoadFunctions = [];
		this.postDataFunctions = [];
		this.processFunctions = [];
		this.afterFunctions = [];
        
        this._defaults = defaults;
        this._name = pluginName;
        
        this.init();
    }

    Plugin.prototype.init = function () {
        /* Place initialization logic here */
        /* You already have access to the DOM element and */
        /* the options via the instance, e.g. this.element */
        /* and this.options */
		
		this.$container.addClass(this.options.container_class);
		if (Detect.css3dTransforms) {
			this.$container.css('-webkit-transform', 'translate3d(0,0,0)');
			this.$container.css('-moz-transform', 'translate3d(0,0,0)');
			this.$container.css('-ms-transform', 'translate3d(0,0,0)');
			this.$container.css('transform', 'translate3d(0,0,0)');
		}
		
		if(!Detect.pushState) {
			return;
		}
		
		$.address.state('/');
		$.address.strict(false);
		
		/* Preserve context when $.address calls handler function */
		/* TODO: Unbind address inside content after changing content */
		$.address.change(this.address.bind(this));
		
		$(this.options.links_selector).address();
		
		if(this.callback !== $.noop()) {
			if(!Detect.cssTransitions || !Detect.cssAnimations) {
				this.callback(Detect.cssTransitions, Detect.cssAnimations);
			}
		}
    };

    /* A really lightweight plugin wrapper around the constructor, */
    /* preventing against multiple instantiations */
    $.fn[pluginName] = function ( options, callback ) {
		if (typeof arguments[0] === 'string' && typeof arguments[1] === 'object' && arguments[0] === 'addPlugin') {
		  var methodName = arguments[0];
		  var args = Array.prototype.slice.call(arguments, 1);
		  var returnVal;
		  this.each(function() {
			/* Check that the element has a plugin instance, and that */
			/* the requested public method exists. */
			if ($.data(this, 'plugin_' + pluginName) && typeof $.data(this, 'plugin_' + pluginName)[methodName] === 'function') {
			  /* Call the method of the Plugin instance, and Pass it */
			  /* the supplied arguments. */
			  /*pluginMethod = $.data(this, 'plugin_' + pluginName)[methodName];*/
			  returnVal = $.data(this, 'plugin_' + pluginName)[methodName].apply($.data(this, 'plugin_' + pluginName), args);
			} else {
			  throw new Error('Method ' +  methodName + ' does not exist on jQuery.' + pluginName);
			}
		  });
		  if (returnVal !== undefined){
			/* If the method returned a value, return the value. */
			return returnVal;
		  } else {
			/* Otherwise, returning 'this' preserves chainability. */
			return this;
		  }
		/* If the first parameter is an object (options), or was omitted, */
		/* instantiate a new instance of the plugin. */
		} else if (typeof options === "object" || !options) {
			return this.each(function () {
				if (!$.data(this, 'plugin_' + pluginName)) {
					$.data(this, 'plugin_' + pluginName, 
					new Plugin( this, options, callback ));
				}
			});
		}
    }
    
    Plugin.prototype.relativeUrl = function(url) {
		/* Absolute Link clicked */
		if (url.startsWith(this.options.baseurl)) {
			url = url.substr(this.options.baseurl.length);
		};
		if(url.startsWith(this.properties.prefix)) {
			url = url.substr(this.properties.prefix.length);
		}
		if(url === '') {
			url = '/';
		} 
		if (url !== '/' && url.startsWith('/')) {
			url = url.substr(1);
		}
		return url;
	}
    
    Plugin.prototype.address = function(event) {
		if (event.value) {
			var url = this.relativeUrl(event.value);
			
			if(this.properties.first === 2) {

				var args = { html: this.$container.html() }
				for(var i=0;i<this.initialCachingFunctions.length;i++) {
					args = $.extend({}, args, this.initialCachingFunctions[i]());
				}
				
				if($.inArray(url, this.options.no_cache) < 0) {
					this.properties.cache[ url ] = args;
				}
				
				this.properties.first = 1;
			} else if(this.properties.first === 1) {
				var currentUrl = this.relativeUrl(window.location.href);
				if(url !== currentUrl) {
					this.properties.first = 0;
				}
			}
			
			if (this.properties.processing) {
				this.properties.last_url = url;
			} else if (!this.properties.processing && !this.properties.first && url) {
				this.properties.processing = true;
				
				this.properties.url = url;
				
				this.properties.anim_finished=0;
				
				this.$container.afterTransition(this.addPreloader.bind(this));
				
				this.$container.addClass('out');
				
				this.properties.content_received=0;
				this.loadContent.bind(this);
				this.loadContent();
			}
		}
	}
    
    Plugin.prototype.addPreloader = function () {
		
		for(var i=0;i<this.beforeLoadFunctions.length;i++) {
			this.beforeLoadFunctions[i]();
		}
		
		this.$loading = $(stripslashes(this.options.loading_html));
		this.$loading.css('position', 'absolute');
		this.$loading.css('z-index', '9999');
		
		this.$loading.hide();
		this.$loading.prependTo(this.$loading_container);
		
		this.$loading.css('transform', 'translate3d(0,0,0)');
		
		$loading_position_container = $(this.options.loading_position_container);
		if($loading_position_container.length === 0) {
			$loading_position_container = this.$container;
		}
		
		var pos = new Array();
		pos = this.options.loading_position.split("-");
		
		/* y pos */
		var ypos = 0;
		switch (pos[0]) {
			case 'top':
				ypos = $loading_position_container.offset().top;
			break;
			case 'center':
			default: 
				ypos = $loading_position_container.offset().top + ($loading_position_container.height() / 2) - (this.$loading.height() / 2);
			break;
			case 'bottom':
				ypos = $loading_position_container.offset().top + $loading_position_container.height() - this.$loading.height();
			break;
		}
		
		/* x pos */
		var xpos = 0;
		switch (pos[1]) {
			case 'left':
				xpos = $loading_position_container.offset().left;
			break;
			case 'center':
			default: 
				xpos = $loading_position_container.offset().left + ($loading_position_container.width() / 2) - (this.$loading.width() / 2);
			break;
			case 'right':
				xpos = $loading_position_container.offset().left + $loading_position_container.width() - this.$loading.width();
			break;
		}
		
		/* Centering element on container */
		this.$loading.css('left', xpos + 'px');
		this.$loading.css('top', ypos + 'px');
		
		this.$loading.show();
		
		this.properties.anim_finished=1; if(this.properties.content_received) this.showContent(this.properties.new_content);
	}
	
	Plugin.prototype.loadContent = function () {
		if ( this.properties.cache[ this.properties.url ]) {
			/* Since the element is already in the cache, it doesn't need to be
			 created, so instead of creating it again, let's just show it! */
			if (this.properties.anim_finished) {
				this.showContent(this.properties.cache[ this.properties.url ]);
			} else {
				this.properties.new_content=this.properties.cache[ this.properties.url ];
				this.properties.content_received=1;
			}
		} else {
			/* Loading animation test mode*/
			if(this.options.loading_test_mode == true) {
				return;
			}
			
			args = { action : 'wp-ajax-submit-url', url : encodeURI(this.properties.url) };
			
			for(var i=0;i<this.postDataFunctions.length;i++) {
				args = $.extend({}, args, this.postDataFunctions[i]());
			}
			
			$.post(
				this.options.ajaxurl,
				args,
				this.processJSON.bind(this),
				"json"
			).error(this.processError.bind(this));
		}
	}
	
	Plugin.prototype.processError = function(xhr, ajaxOptions, thrownError) {
		if(xhr.status=='404') {
			result={html:xhr.responseText.html};
			this.processJSON.bind(this);
			this.processJSON(result);
		}
	}
	
	Plugin.prototype.processJSON = function (result) {
		if($.inArray(this.properties.url, this.options.no_cache) < 0) {
			this.properties.cache[this.properties.url]=result;
		}
		if(this.properties.anim_finished) {
			this.showContent(result);
		} else {
			this.properties.new_content = result;
			this.properties.content_received = 1;
		}
	}
	
	Plugin.prototype.showContent = function (result) {
		this.$container.html(result.html);
		
		for(var i=0;i<this.processFunctions.length;i++) {
			this.processFunctions[i](result);
		}
		
		this.$container.find(this.options.links_selector).address();
		/*alterForms($container);
		bindForms($container);*/

		this.$loading.remove();
		
		/* OPTIMIZE: Permit two-ways animation / in - out */
		this.$container.imagesLoaded(this.transitionOut.bind(this));
	}
	
	Plugin.prototype.transitionOut = function () {
		this.$container.afterTransition(this.afterRender.bind(this));
		this.$container.removeClass('out');
	}
	
	Plugin.prototype.afterRender = function () {
		for(var i=0;i<this.afterFunctions.length;i++) {
			this.afterFunctions[i]();
		}
		
		this.properties.processing = false;
		
		if(this.properties.last_url !== '') {
			this.address.bind(this);
			this.address({value: this.properties.last_url});
			this.properties.last_url = '';
		}
	}
	
	Plugin.prototype.addPlugin = function (plugin) {
		if (typeof plugin.firstCaching === 'function') {
			this.initialCachingFunctions.push(plugin.firstCaching);
		}
		if (typeof plugin.beforeLoad === 'function') {
			this.beforeLoadFunctions.push(plugin.beforeLoad);
		}
		if (typeof plugin.postParams === 'function') {
			this.postDataFunctions.push(plugin.postParams);
		}
		if (typeof plugin.process === 'function') {
			this.processFunctions.push(plugin.process);
		}
		if (typeof plugin.afterRender === 'function') {
			this.afterFunctions.push(plugin.afterRender);
		}
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
		return (typeof obj.href !== 'undefined') && !obj.href.match(/^mailto\:/)
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
		function cssProperty(name) {
			var div = document.createElement("div");
			var p, ext, pre = ["", "ms", "O", "Webkit", "Moz", "Khtml"];
			for (p in pre) {
			  if (div.style[ pre[p] + name ] !== undefined) {
				ext = pre[p];
				break;
			  }
			}
			delete div;
			return ext;
		};
		function has3d() {
			var el = document.createElement('p'), 
				has3d,
				transforms = {
					'webkitTransform':'-webkit-transform',
					'OTransform':'-o-transform',
					'msTransform':'-ms-transform',
					'MozTransform':'-moz-transform',
					'transform':'transform'
				};

			// Add it to the body to get the computed style.
			document.body.insertBefore(el, null);

			for (var t in transforms) {
				if (el.style[t] !== undefined) {
					el.style[t] = "translate3d(1px,1px,1px)";
					has3d = window.getComputedStyle(el).getPropertyValue(transforms[t]);
				}
			}

			document.body.removeChild(el);

			return (has3d !== undefined && has3d.length > 0 && has3d !== "none");
		};
		return {
			"cssTransitions" : Modernizr.csstransitions,
			"cssAnimations" : Modernizr.cssanimations,
			"css3dTransforms" : has3d(),
			"pushState" : Modernizr.history
		};
	}());

})( jQuery, window, document );
