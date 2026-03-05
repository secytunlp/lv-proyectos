/*
 * Feedback - jQuery plugin 1.2.0
 *
 * Copyright (c) 2009 - 2010 Duncan J. Kenzie
 *  1.0  version published 2009-07-26
 *  1.0.1 version published 2010-01-22
 *  This version published 2010-09-24
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 *
 * Dependencies: 
 * Requires jQuery UI installation.
 * Purpose:
 * Provides feedback to users on any event. Useful for showing informational or error messages next to specific page elements 
 * as event-driven responses to user actions. 
 * Technical notes:
 * Use of anonymous function (which also gets called) allows use of $ without risk of issues with the noconflict(); method 
 * See jQuery plugin page for more info. 
 * DJK 20100924 - Added unique class of "ui-feedback" (you can override this option, named feedbackClass) to each div. 
 *                This lets you turn off existing feedback messages by using this selector: jQuery(".ui-feedback").hide(); 
 *                You might want to do this if multiple feedback messages potentially overlap one another.  
 *
 */
 
(function() { 

 jQuery.fn.feedback = function(msgtext, options) {
     // define defaults and override with options, if available
     // by extending the default settings, we don't modify the argument
	 var opts  = jQuery.extend({
	     type: "info",  					   
	     infoIcon: "ui-icon-info",
	     infoClass: "ui-state-highlight ui-corner-all",
	     errorIcon:   "ui-icon-alert", 
	     errorClass:  "ui-state-error ui-corner-all", 
	     duration: 2000,
	     left: false, 
	     below: false, 
	     right: false, 
	     above: false,
	     offsetX : 10, 
	     offsetY : 10, 
             feedbackClass: "ui-feedback"
	  }, options);

 var x = 0; 
 var y = 0; 
 var divclass= opts.feedbackClass;  // Class for container div - error or info . 
 var iconclass="";  // Icon class- alert or info. 
 
   if (!msgtext) var msgtext = "Something happened"; 
	
   return this.each(function(){
   		// handle to the element(s):  
  		var me = $(this); 
  	  	if (opts.type == "error") 
		{ 
			divclass= divclass + " " + opts.errorClass ;
			iconclass=opts.errorIcon; 
		}
		else 
		{
			divclass= divclass + " " + opts.infoClass; 
			iconclass=opts.infoIcon;
		}
		
		// if the icon class starts with "ui-" assume it's a valid Jquery UI class:  
		if (iconclass.substr(0,3) == "ui-")  iconclass = "ui-icon " + iconclass; 
		
  		// Create DOM elements of div, para (for text) and span (for image) and insert  after current DOM object: 
  		var msg = $('<div></div>').css({ display : "none", position : "absolute", paddingRight: "3px" }).addClass(divclass);
  		msg.append('<p><span style="float:left;" class="'+ iconclass+'"></span>'+msgtext+'</p>');
		
		
	//	var msgheight=document.defaultView.getComputedStyle(msg,null).getPropertyValue("height");

	//	console.log(msgheight); 
		
		// Insert after this DOM element: 
		me.after(msg);
		
  		// Compute position of me and use as basis for the tip: 
  		var p = me.position();
  		var meWidth = me.outerWidth(); // Includes padding and border width.  
		var meHeight = me.outerHeight(); 
    	var msgWidth = msg.outerWidth(); 
		var msgHeight = msg.outerHeight(); 
      	 
      	// Put it to specified location of object
      	// Left means the margin-offset, as a positive number, is subtracted from the absolute position of 'left' 
      	// All are false in the opts object, by default. 
      	// in which case, I assume you want the message to the right, on same horizontal plane as selected element. 
      	if (opts.left)  
      		x = p.left - msgWidth - opts.offsetX -3 ;
     	else if (opts.right)
     		x = p.left + meWidth + opts.offsetX; 
     	else
            x = p.left + meWidth + opts.offsetX; 
            
         if (x < 0) x = 1; 	
        // Even if developer wants message on the right, if start + length of message exceeds document width, put it to the left: 
       
     	if (( x+msgWidth) > document.body.clientWidth) 	
     		x = p.left - msgWidth - opts.offsetX;
    
    	// Calculate y (top) 
		// Also, if no left/right value specified, then place the message starting aligned with the element 
        if (opts.above) 
        { 
        	 y = p.top - msgHeight - opts.offsetY;
        	 if (y < 0) y = 0; 
			 if (!opts.left && !opts.right) x = p.left; 
         }
        else if(opts.below)
        {
        	y = p.top +  meHeight + opts.offsetY; 
        	if (y > document.body.clientHeight) y = p.top; 
        }
        // no top or bottom value - place it at same horizontal plane as element. 
        else 
        {
        	y= p.top; 
        }
        
        // After fadeout remove obsolete object (in a callback -ensures done after the fade): 
  		 		
  		msg.fadeIn("slow")
  			.css({ left: x+'px', top: y+'px' })
  			.animate({opacity: 1.0}, opts.duration)
  			.fadeOut("slow", function(){
  					$(this).remove();
  			}); 
 	 });
 };  
})(jQuery); 
