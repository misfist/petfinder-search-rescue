/*!
 * Shuffle.js by @Vestride
 * Categorize, sort, and filter a responsive grid of items.
 * Dependencies: jQuery 1.9+, Modernizr 2.6.2+
 * @license MIT license
 * @version 2.1.0
 */
 
(function (factory) {
  if (typeof define === 'function' && define.amd) {
    define(['jquery', 'modernizr'], factory);
  } else {
    factory(window.jQuery, window.Modernizr);
  }
})(function(jQuery, Modernizr, undefined) {

'use strict';


// Validate Modernizr exists.
// Shuffle requires `csstransitions`, `csstransforms`, `csstransforms3d`,
// and `prefixed` to exist on the Modernizr object.
if (typeof Modernizr !== 'object') {
  throw new Error('Shuffle.js requires Modernizr.\n' +
      'http://vestride.github.io/Shuffle/#dependencies');
}

// Used for unique instance variables
var id = 0;


// Underscore's throttle function.
function throttle(func, wait, options) {
  var context, args, result;
  var timeout = null;
  var previous = 0;
  options = options || {};
  var later = function() {
    previous = options.leading === false ? 0 : jQuery.now();
    timeout = null;
    result = func.apply(context, args);
    context = args = null;
  };
  return function() {
    var now = jQuery.now();
    if (!previous && options.leading === false) previous = now;
    var remaining = wait - (now - previous);
    context = this;
    args = arguments;
    if (remaining <= 0 || remaining > wait) {
      clearTimeout(timeout);
      timeout = null;
      previous = now;
      result = func.apply(context, args);
      context = args = null;
    } else if (!timeout && options.trailing !== false) {
      timeout = setTimeout(later, remaining);
    }
    return result;
  };
}


/**
 * Returns css prefixed properties like `-webkit-transition` or `box-sizing`
 * from `transition` or `boxSizing`, respectively.
 * @param {(string|boolean)} prop Property to be prefixed.
 * @return {string} The prefixed css property.
 */
function dashify( prop ) {
  if (!prop) {
    return '';
  }

  // Replace upper case with dash-lowercase,
  // then fix ms- prefixes because they're not capitalized.
  return prop.replace(/([A-Z])/g, function( str, m1 ) {
    return '-' + m1.toLowerCase();
  }).replace(/^ms-/,'-ms-');
}

// Constant, prefixed variables.
var TRANSITION = Modernizr.prefixed('transition');
var TRANSITION_DELAY = Modernizr.prefixed('transitionDelay');
var TRANSITION_DURATION = Modernizr.prefixed('transitionDuration');
var TRANSITIONEND = {
  'WebkitTransition' : 'webkitTransitionEnd',
  'transition' : 'transitionend'
}[ TRANSITION ];
var TRANSFORM = Modernizr.prefixed('transform');
var CSS_TRANSFORM = dashify(TRANSFORM);

// Constants
var CAN_TRANSITION_TRANSFORMS = Modernizr.csstransforms && Modernizr.csstransitions;
var HAS_TRANSFORMS_3D = Modernizr.csstransforms3d;
var SHUFFLE = 'shuffle';
var ALL_ITEMS = 'all';
var FILTER_ATTRIBUTE_KEY = 'groups';


/**
 * Categorize, sort, and filter a responsive grid of items.
 *
 * @param {Element|jQuery} element An element or a jQuery collection which
 *     is the parent container for the grid items.
 * @param {Object} [options=Shuffle.options] Options object.
 * @constructor
 */
var Shuffle = function( element, options ) {
  options = options || {};
  jQuery.extend( this, Shuffle.options, options, Shuffle.settings );

  this.jQueryel = jQuery(element);
  this.jQuerywindow = jQuery(window);
  this.unique = 'shuffle_' + id++;

  this._fire( Shuffle.EventType.LOADING );
  this._init();

  // Dispatch the done event asynchronously so that people can bind to it after
  // Shuffle has been initialized.
  setTimeout( jQuery.proxy( this._fire, this, Shuffle.EventType.DONE ), 16 );
};


/**
 * Events the container element emits with the .shuffle namespace.
 * For example, "done.shuffle".
 * @enum {string}
 */
Shuffle.EventType = {
  LOADING: 'loading',
  DONE: 'done',
  SHRINK: 'shrink',
  SHRUNK: 'shrunk',
  FILTER: 'filter',
  FILTERED: 'filtered',
  SORTED: 'sorted',
  LAYOUT: 'layout',
  REMOVED: 'removed'
};


Shuffle.prototype = {

  _init : function() {
    var self = this,
        containerCSS,
        containerWidth,
        resizeFunction = jQuery.proxy( self._onResize, self ),
        debouncedResize = self.throttle ?
            self.throttle( resizeFunction, self.throttleTime ) :
            resizeFunction,
        sort = self.initialSort ? self.initialSort : null;

    // Save variables needed later then add some classes
    self._setVars();

    // Zero out all columns
    self._resetCols();

    // Add classes and invalidate styles
    self._addClasses();

    // Set initial css for each item
    self._initItems();

    // Bind resize events (http://stackoverflow.com/questions/1852751/window-resize-event-firing-in-internet-explorer)
    self.jQuerywindow.on('resize.' + SHUFFLE + '.' + self.unique, debouncedResize);

    // Get container css all in one request. Causes reflow
    containerCSS = self.jQueryel.css(['paddingLeft', 'paddingRight', 'position']);
    containerWidth = self._getOuterWidth( self.jQueryel[0] );

    // Position cannot be static.
    if ( containerCSS.position === 'static' ) {
      self.jQueryel[0].style.position = 'relative';
    }

    // Get offset from container
    self.offset = {
      left: parseInt( containerCSS.paddingLeft, 10 ) || 0,
      top: parseInt( containerCSS.paddingTop, 10 ) || 0
    };

    // We already got the container's width above, no need to cause another reflow getting it again...
    // Calculate the number of columns there will be
    self._setColumns( parseInt( containerWidth, 10 ) );

    // Kick off!
    self.shuffle( self.group, sort );

    // The shuffle items haven't had transitions set on them yet
    // so the user doesn't see the first layout. Set them now that the first layout is done.
    if ( self.supported ) {
      setTimeout(function() {
        self._setTransitions();
        self.jQueryel[0].style[ TRANSITION ] = 'height ' + self.speed + 'ms ' + self.easing;
      }, 0);
    }
  },

  // Will invalidate styles
  _addClasses : function() {
    this.jQueryel.addClass( SHUFFLE );
    this.jQueryitems.addClass('shuffle-item filtered');
  },

  _setVars : function() {
    var self = this,
        columnWidth = self.columnWidth;

    self.jQueryitems = self._getItems();

    // If the columnWidth property is a function, then the grid is fluid
    self.isFluid = columnWidth && jQuery.isFunction(self.columnWidth);

    // Column width is the default setting and sizer is not (meaning passed in)
    // Assume they meant column width to be the sizer
    if ( columnWidth === 0 && self.sizer !== null ) {
      columnWidth = self.sizer;
    }

    // If column width is a string, treat is as a selector and search for the
    // sizer element within the outermost container
    if ( typeof columnWidth === 'string' ) {
      self.jQuerysizer = self.jQueryel.find( columnWidth );

    // Check for an element
    } else if ( columnWidth && columnWidth.nodeType && columnWidth.nodeType === 1 ) {
      // Wrap it in jQuery
      self.jQuerysizer = jQuery( columnWidth );

    // Check for jQuery object
    } else if ( columnWidth && columnWidth.jquery ) {
      self.jQuerysizer = columnWidth;
    }

    if ( self.jQuerysizer && self.jQuerysizer.length ) {
      self.useSizer = true;
      self.sizer = self.jQuerysizer[0];
    }
  },


  /**
   * Filter the elements by a category.
   * @param {string} [category] Category to filter by. If it's given, the last
   *     category will be used to filter the items.
   * @param {jQuery} [jQuerycollection] Optionally filter a collection. Defaults to
   *     all the items.
   * @return {jQuery} Filtered items.
   */
  _filter : function( category, jQuerycollection ) {
    var self = this,
        isPartialSet = jQuerycollection !== undefined,
        jQueryitems = isPartialSet ? jQuerycollection : self.jQueryitems,
        jQueryfiltered = jQuery();

    category = category || self.lastFilter;

    self._fire( Shuffle.EventType.FILTER );

    // Loop through each item and use provided function to determine
    // whether to hide it or not.
    if ( jQuery.isFunction( category ) ) {
      jQueryitems.each(function() {
        var jQueryitem = jQuery(this);
        if ( category.call(jQueryitem[0], jQueryitem, self) ) {
          jQueryfiltered = jQueryfiltered.add( jQueryitem );
        }
      });

    // Otherwise we've been passed a category to filter by
    } else {
      self.group = category;

      // category === 'all', add filtered class to everything
      if ( category === ALL_ITEMS ) {
        jQueryfiltered = jQueryitems;

      // Check each element's data-groups attribute against the given category.
      } else {
        jQueryitems.each(function() {
          var jQueryitem = jQuery(this),
              groups = jQueryitem.data( FILTER_ATTRIBUTE_KEY ),
              keys = self.delimeter && !jQuery.isArray( groups ) ?
                groups.split( self.delimeter ) :
                groups;

          if ( jQuery.inArray(category, keys) > -1 ) {
            jQueryfiltered = jQueryfiltered.add( jQueryitem );
          }
        });
      }
    }

    // Individually add/remove concealed/filtered classes
    self._toggleFilterClasses( jQueryitems, jQueryfiltered );

    jQueryitems = null;
    jQuerycollection = null;

    return jQueryfiltered;
  },


  _toggleFilterClasses : function( jQueryitems, jQueryfiltered ) {
    var concealed = 'concealed',
        filtered = 'filtered';

    jQueryitems.filter( jQueryfiltered ).each(function() {
      var jQueryfilteredItem = jQuery(this);
      // Remove concealed if it's there
      if ( jQueryfilteredItem.hasClass( concealed ) ) {
        jQueryfilteredItem.removeClass( concealed );
      }
      // Add filtered class if it's not there
      if ( !jQueryfilteredItem.hasClass( filtered ) ) {
        jQueryfilteredItem.addClass( filtered );
      }
    });

    jQueryitems.not( jQueryfiltered ).each(function() {
      var jQueryfilteredItem = jQuery(this);
      // Add concealed if it's not there
      if ( !jQueryfilteredItem.hasClass( concealed ) ) {
        jQueryfilteredItem.addClass( concealed );
      }
      // Remove filtered class if it's there
      if ( jQueryfilteredItem.hasClass( filtered ) ) {
        jQueryfilteredItem.removeClass( filtered );
      }
    });
  },

  /**
   * Set the initial css for each item
   * @param {jQuery} [jQueryitems] Optionally specifiy at set to initialize
   * @return {jQuery} The items which were just set
   */
  _initItems : function( jQueryitems ) {
    jQueryitems = jQueryitems || this.jQueryitems;
    return jQueryitems.css( this.itemCss );
  },

  _updateItemCount : function() {
    this.visibleItems = this.jQueryitems.filter('.filtered').length;
    return this;
  },

  _setTransition : function( element ) {
    var self = this;
    element.style[ TRANSITION ] = CSS_TRANSFORM + ' ' + self.speed + 'ms ' + self.easing + ', opacity ' + self.speed + 'ms ' + self.easing;
    return element;
  },

  _setTransitions : function( jQueryitems ) {
    var self = this;

    jQueryitems = jQueryitems || self.jQueryitems;
    jQueryitems.each(function() {
      self._setTransition( this );
    });
    return self;
  },

  _setSequentialDelay : function( jQuerycollection ) {
    var self = this;

    if ( !self.supported ) {
      return;
    }

    // jQuerycollection can be an array of dom elements or jquery object
    jQuery.each( jQuerycollection, function(i) {
      // This works because the transition-property: transform, opacity;
      this.style[ TRANSITION_DELAY ] = '0ms,' + ((i + 1) * self.sequentialFadeDelay) + 'ms';

      // Set the delay back to zero after one transition
      jQuery(this).one(TRANSITIONEND, function() {
        this.style[ TRANSITION_DELAY ] = '0ms';
      });
    });
  },

  _getItems : function() {
    return this.jQueryel.children( this.itemSelector );
  },

  _getPreciseDimension : function( element, style ) {
    var dimension;
    if ( window.getComputedStyle ) {
      dimension = window.getComputedStyle( element, null )[ style ];
    } else {
      dimension = jQuery( element ).css( style );
    }
    return parseFloat( dimension );
  },

  _getOuterWidth: function( element, includeMargins ) {
    var width = element.offsetWidth;

    if (includeMargins) {
      var marginLeft = Math.round(parseFloat(element.style.marginLeft)) || 0;
      var marginRight = Math.round(parseFloat(element.style.marginRight)) || 0;
      width += marginLeft + marginRight;
    }

    return width;
  },


  _getColumnSize: function( gutterSize, containerWidth ) {
    var size;

    // Use fluid columnWidth function if there
    if (this.isFluid) {
      size = this.columnWidth(containerWidth);

    // columnWidth option isn't a function, are they using a sizing element?
    } else if (this.useSizer) {
      size = this._getPreciseDimension(this.sizer, 'width');

    // if not, how about the explicitly set option?
    } else if (this.columnWidth) {
      size = this.columnWidth;

    // or use the size of the first item
    } else if (this.jQueryitems.length > 0) {
      size = this._getOuterWidth(this.jQueryitems[0], true);

    // if there's no items, use size of container
    } else {
      size = containerWidth;
    }

    // Don't let them set a column width of zero.
    if ( size === 0 ) {
      size = containerWidth;
    }

    // return Math.round(size + gutterSize);
    return size + gutterSize;
  },


  _getGutterSize: function(containerWidth) {
    var size;
    if (jQuery.isFunction(this.gutterWidth)) {
      size = this.gutterWidth(containerWidth);
    } else if (this.useSizer) {
      size = this._getPreciseDimension(this.sizer, 'marginLeft');
    } else {
      size = this.gutterWidth;
    }

    return size;
  },


  /**
   * Calculate the number of columns to be used. Gets css if using sizer element.
   * @param {number} [theContainerWidth] Optionally specify a container width if it's already available.
   */
  _setColumns : function( theContainerWidth ) {
    var containerWidth = theContainerWidth || this._getOuterWidth(this.jQueryel[0]);
    var gutter = this._getGutterSize(containerWidth);
    var columnWidth = this._getColumnSize(gutter, containerWidth);
    var calculatedColumns = (containerWidth + gutter) / columnWidth;

    // Widths given from getComputedStyle are not precise enough...
    if ( Math.abs(Math.round(calculatedColumns) - calculatedColumns) < 0.03 ) {
      // e.g. calculatedColumns = 11.998876
      calculatedColumns = Math.round( calculatedColumns );
    }

    this.cols = Math.max( Math.floor(calculatedColumns), 1 );
    this.containerWidth = containerWidth;
    this.colWidth = columnWidth;
  },

  /**
   * Adjust the height of the grid
   */
  _setContainerSize : function() {
    this.jQueryel.css( 'height', Math.max.apply( Math, this.colYs ) );
  },

  /**
   * Fire events with .shuffle namespace
   */
  _fire : function( name, args ) {
    this.jQueryel.trigger( name + '.' + SHUFFLE, args && args.length ? args : [ this ] );
  },


  /**
   * Loops through each item that should be shown
   * Calculates the x and y position and then transitions it
   * @param {Array.<Element>} items Array of items that will be shown/layed out in order in their array.
   *     Because jQuery collection are always ordered in DOM order, we can't pass a jq collection
   * @param {function} complete callback function
   * @param {boolean} isOnlyPosition set this to true to only trigger positioning of the items
   * @param {boolean} isHide
   */
  _layout: function( items, fn, isOnlyPosition, isHide ) {
    var self = this;

    fn = fn || self._filterEnd;

    self.layoutTransitionEnded = false;
    jQuery.each(items, function(index, item) {
      var jQuerythis = jQuery(item),
          brickWidth = self._getOuterWidth(item, true),
          columnSpan = brickWidth / self.colWidth;

      // If the difference between the rounded column span number and the
      // calculated column span number is really small, round the number to
      // make it fit.
      if ( Math.abs(Math.round(columnSpan) - columnSpan) < 0.03 ) {
        // e.g. columnSpan = 4.0089945390298745
        columnSpan = Math.round( columnSpan );
      }

      // How many columns does this brick span. Ensure it's not more than the
      // amount of columns in the whole layout.
      var colSpan = Math.min( Math.ceil(columnSpan), self.cols );

      // The brick is only one column.
      if ( colSpan === 1 ) {
        self._placeItem( jQuerythis, self.colYs, fn, isOnlyPosition, isHide );

      // The brick spans more than one column, figure out how many different
      // places could this brick fit horizontally
      } else {
        var groupCount = self.cols + 1 - colSpan,
            groupY = [],
            groupColY,
            i;

        // for each group potential horizontal position
        for ( i = 0; i < groupCount; i++ ) {
          // make an array of colY values for that one group
          groupColY = self.colYs.slice( i, i + colSpan );
          // and get the max value of the array
          groupY[i] = Math.max.apply( Math, groupColY );
        }

        self._placeItem( jQuerythis, groupY, fn, isOnlyPosition, isHide );
      }
    });

    // `_layout` always happens after `_shrink`, so it's safe to process the style
    // queue here with styles from the shrink method
    self._processStyleQueue();

    // Adjust the height of the container
    self._setContainerSize();
  },

  // Reset columns.
  _resetCols : function() {
    var i = this.cols;
    this.colYs = [];
    while (i--) {
      this.colYs.push( 0 );
    }
  },

  _reLayout : function( callback, isOnlyPosition ) {
    var self = this;
    callback = callback || self._filterEnd;
    self._resetCols();

    // If we've already sorted the elements, keep them sorted
    if ( self.keepSorted && self.lastSort ) {
      self.sort( self.lastSort, true, isOnlyPosition );
    } else {
      self._layout( self.jQueryitems.filter('.filtered').get(), self._filterEnd, isOnlyPosition );
    }
  },

  // worker method that places brick in the columnSet with the the minY
  _placeItem : function( jQueryitem, setY, callback, isOnlyPosition, isHide ) {
    // get the minimum Y value from the columns
    var self = this,
        minimumY = Math.min.apply( Math, setY ),
        shortCol = 0;

    // Find index of short column, the first from the left where this item will go
    // if ( setY[i] === minimumY ) requires items' height to be exact every time.
    // The buffer value is very useful when the height is a percentage of the width
    for (var i = 0, len = setY.length; i < len; i++) {
      if ( setY[i] >= minimumY - self.buffer && setY[i] <= minimumY + self.buffer ) {
        shortCol = i;
        break;
      }
    }

    // Position the item
    var x = self.colWidth * shortCol,
    y = minimumY;
    x = Math.round( x + self.offset.left );
    y = Math.round( y + self.offset.top );

    // Save data for shrink
    jQueryitem.data( {x: x, y: y} );

    // Apply setHeight to necessary columns
    var setHeight = minimumY + jQueryitem.outerHeight( true ),
    setSpan = self.cols + 1 - len;
    for ( i = 0; i < setSpan; i++ ) {
      self.colYs[ shortCol + i ] = setHeight;
    }

    var transitionObj = {
      from: 'layout',
      jQuerythis: jQueryitem,
      x: x,
      y: y,
      scale: 1
    };

    if ( isOnlyPosition ) {
      transitionObj.skipTransition = true;
    } else {
      transitionObj.opacity = 1;
      transitionObj.callback = callback;
    }

    if ( isHide ) {
      transitionObj.opacity = 0;
    }

    self.styleQueue.push( transitionObj );
  },

  /**
   * Hides the elements that don't match our filter
   */
  _shrink : function( jQuerycollection, fn ) {
    var self = this,
        jQueryconcealed = jQuerycollection || self.jQueryitems.filter('.concealed'),
        transitionObj = {},
        callback = fn || self._shrinkEnd;

    // Abort if no items
    if ( !jQueryconcealed.length ) {
      return;
    }

    self._fire( Shuffle.EventType.SHRINK );

    self.shrinkTransitionEnded = false;
    jQueryconcealed.each(function() {
      var jQuerythis = jQuery(this),
          data = jQuerythis.data();

      transitionObj = {
        from: 'shrink',
        jQuerythis: jQuerythis,
        x: data.x,
        y: data.y,
        scale : 0.001,
        opacity: 0,
        callback: callback
      };

      self.styleQueue.push( transitionObj );
    });
  },

  _onResize : function() {
    // If shuffle is disabled, destroyed, don't do anything
    if ( !this.enabled || this.destroyed ) {
      return;
    }

    // Will need to check height in the future if it's layed out horizontaly
    var containerWidth = this._getOuterWidth(this.jQueryel[0]);

    // containerWidth hasn't changed, don't do anything
    if ( containerWidth === this.containerWidth ) {
      return;
    }

    this.resized();
  },


  /**
   * Transitions an item in the grid
   *
   * @param {Object}   opts options
   * @param {jQuery}   opts.jQuerythis jQuery object representing the current item
   * @param {number}   opts.x translate's x
   * @param {number}   opts.y translate's y
   * @param {number}   opts.scale amount to scale the item
   * @param {number}   opts.opacity opacity of the item
   * @param {Function} opts.callback complete function for the animation
   * @private
   */
  _transition: function(opts) {
    var self = this,
    transform,
    // Only fire callback once per collection's transition
    complete = function() {
      if ( !self.layoutTransitionEnded && opts.from === 'layout' ) {
        self._fire( Shuffle.EventType.LAYOUT );
        opts.callback.call( self );
        self.layoutTransitionEnded = true;
      } else if ( !self.shrinkTransitionEnded && opts.from === 'shrink' ) {
        opts.callback.call( self );
        self.shrinkTransitionEnded = true;
      }
    };

    opts.callback = opts.callback || jQuery.noop;

    // Use CSS Transforms if we have them
    if ( self.supported ) {

      // Make scale one if it's not preset
      if ( opts.scale === undefined ) {
        opts.scale = 1;
      }

      if ( HAS_TRANSFORMS_3D ) {
        transform = 'translate3d(' + opts.x + 'px, ' + opts.y + 'px, 0) scale3d(' + opts.scale + ', ' + opts.scale + ', 1)';
      } else {
        transform = 'translate(' + opts.x + 'px, ' + opts.y + 'px) scale(' + opts.scale + ', ' + opts.scale + ')';
      }

      if ( opts.x !== undefined ) {
        opts.jQuerythis.css( TRANSFORM, transform );
      }

      if ( opts.opacity !== undefined ) {
        // Update css to trigger CSS Animation
        opts.jQuerythis.css('opacity' , opts.opacity);
      }

      opts.jQuerythis.one(TRANSITIONEND, complete);
    } else {

      var cssObj = {
        left: opts.x,
        top: opts.y,
        opacity: opts.opacity
      };

      // Use jQuery to animate left/top
      opts.jQuerythis.stop( true ).animate( cssObj, self.speed, 'swing', complete);
    }
  },

  _processStyleQueue : function() {
    var self = this,
        queue = self.styleQueue;

    jQuery.each(queue, function(i, transitionObj) {

      if ( transitionObj.skipTransition ) {
        self._skipTransition( transitionObj.jQuerythis[0], function() {
          self._transition( transitionObj );
        });
      } else {
        self._transition( transitionObj );
      }
    });

    // Remove everything in the style queue
    self.styleQueue.length = 0;
  },

  _shrinkEnd: function() {
    this._fire( Shuffle.EventType.SHRUNK );
  },

  _filterEnd: function() {
    this._fire( Shuffle.EventType.FILTERED );
  },

  _sortEnd: function() {
    this._fire( Shuffle.EventType.SORTED );
  },

  /**
   * Change a property or execute a function which will not have a transition
   * @param {Element} element DOM element that won't be transitioned
   * @param {(string|Function)} property The new style property which will be set or a function which will be called
   * @param {string} [value] The value that `property` should be.
   * @private
   */
  _skipTransition : function( element, property, value ) {
    var reflow,
        duration = element.style[ TRANSITION_DURATION ];

    // Set the duration to zero so it happens immediately
    element.style[ TRANSITION_DURATION ] = '0ms'; // ms needed for firefox!

    if ( jQuery.isFunction( property ) ) {
      property();
    } else {
      element.style[ property ] = value;
    }

    // Force reflow
    reflow = element.offsetWidth;

    // Put the duration back
    element.style[ TRANSITION_DURATION ] = duration;
  },

  _addItems : function( jQuerynewItems, animateIn, isSequential ) {
    var self = this,
        jQuerypassed,
        passed;

    if ( !self.supported ) {
      animateIn = false;
    }

    jQuerynewItems.addClass('shuffle-item');
    self._initItems( jQuerynewItems );
    self._setTransitions( jQuerynewItems );
    self.jQueryitems = self._getItems();

    // Hide all items
    jQuerynewItems.css('opacity', 0);

    // Get ones that passed the current filter
    jQuerypassed = self._filter( undefined, jQuerynewItems );
    passed = jQuerypassed.get();

    // How many filtered elements?
    self._updateItemCount();

    if ( animateIn ) {
      self._layout( passed, null, true, true );

      if ( isSequential ) {
        self._setSequentialDelay( jQuerypassed );
      }

      self._revealAppended( jQuerypassed );
    } else {
      self._layout( passed );
    }
  },

  _revealAppended : function( jQuerynewFilteredItems ) {
    var self = this;

    setTimeout(function() {
      jQuerynewFilteredItems.each(function(i, el) {
        self._transition({
          from: 'reveal',
          jQuerythis: jQuery(el),
          opacity: 1
        });
      });
    }, self.revealAppendedDelay);
  },


  /**
   * Public Methods
   */

  /**
   * The magic. This is what makes the plugin 'shuffle'
   * @param {(string|Function)} [category] Category to filter by. Can be a function
   * @param {Object} [sortObj] A sort object which can sort the filtered set
   */
  shuffle : function( category, sortObj ) {
    var self = this;

    if ( !self.enabled ) {
      return;
    }

    if ( !category ) {
      category = ALL_ITEMS;
    }

    self._filter( category );
    // Save the last filter in case elements are appended.
    self.lastFilter = category;

    // How many filtered elements?
    self._updateItemCount();

    self._resetCols();

    // Shrink each concealed item
    self._shrink();

    // If given a valid sort object, save it so that _reLayout() will sort the items
    if ( sortObj ) {
      self.lastSort = sortObj;
    }
    // Update transforms on .filtered elements so they will animate to their new positions
    self._reLayout();
  },

  /**
   * Gets the .filtered elements, sorts them, and passes them to layout
   *
   * @param {Object} opts the options object for the sorted plugin
   * @param {boolean} [fromFilter] was called from Shuffle.filter method.
   */
  sort: function( opts, fromFilter, isOnlyPosition ) {
    var self = this,
        items = self.jQueryitems.filter('.filtered').sorted(opts);

    if ( !fromFilter ) {
      self._resetCols();
    }

    self._layout(items, function() {
      if (fromFilter) {
        self._filterEnd();
      }
      self._sortEnd();
    }, isOnlyPosition);

    self.lastSort = opts;
  },

  /**
   * Relayout everything
   */
  resized: function( isOnlyLayout ) {
    if ( this.enabled ) {

      if ( !isOnlyLayout ) {
        // Get updated colCount
        this._setColumns();
      }

      // Layout items
      this._reLayout();
    }
  },

  /**
   * Use this instead of `update()` if you don't need the columns and gutters updated
   * Maybe an image inside `shuffle` loaded (and now has a height), which means calculations
   * could be off.
   */
  layout : function() {
    this.update( true );
  },

  update : function( isOnlyLayout ) {
    this.resized( isOnlyLayout );
  },

  /**
   * New items have been appended to shuffle. Fade them in sequentially
   * @param {jQuery} jQuerynewItems jQuery collection of new items
   * @param {boolean} [animateIn] If false, the new items won't animate in
   * @param {boolean} [isSequential] If false, new items won't sequentially fade in
   */
  appended : function( jQuerynewItems, animateIn, isSequential ) {
    // True if undefined
    animateIn = animateIn === false ? false : true;
    isSequential = isSequential === false ? false : true;

    this._addItems( jQuerynewItems, animateIn, isSequential );
  },

  /**
   * Disables shuffle from updating dimensions and layout on resize
   */
  disable : function() {
    this.enabled = false;
  },

  /**
   * Enables shuffle again
   * @param {boolean} [isUpdateLayout=true] if undefined, shuffle will update columns and gutters
   */
  enable : function( isUpdateLayout ) {
    this.enabled = true;
    if ( isUpdateLayout !== false ) {
      this.update();
    }
  },

  /**
   * Remove 1 or more shuffle items
   * @param {jQuery} jQuerycollection A jQuery object containing one or more element in shuffle
   * @return {Shuffle} The shuffle object
   */
  remove : function( jQuerycollection ) {

    // If this isn't a jquery object, exit
    if ( !jQuerycollection.length || !jQuerycollection.jquery ) {
      return;
    }

    var self = this;

    // Hide collection first
    self._shrink( jQuerycollection, function() {
      var shuffle = this;

      // Remove the collection in the callback
      jQuerycollection.remove();

      // Update the items, layout, count and fire off `removed` event
      setTimeout(function() {
        shuffle.jQueryitems = shuffle._getItems();
        shuffle.layout();
        shuffle._updateItemCount();
        shuffle._fire( Shuffle.EventType.REMOVED, [ jQuerycollection, shuffle ] );

        // Let it get garbage collected
        jQuerycollection = null;
      }, 0);
    });

    // Process changes
    self._processStyleQueue();

    return self;
  },

  /**
   * Destroys shuffle, removes events, styles, and classes
   */
  destroy: function() {
    var self = this;

    // If there is more than one shuffle instance on the page,
    // removing the resize handler from the window would remove them
    // all. This is why a unique value is needed.
    self.jQuerywindow.off('.' + self.unique);

    // Reset container styles
    self.jQueryel
        .removeClass( SHUFFLE )
        .removeAttr('style')
        .removeData( SHUFFLE );

    // Reset individual item styles
    self.jQueryitems
        .removeAttr('style')
        .removeClass('concealed filtered shuffle-item');

    // Null DOM references
    self.jQuerywindow = null;
    self.jQueryitems = null;
    self.jQueryel = null;
    self.jQuerysizer = null;
    self.sizer = null;

    // Set a flag so if a debounced resize has been triggered,
    // it can first check if it is actually destroyed and not doing anything
    self.destroyed = true;
  }
};


// Overrideable options
Shuffle.options = {
  group: ALL_ITEMS, // Filter group
  speed: 250, // Transition/animation speed (milliseconds)
  easing: 'ease-out', // css easing function to use
  itemSelector: '', // e.g. '.picture-item'
  sizer: null, // sizer element. Can be anything columnWidth is
  gutterWidth: 0, // a static number or function that tells the plugin how wide the gutters between columns are (in pixels)
  columnWidth: 0, // a static number or function that returns a number which tells the plugin how wide the columns are (in pixels)
  delimeter: null, // if your group is not json, and is comma delimeted, you could set delimeter to ','
  buffer: 0, // useful for percentage based heights when they might not always be exactly the same (in pixels)
  initialSort: null, // Shuffle can be initialized with a sort object. It is the same object given to the sort method
  throttle: throttle, // By default, shuffle will try to throttle the resize event. This option will change the method it uses
  throttleTime: 300, // How often shuffle can be called on resize (in milliseconds)
  sequentialFadeDelay: 150, // Delay between each item that fades in when adding items
  supported: CAN_TRANSITION_TRANSFORMS // supports transitions and transforms
};


// Not overrideable
Shuffle.settings = {
  jQuerysizer: null,
  useSizer: false,
  itemCss : { // default CSS for each item
    position: 'absolute',
	marginBottom: '10px',
	marginTop: '5px',
	marginLeft: '0px',
    top: 0,
    left: 0
  },
  offset: { top: 0, left: 0 },
  revealAppendedDelay: 300,
  keepSorted : true, // Keep sorted when shuffling/layout
  enabled: true,
  destroyed: false,
  styleQueue: []
};


// Plugin definition
jQuery.fn.shuffle = function( opts ) {
  var args = Array.prototype.slice.call( arguments, 1 );
  return this.each(function() {
    var jQuerythis = jQuery( this ),
        shuffle = jQuerythis.data( SHUFFLE );

    // If we don't have a stored shuffle, make a new one and save it
    if ( !shuffle ) {
      shuffle = new Shuffle( jQuerythis, opts );
      jQuerythis.data( SHUFFLE, shuffle );
    }

    if ( typeof opts === 'string' && shuffle[ opts ] ) {
      shuffle[ opts ].apply( shuffle, args );
    }
  });
};


// You can return `undefined` from the `by` function to revert to DOM order
// This plugin does NOT return a jQuery object. It returns a plain array because
// jQuery sorts everything in DOM order.
jQuery.fn.sorted = function(options) {
  var opts = jQuery.extend({}, jQuery.fn.sorted.defaults, options),
      arr = this.get(),
      revert = false;

  if ( !arr.length ) {
    return [];
  }

  if ( opts.randomize ) {
    return jQuery.fn.sorted.randomize( arr );
  }

  // Sort the elements by the opts.by function.
  // If we don't have opts.by, default to DOM order
  if (opts.by !== jQuery.noop && opts.by !== null && opts.by !== undefined) {
    arr.sort(function(a, b) {

      // Exit early if we already know we want to revert
      if ( revert ) {
        return 0;
      }

      var valA = opts.by(jQuery(a)),
          valB = opts.by(jQuery(b));

      // If both values are undefined, use the DOM order
      if ( valA === undefined && valB === undefined ) {
        revert = true;
        return 0;
      }

      if ( valA === 'sortFirst' || valB === 'sortLast' ) {
        return -1;
      }

      if ( valA === 'sortLast' || valB === 'sortFirst' ) {
        return 1;
      }

      return (valA < valB) ? -1 :
          (valA > valB) ? 1 : 0;
    });
  }

  // Revert to the original array if necessary
  if ( revert ) {
    return this.get();
  }

  if ( opts.reverse ) {
    arr.reverse();
  }

  return arr;
};


jQuery.fn.sorted.defaults = {
  reverse: false, // Use array.reverse() to reverse the results
  by: null, // Sorting function
  randomize: false // If true, this will skip the sorting and return a randomized order in the array
};


// http://stackoverflow.com/a/962890/373422
jQuery.fn.sorted.randomize = function( array ) {
  var top = array.length,
      tmp, current;

  if ( !top ) {
    return array;
  }

  while ( --top ) {
    current = Math.floor( Math.random() * (top + 1) );
    tmp = array[ current ];
    array[ current ] = array[ top ];
    array[ top ] = tmp;
  }

  return array;
};

return Shuffle;

});
