function autocomplete_callback_ds_titulogrado( oid ){
	
}

function solicitud_filter_lugarTrabajo_change(  ){
	
}

function solicitud_filter_titulo_change(  ){
	
}

function solicitud_filter_tituloposgrado_change(  ){

}


function FormatToCurrency(num)
{
num = num.toString().replace(/\$|\,/g,'');
if(isNaN(num))
num = "0";
sign = (num == (num = Math.abs(num)));
num = Math.floor(num*100+0.50000000001);
cents = num%100;
num = Math.floor(num/100).toString();
if(cents < 10)
cents = "0" + cents;
for (var i = 0; i < Math.floor((num.length-(1+i))/3); i++)
num = num.substring(0,num.length-(4*i+3))+'.'+ num.substring(num.length-(4*i+3));
return (((sign)?'':'-') + '$' + num + ',' + cents);
}


function solicitud_filter_evaluador_change(attr){
	
	
	var oid = attr["entity"]["oid"];
	var cd_cat = 0;
	jQuery.ajax({
	      url:"doAction?action=assign_evaluadores_dame_cat&solicitud_oid=" + oid,
	      dataType:"json",
	      success: function(data){
	      	
	      	if( data != null && data["error"]!=null){
	      		showMessageError( data["error"], true );
	      		//inhabilitar el submit.
	      		//$("#edit_solicitud_input_submit_ajax").hide();
	      	}
	      	
	      	else{
	      		cd_cat = data["cat"];
	      		cd_facultad = data["facultad"];
	      		//jAlert(data["cat"]);
	      		findentity_setParent_evaluacion_filter_evaluadorInterno_oid( cd_cat+',==,'+cd_facultad );
	      		findentity_setParent_evaluacion_filter_evaluadorExterno_oid( cd_cat+',!=,'+cd_facultad );
	      		findentity_setParent_evaluacion_filter_evaluadorTercero_oid( cd_cat+',3,'+cd_facultad );
	      	} 	
	      	// $("#iconoLoading").remove();
	      	
	      }
	});
	
	
	
	
}

function solicitud_filter_evaluador_change1(attr){
	
	
	var oid = attr["entity"]["oid"];
	var cd_cat = 0;
	
	jQuery.ajax({
	      url:"doAction?action=assign_evaluadores_dame_cat&solicitud_oid=" + oid,
	      dataType:"json",
	      success: function(data){
	      	
	      	if( data != null && data["error"]!=null){
	      		showMessageError( data["error"], true );
	      		//inhabilitar el submit.
	      		//$("#edit_solicitud_input_submit_ajax").hide();
	      	}
	      	
	      	else{
	      		cd_cat = data["cat"];
	      		cd_facultad = data["facultad"];
	      		//jAlert(data["cat"]);
	      		/*findentity_setParent_evaluacion_filter_evaluadorInterno_oid( cd_cat+',==,'+cd_facultad );
	      		findentity_setParent_evaluacion_filter_evaluadorExterno_oid( cd_cat+',!=,'+cd_facultad );
	      		findentity_setParent_evaluacion_filter_evaluadorTercero_oid( cd_cat+',3,'+cd_facultad );*/
				findentity_setParent_evaluacion_filter_evaluador_oid( cd_cat+',!=,'+cd_facultad );
	      	} 	
	      	// $("#iconoLoading").remove();
	      	
	      }
	});
	
	
	
	
}

function bl_interno_click(bl_interno){
	
	
	var oid = $("#evaluacion_filter_solicitud_oid").val();
	var cd_cat = 0;
	
	
	jQuery.ajax({
	      url:"doAction?action=assign_evaluadores_dame_cat&solicitud_oid=" + oid,
	      dataType:"json",
	      success: function(data){
	      	
	      	if( data != null && data["error"]!=null){
	      		showMessageError( data["error"], true );
	      		//inhabilitar el submit.
	      		//$("#edit_solicitud_input_submit_ajax").hide();
	      	}
	      	
	      	else{
	      		cd_cat = data["cat"];
	      		cd_facultad = data["facultad"];
	      		if(bl_interno){
					findentity_setParent_evaluacion_filter_evaluador_oid( cd_cat+',==,'+cd_facultad );
				}
				else{
					findentity_setParent_evaluacion_filter_evaluador_oid( cd_cat+',!=,'+cd_facultad );

	      	} 	}
	      	// $("#iconoLoading").remove();
	      	
	      }
	});
	
	
	
	
}

function formatDec(valor, decimales) {
	var parts = String(valor).split(".");
	parts[1] = String(parts[1]).substring(0, decimales);
	// parts[1] = Number(parts[1]) * Math.pow(10, -(decimales - 1)); //POTENCIA
	// parts[1] = String(Math.floor(parts[1])); //REDODEA HACIA ABAJO
	return parseFloat(parts.join("."));
}



