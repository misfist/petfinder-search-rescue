var $j = jQuery;
var DEMO = (function( $j ) {
  'use strict';

  var $jgrid = $j('#psr__grid'),
      $jfilterOptions = $j('.filter-options > li'),
      $jsizer = $jgrid.find('.shuffle__sizer'),

  init = function() {


    // None of these need to be executed synchronously
    setTimeout(function() {
      listen();
      setupFilters();
      setupSorting();
      setupSearching();
    }, 100);

   //track grid "shuffling processes"
    $jgrid.on('loading.shuffle done.shuffle shrink.shuffle shrunk.shuffle filter.shuffle filtered.shuffle sorted.shuffle layout.shuffle', function(evt, shuffle) {
      // Make sure the browser has a console
      if ( window.console && window.console.log && typeof window.console.log === 'function' ) {
        //console.log( 'Shuffle:', evt.type );
      }
    });

    // connect.picture-item in grid 
    $jgrid.shuffle({
      itemSelector: '.picture-item',
      sizer: $jsizer
    });

  },


/* =============================================================
	CLICK ON ANY OPTION BTN
	(to filter adoptable pets)
 * ============================================================= */
  setupFilters = function() {
    var $jbtns = $jfilterOptions;
    $jbtns.on('click', function() {
		
		//CONTINUE IF SECTION HAS MORE THAN ONE OPTIONN IN SECTION - else do nothing
		if(! $j(this).hasClass("onlyOption")){
		 
		  //IF THERE WAS TEXT IN THE SEARCH BAR - reset option btns and sections
		  	if( $j('.js-shuffle-search').val().length != 0 ) {
			  $j(".psr__btn-group").removeClass('active');
				  $j(".psr__btn-group .btn").removeClass('active');
				 $jgrid.shuffle( 'shuffle', group );
			}
		  
		  	//RESET GRID:
				//show all pets to reset
   				$j("#psr__grid > div").show();
		 		//hide any msgs
				$j(".noPetsFound-msg").hide();
				
				
			//CLEAR SEARCH BAR
          	$j(".filter__search").val("");
		  	$j("#search-icon .ico-psr_close").removeClass().addClass("ico-psr_search");
				
				
			//SET VARS FOR BTN/BTN-GROUP
      var $jthis = $j(this),
          isActive = $jthis.hasClass( 'active' ),
          group = isActive ? 'all' : $jthis.data('group');
		  
          
         	  
		  //IF A NORMAL BUTTON IS TURNED OFF - remove active state, check to see if option section still has any active btns
		  if ( isActive && ! $jthis.hasClass("allbtn")) {		
		 	 $j(this).removeClass('active');
		  	var newGroupCount =  $j(this).parent().find($j(".btn.active")).length;
		  	//if there are no more active btns in section, remove active state on option section
			if (newGroupCount==0){
		 		 $j(this).parent().removeClass('active');
		  	}
		  }
	
		  //IF RESET/ALL BTN IS CLICKED
          else if(! isActive){			  
			  $j(".btn.viewallbtn").removeClass("active");//remove active state on reset btn
			  
			  if ($jthis.hasClass("viewallbtn")){//if is the reset btn, remove active state off all other btns
				  $j(".psr__btn-group").removeClass('active');
				  $j(".psr__btn-group .btn").removeClass('active');
				 $jgrid.shuffle( 'shuffle', group );
			  }
			  
			  else{
				//IF NONE OF THE ABOVE, SIMPLY MAKE THE BTN ACTIVE
				$jthis.parent().addClass('active');			
			  
			   //IF AN ALL BTN THAT IS TURNED ON - example "Both", "Any", remove active state on other btns in section, and remove active state on section
			   if ($jthis.hasClass("allbtn")){
				   $jthis.parent().removeClass('active');
				  	$jthis.parent().find($j('.btn')).removeClass('active');		  			
			  }
			  
			  //IF A NORMAL BTN THAT IS TURNED ON, REMOVE ACTIVE STATE ON ALLBTN
			   if (! $jthis.hasClass("allbtn")){
				  	$jthis.parent().find($j('.allbtn')).removeClass('active');		  			
			  }

      	  
	    	//IN OR BTN GROUP (remove active state on other btns in same group)
	  		if ($jthis.parent().hasClass('OR-psr__btn-group')){
				$jthis.parent().find($j('.btn')).removeClass('active');
	  		}	
	  	
	  		//set current btn that was clicked to active
	  		$jthis.toggleClass('active');
			          }    //end else
	}//end if !active
	 
			
/* =============================================================
	CREATE IF/AND STATEMENT
	Based on active option sections and option btns selected
 * ============================================================= */
		
		$j("#psr__grid > div").hide();//HIDE ALL PETS AT FIRST		
				//set vars
				  var selected_AND_Attribute='';
				  var AND_ifstatement='';
				  var endingIfs='';
				  var ifstatement_firstround=true;
				  var my_group_round=1;
				  var my_btn_round=1;
				  
				  var btnGroupCount = $j('.psr__btn-group.active').length;//see how many active option sections exist
				  
				 if (btnGroupCount != 0 ){//as long as there is at least one active section, continue
				  AND_ifstatement += "$j('#psr__grid > div').each(function(";
				  }		  
				  
				  else {AND_ifstatement += "$j('#psr__grid > div').each(function() { if($j(this).is('.picture-item')";}
				  
				 
					//LOOP FOR ACTIVE OPTION SECTIONS
					$j('.psr__btn-group.active').each(function() {
						
				  		my_btn_round=1;
						
						  if ( my_group_round==1){
					   AND_ifstatement += "){if(";
					  
				   }
	  		 			var btnCount = $j(this).find('.btn.active').length;//see how may active option btns exist within section
						$j(this).find('.btn.active').each(function() {				 			
			
		 					var $jnewDataGroup_AND = $j(this).attr('data-group'); //var for current btn's data-group						
							
							if ($jnewDataGroup_AND != "all" && $jnewDataGroup_AND !=""){	//if btn is not an "allbtn" (like "both" or "any"				
		 				
								AND_ifstatement += "$j(this).is('." + $jnewDataGroup_AND + "') ";//add data-group to class on btn	
							
								if (my_btn_round < btnCount && btnCount > 1){//use my-btn-round to track loop.  only continue loop if less than total num active btns (and >1)
									
									if($jthis.parent().hasClass("lookingFor-optionGroup")){ //*speical looking for section requires a pet that meets all options in section
										AND_ifstatement += "&& ";
									}
									else {AND_ifstatement += "|| ";}
									my_btn_round++;
									}
							}
				});	
						
					if (my_group_round < btnGroupCount){
						AND_ifstatement += "){ if(";	
							my_group_round ++;
						endingIfs+=' } ';			
					}
									
					
				});	
						
			//FINISH END OF IF STATEMENT
				
					AND_ifstatement += "){ $j(this).show(); }"+endingIfs+" });   $j('.shuffle__sizer').show(); $j('.shuffle__sizer').css('visibility','visible');			$j('#psr__grid').shuffle('update');var visiblePetsCount = $j('.picture-item:visible').length; if(visiblePetsCount<1){$j('#noPetsCriteria-msg').show();}";
					//console.log(AND_ifstatement);
					var F = new Function (AND_ifstatement);
					return(F());
					
					
				  
	  
	  //rearrange pets in grid
	  $j(".shuffle__sizer").show();
	$j(".shuffle__sizer").css('visibility','visible');
	  $jgrid.shuffle('update');
	  
	  }//end if not onlyoption
		  
    });//end click btn function
	
			

    $jbtns = null;
		
  },//end setup filters


/* =============================================================
	SORTING FUNCTIONALITY
	(Currently unused)
 * ============================================================= */

  setupSorting = function() {
    // Sorting options
    $j('.sort-options').on('change', function() {
      var sort = this.value,
          opts = {};

      if ( sort === 'date-created' ) {
        opts = {
          reverse: true,
          by: function($jel) {
            return $jel.data('date-created');
          }
        };
      } 
	  else if ( sort === 'title' ) {
        opts = {
          by: function($jel) {
            return $jel.data('title').toLowerCase();
          }
        };
      }
	   else if ( sort === 'groups' ) {
        opts = {
          by: function($jel) {
            return $jel.data('groups');
          }
        };
      }
	    else if ( sort === 'website' ) {
        opts = {
          by: function($jel) {
            return $jel.data('website');
          }
        };
      }

      // SHUFFLE GRID
      $jgrid.shuffle('sort', opts);
    });
  },
  
  /* =============================================================
	SEARCHBAR
	Updates on keyup
 * ============================================================= */

  setupSearching = function() {
    // Advanced filtering
    $j('.js-shuffle-search').on('keyup change', function() {
	
      var val = this.value.toLowerCase();
	  
	   //SWAP OUT ICONS - show x instead of search icon
		 $j("#search-icon .ico-psr_search").removeClass().addClass("ico-psr_close psr__hoverme");

 		//RESET GRID:
			//show all pets to reset
   			$j(".picture-item").show();
      		//remove all active states when searching      
         	$j(".filter-options.psr__btn-group .btn").removeClass('active');
		 	//hide any msgs
			$j(".noPetsFound-msg").hide();
  
      
	  //UPDATE GRID
	  $jgrid.shuffle('shuffle', function($jel, shuffle) {
        //reset category back to view all when searching
      shuffle.group = 'all';

        // Only search elements in the current group (currently disabled)
        // if (shuffle.group !== 'all' && $j.inArray(shuffle.group, $jel.data('groups')) === -1) {
        //   return false;
        // }

        var text = $j.trim( $jel.find('.picture-item__title').text() ).toLowerCase();
	
        return text.indexOf(val) !== -1;
		
		
      });
	  var numPetsSearchName=$j('.picture-item.filtered').length;
	  //IF NO PETS ARE FOUND WITH THAT NAME	 
	  if (numPetsSearchName < 1){
	 	$j('#noPetsName-msg').show();
		}
		else{ $j('#noPetsName-msg').hide();}
    });
/* =============================================================
	CLEAR SEACH BAR
	(Click on x in searchbox)
 * ============================================================= */
  $j("#search-icon").click(function(){
	  //(if not search icon in input, reset animals and clear input)
	  if (!$j(this).find('span').hasClass("ico-psr_search")){
			
			//reset input
    		$j(".filter__search").val("");
			//remove x and switch to search icon
			$j("#search-icon .ico-psr_close").removeClass().addClass("ico-psr_search");	
    		var val="";
			
    		//reset grid and show all pets
   			$j(".picture-item").show();
    		 //change active btn back to viewallbtn on clear
         	$j(".filter-options.psr__btn-group .btn").removeClass('active');
 	 		
			//rearrange grid
  			$jgrid.shuffle('shuffle', function($jel, shuffle) {
     		//reset category back to view all on clear
      		shuffle.group = 'all';
        	// Only search elements in the current group
        	if (shuffle.group !== 'all' && $j.inArray(shuffle.group, $jel.data('groups')) === -1) {
          		return false;
        	}

        	var text = $j.trim( $jel.find('.picture-item__title').text() ).toLowerCase();
		
        	return text.indexOf(val) !== -1;
		
      });
	  }//end if (not search icon)
  });
     
  },

  // Re layout shuffle when images load. This is only needed
  // below 768 pixels 
  listen = function() {
   // var debouncedLayout = $j.throttle( 300, function() {
      $jgrid.shuffle('update');
    //});

    // Get all images inside shuffle
    $jgrid.find('img').each(function() {
      var proxyImage;

      // Image already loaded
      if ( this.complete && this.naturalWidth !== undefined ) {
        return;
      }

      // If none of the checks above matched, simulate loading on detached element.
      proxyImage = new Image();
      $j( proxyImage ).on('load', function() {
        $j(this).off('load');
        //debouncedLayout();
      });

      proxyImage.src = this.src;
    });

    // Because this method doesn't seem to be perfect.
    setTimeout(function() {
      //debouncedLayout();
    }, 500);
  };

  return {
    init: init
  };
}( jQuery ));


