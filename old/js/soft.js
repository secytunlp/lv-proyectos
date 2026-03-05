/**

 * se carga una url invocada por ajax 

 * @param contentId 
 * @param myUrl
 * @param methodType
 * @param formId
 * @param onSuccess
 * @param showEffect
 */
	
function soft_load(contentId, myUrl, methodType, formId, onSuccess, showEffect){
	var loadUrl = myUrl;

	if(methodType)
		methodType = methodType;
	else
		methodType = "GET";

	var tagToWait = "body";
	 if( contentId )
		 tagToWait = "#"+contentId ;
	
	if( showEffect == true ){
		wait( tagToWait );
	}
	  
	
	//vemos si viene el formID
	var formData = '';
	if(formId)
		formData = $(formId).serialize();
	
	
	$.ajax({
		  url: loadUrl,
		  type: methodType,
		  data: formData,
		  cache: false,
		  success: function(content){
		    
			if( contentId ){
				$( "#"+contentId ).html(content);
				
			}

			if( showEffect )
				wakeUp( tagToWait );
			
			if( onSuccess ){
					
				//alert(content);
				onSuccess( content );
			}
		  }
		});
}

function wait( id ){
	$( id ).fadeTo("fast", 0.33);
}

function showMessage(id){
	$( id ).show(300).delay(4000).slideUp(300);
}
function hide( id ){
	$( id ).fadeOut(5000);
}

function wakeUp( id ){
	$( id ).fadeTo("fast", 1);
}

function showMessageInfo( msg, hide ){
	
	$( "#layout_info_message" ).html( msg );

	updated_right = ($(window).width() / 2) - ($( "#layout_info" ).width()/2);
	$( "#layout_info" ).css({'right': updated_right});

	updated_height = ($(window).height() / 2);
	$( "#layout_info" ).css({'top': updated_height});
	
	if( hide )
		$( "#layout_info" ).show().delay(4000).slideUp(300);
	else
		$( "#layout_info" ).show();
	
	
}

function hideMessageInfo( ){
	$( "#layout_info" ).slideUp(300);
}

function showMessageError( msg, hide ){
	$( "#layout_error_message" ).html( msg );
	
	updated_right = ($(window).width() / 2) - ($( "#layout_error" ).width()/2);
	$( "#layout_error" ).css({'right': updated_right});


	updated_height = ($(window).height() / 2);
	$( "#layout_error" ).css({'top': updated_height});
	
	if( hide )
		$( "#layout_error" ).show().delay(4000).slideUp(300);
	else
		$( "#layout_error" ).show();
	
	
}

function hideMessageError( ){
	$( "#layout_error" ).slideUp(300);
}


/* redefinimos funciones del componente FORM */

function submitAjaxForm(form_id, action, onSuccessCallback, beforeSubmit){
	
	var resp =  validate( form_id );
	beforeSubmit;
	
	if( resp ){
		
		soft_load(false, action, "POST", "#"+form_id, onSuccessCallback, false);
	}
	return false;
}