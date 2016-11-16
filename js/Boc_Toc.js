/**
 * Jquery function to create dynamic table of contents.
 * Written by Lucas Gerroir.
 *
 * @param object options     bocToc setting values.
 */

(function ( $ ) {

	$.fn.bocToc = function( options ) {

		// Default setting options.
        var settings = $.extend({
        	headers : "h2, h3, h4, h5, h6",
        	spyscroll : true,
        	foot_stop : "footer",
        	container_div : "#boc_toc_container",
        	depth : "h2"

        }, options );
   	 	
   	 	// the class of the node you are binding the function to.
   	    var main_class = this.selector;

   	    // define necessary variables
   	    var toc = $("<ul></ul>").attr("class", "nav"),
   	        toc_container = $(settings.container_div),
   	        prev_level = 2,
   	        level_array = settings.headers.replace(/\s/g, '').split(","), // split chosen headers into array
   	        top_level_array = setupLevel(level_array), // setup header hierarchy sorting array
   	        smallest_header = level_array[0].match(/\d/g)[0], // smallest header level chosen
   	        ids = [];

   	    // if spyscroll is turned on call necessary functions
   	    if (settings.spyscroll) {

   	    	AddSpyScrollClasses(this, toc_container);
   	    	stickBocContainer(toc_container.selector);

   	    }

   	    // walk through header nodes within a container
	    $( main_class ).find(settings.headers).each(function() {
            
            // define necessary variables 
            var header = $( this ),
            	header_name = header[0].tagName.toString().toLowerCase(),
            	level = header_name.match(/\d/g)[0], //change header to numeric value for comparrison
            	text = header.text(),
            	anchor_link = addID(header, text);
            
            
           if (level == prev_level) { // if the header level a sibling

           	    var temp = top_level_array[header_name];
           		top_level_array[header_name].push(temp.append(createListItem(text, anchor_link)));
           
           } else if (level > prev_level) { // if the current header level is a child
           
           		var temp = top_level_array[header_name];
           		top_level_array[header_name].push(temp.append(createListItem(text, anchor_link)));
           
           } else if (level < prev_level) { // if the current header level is a fellow parent
           
           		top_level_array = sortlist(top_level_array, smallest_header);
           		var temp = top_level_array[header_name];
           		top_level_array[header_name].push(temp.append(createListItem(text, anchor_link)));
           }

            // set prev level to current level for next loop
       		prev_level = level;
        });

	    // sort headers so they are hierarchical
	    toc = sortlist(top_level_array, smallest_header);

	    // add the hiearchical list to nav
	    var boc_toc = generateNav(toc["h" + smallest_header][0]); 
	   
	    // append the hiearchial nav to main container
	    toc_container.append(boc_toc);
		

		/**
	 	*  Sorts the headers in main array into hierarchical order.
	 	*
	 	* @see bocToc::sortlist()
	 	*
	 	* @param  object  top_level_array     An object with keys for each header and list items for each header
	 	* @param  int     smallest_header     The smallest header ex. h2
	 	* @return object  top_level_array     Hierarchical sorted object
	 	*/
	    function sortlist( top_level_array, smallest_header ) 
	    {
	    	// start from the highest header and append it to the parent header 	
	 		for (i = ( Object.keys( top_level_array ).length + 1 ); i >= smallest_header; i--) {
	 			
	 			var header = "h" + i;
	 			var parent_header = "h" + (i - 1);
	 			
	 			if (top_level_array[header].length > 1 && top_level_array[parent_header]) {

	 				top_level_array[parent_header].children("li:last-child").append(top_level_array[header][0]);
	 				top_level_array[header] = $("<ul></ul>").attr("class", "nav");
	 			
	 			}
	 		}

			return top_level_array
	    }
	    
	    /**
	 	* removes beginning, trailing spaces and special characters from title.
	 	*
	 	* @see bocToc::filterTitles()
	 	*
	 	* @param  string  title              The text from the header 
	 	* @return string  filtered_title.    The title with beginning and trailing space, special characters removed
	 	*/
	    function filterTitles( title )
	    {
	    	var filtered_title = title.replace(/^\s+|\s+$/gm,'');
	    	filtered_title = filtered_title.replace(/[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '');

	    	return filtered_title;
	    }

	   /**
	 	*  Tracks scroll and adds position fixed to boc toc container.
	 	*  As well this function makes sure the boc toc container does not go over the footer
 	 	*
	 	* @see bocToc::stickBocContainer()
	 	*
	 	* @param  string  boc_toc_container      reference to the main container 
	 	*/
	    function stickBocContainer( boc_toc_container )
	    {	
	    	
	    	if ($(boc_toc_container).length) {
	    		var distance_from_top = $(boc_toc_container).offset().top;
	    	}

	    	$(window).scroll(function() {	

	    		var window_dist_top = $(window).scrollTop();

	    		// add class so boc toc container position fixed
  				if ( window_dist_top >= distance_from_top ) {
        			$(boc_toc_container).addClass("toc_stick");
    			} else {
    				$(boc_toc_container).removeClass( "toc_stick" )
    			}

    			var container_height = $(boc_toc_container).height();
    			var footer_dist_top = $(settings.foot_stop).offset().top;
    			var cont_window = (window_dist_top + container_height);
                
                // if the boc toc container goes over the footer or chosen div apply negative margin top
    			if (cont_window > footer_dist_top) {
    				var overlap = footer_dist_top - cont_window;
    				$(boc_toc_container).css("marginTop", overlap + "px");
    			} else {
    				$(boc_toc_container).css("marginTop", "0px");
    			}

			});
	    	
	    }

	   /**
	 	* Adds sopsyscroll data attributes and classes.
	 	* As well intiates spyscroll.
	 	*
	 	* @see bocToc::AddSpyScrollClasses()
	 	*
	 	* @param  object  content_container      The container where the headers are.
	 	* @param  object  target                 The boc toc container.
	 	*/
	    function AddSpyScrollClasses( content_container, target )
	    {   
	    	
	    	$(target.selector).addClass("spyscroll");

	    	$("body").ready(function(){

	    		$("body").css("position", "relative");
	    		$("body").attr("data-spy", "scroll");
	    		$("body").attr("data-offset", "10");
	    		$("body").attr("data-target", target.selector);

	    		$("body").scrollspy({ target: target.selector, offset : 10 });

	    	});

	    }

	   /**
	 	* Adds the correct id to the header node
	 	*
	 	* @see bocToc::addID()
	 	*
	 	* @param  object  header        The header on the page
	 	* @param  title   title         The text within the header
	 	*
	 	* @return string   anchor_link  string for the id of the header 
	 	*/
	    function addID( header, title )
	    {	
	    	title = filterTitles(title);
	    	var id = title.replace(/\s/g, '-');
	 		
	    	// see if the id already exists
	 		id = generateUniqueID(id);
	    	
	    	header.attr("id", id);

	    	var anchor_link =  "#" + id;

	    	return anchor_link;
	    }

	   /**
	 	* Sets up the main array for hiearchial sorting.
	 	*
	 	* @see bocToc::addID()
	 	*
	 	* @param  object  header        The header on the page
	 	* @param  title   title         The text within the header
	 	*
	 	* @return string   anchor_link  string for the id of the header 
	 	*/
	    function setupLevel( level_array ) 
	    {

	        var top_level_array = {};
	    	level_array.forEach(function(entry) {
	    		top_level_array[entry] = createUnorderedItem(entry);
	    	});

	    	return top_level_array;
	    }

	   /**
	 	*  Creates a list and link object.
	 	*
	 	* @see bocToc::createListItem()
	 	*
	 	* @param  string   text                The text of the header 
	 	* @param  string   anchor_link         The text for the link
	 	* @param  object   ul                  A prepared unordered list object
	 	*
	 	* @return object   li    a list object
	 	*/
        function createListItem( text, anchor_link, ul ) 
        {
        	
        	var list_item = "<li></li>";
        	var a = "<a href='" + anchor_link + "'></a>";
        	var link = $(a).append(text);
        	var li = $(list_item).append(link);

        	return li;
        }

       /** 
	 	*  Checks to see if the header id already exists.
	 	*  This way you can have headers with the same text.
	 	*
	 	* @see bocToc::generateUniqueID()
	 	*
	 	* @param   string   id   text                The text of the header 
	 	*
	 	* @return  string   id    a list object
	 	*/
        function generateUniqueID( id ) {

        	var in_array = $.inArray(id, ids);
	    	if ( in_array != -1 ) {
	    		id += "_" + in_array;
	    	}

	    	ids.push(id);

	    	return id;
        }


       /**
	 	* Creates a navigation object.
	 	*
	 	* @see bocToc::generateNav()
	 	*
	 	* @param   object   object  unordered hierarchical list
	 	*
	 	* @return  object   menu    unordered hierarchical list inside a nav
	 	*/
        function generateNav( object ) {

        	var nav_item = "<nav></nav>";
        	var menu = $(nav_item).append(object);

        	return menu;
        }

        /**
	 	*  Creates unordered list object.
	 	*  As well adds depth class to the chosen header depth
	 	*
	 	* @see bocToc::createUnorderedItem()
	 	*
	 	* @param   string   level  The header level
	 	*
	 	* @return  object   ul     unordered list object
	 	*/
        function createUnorderedItem( level ) 
        {
        	level = level.match(/\d/g)[0];
        	var num_depth = settings.depth.match(/\d/g)[0];
  
        	var unorder_list_item = "<ul></ul>";
        	var ul = "";
        	
        	ul = $(unorder_list_item).addClass("nav");

        	if (num_depth == level) {
        		ul = ul.addClass("depth");
        	} 

        	return ul;
        }
    
	};

}( jQuery ));

// setup chosen div
var chosen_container = jQuery(".entry-content");
var toc_container = "#boc_toc_container";

chosen_container.ready(function() {
	
	//get data attributes setup by admin
	var headers_select = jQuery(toc_container).data("headers");
	var spyscroll = jQuery(toc_container).data("spyscroll");
	var depth = jQuery(toc_container).data("depth");

	// setup object with setting values
	var parameters = {	
						headers : headers_select,
						container_div : toc_container,
						spyscroll : (spyscroll == "checked" ? true : false),
						depth : depth
					 }

    // bind boc toc function call to container 
	chosen_container.bocToc(parameters);
});