/* =============================================================
	OTHER CLICK FUNCTIONS
	Header toggle, uparrow, popups
 * ============================================================= */
(function( $j ) {
 
   //MAXIMIZE/MINIMIZE HEADER
     $j("#toggle-petOptions-holder").click(function(){
       //hide cats
      if (window.showCats!=false){
      $j("#toggle-petOptions .toggle-petOptions-text").html('Show Options');
	  $j("#toggle-petOptions .ico-psr_hide").removeClass().addClass('ico-psr_show');
      window.showCats=false;  
      }
      //show cats
      else if (window.showCats==false){
      $j("#toggle-petOptions .toggle-petOptions-text").html('Hide Options');
	  $j("#toggle-petOptions .ico-psr_show").removeClass().addClass('ico-psr_hide');
      window.showCats=true;
      }
	  //show/hide cat div
      $j("#all-pet-options").slideToggle();
       });

   //SHOW UP ARROW
      $j(window).scroll(function(){  
        // if the user scrolled the page more than 200 pixels, show the 'up' arrow image
        if ($j(this).scrollTop() > 200) {
            $j('.p_sr-pagetop-arrow').fadeIn();
        }

        // hide the 'up' arrow image
        else {
            $j('.p_sr-pagetop-arrow').fadeOut();
			
        } 
    });

      //CLICK ON UP ARROW TO GO BACK TO TOP OF PAGE
      $j(".p_sr-pagetop-arrow").on("click", function() {
          $j("html, body").animate({ scrollTop: 0}, 1000);
          return false;
      });


    //POPUP FUNCTIONS////////////

    updateCurrentIcon=function(thisObj){
      //update var to current icon
      window.currentIcon=thisObj;
	  var $jmyPetName = thisObj.find('.picture-item__title').html();
	  var $jmyPetPhotos = thisObj.find('.my-pet-photos').html();
	  var $jmyPetAttributes = thisObj.find('.my-pet-attributes').html();
	  var $jmyPetOptions = thisObj.find('.my-pet-options').html();
	  var $jmyPetDescription = thisObj.find('.my-pet-description').html();
	  var $jmyPetPetfinderURL = thisObj.find('.my-pet-petfinder_url').html();
	  
    //DISPLAY PET INFORMATION
    //display photo
    $j("#pet-multiphotos").html($jmyPetPhotos);
    //display name
    $j('#indiepet-popup-title').html($jmyPetName);  
    //display adopt btn with link
	$jsavedadoptionlink=$j('#pet-adopt-btn #adoptionformlink').attr('data-group');
	$j('#pet-adopt-btn #adoptionformlink').html('<span class="ico-psr_adopt"></span>Adopt Me');
	$j('#pet-adopt-btn').wrap('<a class="psr__adoptformlink" href="'+$jsavedadoptionlink + '?petname=' + $jmyPetName +'"></a>');
	
    //diplay pet attributes
	 $j('#pet-attributes').html($jmyPetAttributes);
	//diplay pet options
	 $j('#pet-options').html($jmyPetOptions);
	 //diplay pet description
	 $j('#pet-description').html($jmyPetDescription);
	 //diplay petfinder url
	 $j('#pet-petfinder_url').html($jmyPetPetfinderURL);
  }
$j.fn.slideFadeToggle  = function(speed, easing, callback) {
        return this.animate({opacity: 'toggle', width: 'toggle', height:'toggle'}, speed, easing, callback);
};

//CLICKED ON INFO BTN
$j("#psr__adoptinfo-btn").click(function(){
	 $j("#adoptinfo-popup").slideFadeToggle(400);
	 $j(".popup-bg").show(200);
	  $j('html').attr('style','overflow:hidden!important');
});

    //CLICKED ON ANY PET
    $j(".picture-item").click(function(){
         $j("#indiepet-popup").slideFadeToggle(400);
          $j(".popup-bg").show(200);
       $j('html').attr('style','overflow:hidden!important');
        //update currenticon 
      window.currentIcon=$j(this);
      updateCurrentIcon(window.currentIcon);
	  //reset thumbnail scroller
	  init();
    });
    ////BTNS IN POPUP
    //NEXT
    $j("#indiepet-popup-next").click(function(){
	//check if next icon is visible/hidden
	if(window.currentIcon.next().is(":hidden")){
		//if hidden, look for next visible 
		while(window.currentIcon.next().is(":hidden") && window.currentIcon.next().hasClass("picture-item")){
    			window.currentIcon=window.currentIcon.next();
			}			
		}
		//maks sure next is picture-item
		if(window.currentIcon.next().hasClass("picture-item")){
		window.currentIcon=window.currentIcon.next();
		updateCurrentIcon(window.currentIcon);
		
				 //reset thumbnail scroller
	  			init();
		}
    });
     //PREVIOUS
    $j("#indiepet-popup-previous").click(function(){
	//check if next icon is visible/hidden
	if(window.currentIcon.prev().is(":hidden")){
		//if hidden, look for prev visible 
		while(window.currentIcon.prev().is(":hidden") && window.currentIcon.prev().hasClass("picture-item")){
    			window.currentIcon=window.currentIcon.prev();
			}			
		}
		//maks sure prev is picture-item
		if(window.currentIcon.prev().hasClass("picture-item")){
		window.currentIcon=window.currentIcon.prev();
		updateCurrentIcon(window.currentIcon);
		
				 //reset thumbnail scroller
	  			init();
		}
    });
    //CLOSE   
    $j(".window-popup-close").click(function(){
      $j(".window-popup").hide();//hide url-icon popup
       $j(".popup-bg").hide();
	   $j('html').attr('style','overflow:auto!important');
      
    });   
	
	   //IF CLICK ON POPUP BG, CLOSE ALL POPUPS
     $j(".popup-bg").click(function(){
      $j(this).hide();
      $j(".window-popup").hide();
	   $j('html').attr('style','overflow:auto!important');
     
     });
	
	//for thumbnail slider
	//1. set ul width 
//2. image when click prev/next button
var ul;
var li_items;
var imageNumber;
var imageWidth;
var prev, next;
var currentPostion = 0;
var currentImage = 0;


function init(){
	if ($j('#image_slider').length > 0) {
	ul = document.getElementById('image_slider');
	li_items = ul.children;
	imageNumber = li_items.length;
	imageWidth = li_items[0].children[0].clientWidth;
	ul.style.width = parseInt(imageWidth * imageNumber) + 'px';
	}
	prev = document.getElementById("prev");
	next = document.getElementById("next");
	//.onclike = slide(-1) will be fired when onload;
	/*
	prev.onclick = function(){slide(-1);};
	next.onclick = function(){slide(1);};*/
	
	//hide arrows if only one image
	if (imageNumber<=1 || imageNumber==null ){
	$j("#prev").hide();
	$j("#next").hide();
	}
	else{
		$j("#prev").show();
	$j("#next").show();
		prev.onclick = function(){ onClickPrev();};
	next.onclick = function(){ onClickNext();};
	}
}

function animate(opts){
	var start = new Date;
	var id = setInterval(function(){
		var timePassed = new Date - start;
		var progress = timePassed / opts.duration;
		if (progress > 1){
			progress = 1;
		}
		var delta = opts.delta(progress);
		opts.step(delta);
		if (progress == 1){
			clearInterval(id);
			opts.callback();
		}
	}, opts.delay || 17);
	//return id;
}

function slideTo(imageToGo){
	var direction;
	var numOfImageToGo = Math.abs(imageToGo - currentImage);
	// slide toward left

	direction = currentImage > imageToGo ? 1 : -1;
	currentPostion = -1 * currentImage * imageWidth;
	var opts = {
		duration:200,
		delta:function(p){return p;},
		step:function(delta){
			ul.style.left = parseInt(currentPostion + direction * delta * imageWidth * numOfImageToGo) + 'px';
		},
		callback:function(){currentImage = imageToGo;}	
	};
	animate(opts);
}

function onClickPrev(){
	if (currentImage == 0){
		slideTo(imageNumber - 1);
	} 		
	else{
		slideTo(currentImage - 1);
	}		
}

function onClickNext(){
	if (currentImage == imageNumber - 1){
		slideTo(0);
	}		
	else{
		slideTo(currentImage + 1);
	}		
}

window.onload = init;

}( jQuery ));



jQuery(document).ready(function() {
	//check options sections - if a section only has one option, force it to active state and hide viewall btn
		jQuery(".filter-options.psr__btn-group").each(function() {
			var totalNumOptions = jQuery(this).find('.btn').length;
			
			if (totalNumOptions ==2){				
				jQuery(this).find(".btn.allbtn").hide();
				jQuery(this).find('.btn').removeClass("btn").addClass("onlyOption");
			}
		});

//EXECUTE SHUFFLING GRID //////
  DEMO.init();
  
});

//PRELOADER///////
    jQuery(window).load(function() { // makes sure the whole site is loaded
      jQuery("#psr__preloader").delay(350).fadeOut("slow"); // will fade out the white DIV that covers the website.	  
	  jQuery(".psr_container-pets").delay(350).attr('style','overflow:visible');
    });