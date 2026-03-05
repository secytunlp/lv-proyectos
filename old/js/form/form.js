/* funciones del componente FORM */

function submitAjaxForm(form_id, action, onSuccessCallback, beforeSubmit){

	var $form = $("#"+form_id);
	var $submit_ajax = $("#" + form_id  +  "_input_submit_ajax");
	var $restoreOpacity = $submit_ajax.css("opacity");
	
	//var resp =  validate( form_id );
	var resp =  1;
	beforeSubmit;
	
	/* $(':input', $form).each(function() {
         this.disabled = false;
     });*/
	
	if( resp ){
		var methodType = "POST";
		var tagToWait = "body";
		formData = $("#"+form_id).serialize();
		
		
		//Comprobamos si el semaforo esta en verde (1)
		if ($form.data('locked') == undefined || !$form.data('locked')){
			
			//No esta bloqueado aún, bloqueamos y enviamos
			$.ajax({
				  url: action,
				  type: methodType,
				  data: formData,
				  cache: false,
				  beforeSend: function(){
					$submit_ajax.css({opacity:0.5});
					$form.data('locked', true);  // lo bloqueamos
				  },
				  complete: function(){ 
				  },
				  error: function(){ 
					 	$form.data('locked', false);  // lo desbloqueamos
					 	$submit_ajax.css({"opacity":$restoreOpacity});
					 	$submit_ajax.removeAttr( "disabled" )
					  },
				  success: function(content){
				    
					$form.data('locked', false);  // lo desbloqueamos
					$submit_ajax.css({"opacity":$restoreOpacity});
					$submit_ajax.removeAttr( "disabled" );
					 
					if( onSuccessCallback ){
							
						//alert(content);
						onSuccessCallback( content );
					}
					
				  }
				});	 
			 
		}else{
	        //bloqueado...
	    }
	}else{
		
		$form.data('locked', false);  // lo desbloqueamos
		$submit_ajax.css({"opacity":$restoreOpacity});
		$submit_ajax.removeAttr( "disabled" );
	}
	return false;
}




function submitForm(form_id){

	
	var $submit ;
	
	if( $("#" + form_id  +  "_input_submit").length > 0)
		$submit = $("#" + form_id  +  "_input_submit");
	else
		$submit = $("#" + form_id  +  "_input_submit_ajax");
	
	$submit.attr("disabled", "disabled");
	
	$("#"+form_id).submit();
}

function clearForm(form) {
	
	form = $('#'+form);
    // iterate over all of the inputs for the form

    // element that was passed in

    $(':input', form).each(function() {

      var type = this.type;
	  

      var tag = this.tagName.toLowerCase(); // normalize case

      // it's ok to reset the value attr of text inputs,

      // password inputs, and textareas&cd_trunk="+cd_trunk+"&delimiter="+delimiter

      if (type == 'text' || type == 'password' || tag == 'textarea')

        this.value = "";

      // checkboxes and radios need to have their checked state cleared

      // but should *not* have their 'value' changed

      else if (type == 'checkbox' || type == 'radio')

        this.checked = false;

      // select elements need to have their 'selectedIndex' property set to -1

      // (this works for both single and multiple select elements)

      else if (tag == 'select')

        this.selectedIndex = -1;

    });

 }

