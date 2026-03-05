/* 

 *  SCRIPTS.
 *  
 *  function confirmDelete(cartel, a, href);
 *  function evaluate(onComplete);
 *  function list_all(action);
 *  function showErrorMessage(mensaje);
 *  function popUp(a);
 *  function popUpBig(a);
 *  function submit_self(accion);
 *  function submit_blank(accion);
 *  function checkFilter();
 *  
 */



/** ****************** A ************************* */

function actualizarPuerto(){
    value_protocol = document.getElementById('ds_protocol').value;
    oPuerto = document.getElementById('nu_port');
    if(value_protocol == 'SIP'){
        oPuerto.value = '5060';
    }
    if(value_protocol == 'H323'){
        oPuerto.value = '1720';
    }
}
function ajax_member_liquidation(url){
    jQuery.ajax({
        url:url,
        success: function(datos){
            ConfirmLiquidation(datos);
        }
    });
}

function loadDs_product(url){
    pr = document.getElementById('ds_prefix').value;
    jQuery.ajax({
        url:url+'&pr='+pr,
        success: function(datos){
            document.getElementById('ds_product').value = datos;
           // document.getElementById('ds_offer').value = datos;
        }
    });
}

function ConfirmLiquidation(){
    var dialogOpts = {
        title : "Middleware - Member's Liquidation",
        modal : true,
        autoOpen : false,
        bgiframe : true,
        height : 200,
        width : 500,
        buttons:{
            Salir: function() {
                $( this ).dialog( "close" );
            }
        }
    };

    jQuery("#ui-liqconfirmation").children().remove();
    jQuery("#ui-liqconfirmation").dialog("destroy");
    jQuery("#ui-liqconfirmation").dialog(dialogOpts);
    jQuery("#ui-liqconfirmation").dialog("open");
    document.getElementById("ui-liqconfirmation").innerHTML = "<p style='text-align:center;'><b>Liquidation has been successfully!</b></p>";
}

/** ****************** B ************************* */

/** ****************** C ************************* */

/**
 * diálogo de confirmación.
 * 
 * @param cartel -mensaje de confirmación.
 * @param a - tag a html al cual se le setea el link en caso de confirmación
 * @param hred - link en caso de confirmación.
 */

function loadConceptAmount(elementId){
    cd_concept = document.getElementById(elementId).value;
    if(cd_concept !=""){
        jQuery.ajax({
            url:'doAction?action=load_concept_amount&id='+cd_concept,
            success: function(datos){
                document.getElementById('rta_amount').innerHTML = datos;
            }     
        }
        );
    }
    return false;
}

function confirmDelete(cartel, a, href) {
    jConfirm(cartel, 'Confirm', function(r) {
        if (r) {
            a.href = href;
            window.location = href;
            return true;
        } else {
            return false;
        }
    });
}

function confirmaEliminarAjax(url, divId){
    cartel="Do you confirm delete item ?";
    jConfirm(cartel, 'Confirmation', function(r) {
        if (r) {
            invocarURLConAjax(url, divId);
            return false
        } else {
            return false;
        }
    });
    return false;
}

function confirmaEliminarProductAjax(url, divId){
    cartel="Do you confirm delete item ?";
    jConfirm(cartel, 'Confirmation', function(r) {
        if (r) {
            invocarURLConAjax(url, divId);
            return false
        } else {
            return false;
        }
    });
    return false;
}

function updatePrioritiesAjax(url, divId){
    jQuery.ajax({
        url:url,
        success: function(datos){
            if(divId != undefined && document.getElementById(divId)!=undefined){
                document.getElementById(divId).innerHTML = datos;
            }
        }
    });

    return false;
}

function confirmaEliminarProductAjax(url, divId){
    cartel="Do you confirm delete item ?";
    jConfirm(cartel, 'Confirmation', function(r) {
        if (r) {
            invocarURLSincronicaConAjax(url, divId);
            document.getElementById("ds_offer").value = document.getElementById("rtaPrefix").innerHTML;
            return false
        } else {
            return false;
        }
    });
    return false;
}


function confirm_action(  message, title, onSuccess ){

    jConfirm(message,  title, function(r) {
        if (r) {
            onSuccess();
            return false;
        } else {
            return false;
        }
    });

	
}

/** ****************** D ************************* */

$(document).ready(function() {
	$('select[data-custom-event][data-custom-function]').each(function() {
		var selectElement = $(this);
		
		var customEvent = selectElement.data('custom-event');
		
		var customFunction = selectElement.data('custom-function');
		
		selectElement.on(customEvent, function() {
			var selectedValue = selectElement.val();
			window[customFunction](selectedValue);
		});
	});
	$('#volverButton').on('click', function() {
        history.back(-1);
    });
});

/** ****************** E ************************* */

/**
 * se eval�a la funci�n "onComplete" en el opener
 * @param onComplete funci�n a evaluar en el opener.
 * @return
 */
function evaluate(onComplete){
    if(onComplete!=null && onComplete!='')
        window.opener.eval(onComplete);
}

/**
 * se muestra la imagen de espera en el element html dado
 * @param elementId id del elemento html donde se mostrar� la imagen de espera.
 * @return
 */
function esperar(elementId){
    document.getElementById(elementId).innerHTML = "<center><img src='../img/ajax-loader.gif' title='cargando...' alt='cargando...' /> </center>";
}


function estado_filter_solicitud_change(jsondata){

    

	
}

/** ****************** G ************************* */

/** ****************** H ************************* */

/** ****************** I ************************* */
function invocarURLConAjax(url, divId){
    jQuery.ajax({
        url:url,
        success: function(datos){
            if(divId != undefined && document.getElementById(divId)!=undefined){
                document.getElementById(divId).innerHTML = datos;
            }
        }
    });
    return false;
}

function invocarURLSincronicaConAjax(url, divId){
    jQuery.ajax({
        url:url,
        success: function(datos){
            if(divId != undefined && document.getElementById(divId)!=undefined){
                document.getElementById(divId).innerHTML = datos;
            }
        },
        async:false
    });
    return false;
}

/** ****************** J ************************* */

function jQuery_confirm(msj) {
    var dialogOpts = {
        title : "",
        modal : true,
        autoOpen : false,
        bgiframe : true,
        buttons : {
            "Yes" : function() {
                return true;
            },
            "No" : function() {
                return false;
            }
        },
        height : 200,
        width : 300,
        open : function() {
            jQuery("#ui-dialog").load(url);
        }
    };
    jQuery("#ui-dialog").children().remove();
    jQuery("#ui-dialog").dialog("destroy");
    jQuery("#ui-dialog").dialog(dialogOpts);
    jQuery("#ui-dialog").dialog("open");
}

/** ****************** L ************************* */
function loadContent(  url  ){

    window.location = url;
//jQuery(window).load(  url );

}

/**
 * función para realizar el action con el formulario
 * de búsqueda.
 */
function list_all(action) {
	
    if(action){
        document.getElementById('validate').value = "false";
        document.getElementById('filterField').selectedIndex = 0;
        document.getElementById('filterValue').value = "";
        submit_self(action);
    }else{
        document.getElementById('validate').value = "false";
        document.getElementById('filterField').selectedIndex = 0;
        document.getElementById('filterValue').value = "";
    }
}

/** ****************** M ************************* */

/**
 * mensaje de error formateado con jquery.
 * @param mensaje - mensaje de error a mostrar
 */
function showErrorMessage(mensaje) {
    jAlert("<strong>Error</strong><br/><br/><br/>" + mensaje);
}

function navigateBack() {
  history.back(-1);
}

/** ****************** P ************************* */

/**
 * pop up est�ndar de 750x500
 * @param a - tag a html con el link para abrir el popup
 */
function popUp(a) {
    window.open(a.href, a.target,
        'width=750,height=500, ,location=center, scrollbars=YES');
    return false;
}

/**
 * pop up grande de 1024x500
 * @param a - tag a html con el link para abrir el popup
 */
function popUpBig(a) {
    window.open(a.href, a.target,
        'width=1024,height=500, ,location=center, scrollbars=YES');
    return false;

}



/** ****************** S ************************* */

/**
 * setea el valor a un input
 * @param input input a setear el valor
 * @param value valor a setear
 * @param setFocus si pasamos 1, le da el foco al input.
 * @return
 */
function setInput(input, value, setFocus){
    if(input!=null){
        input.value = value;
        if(setFocus==1){
            input.focus();
        }
    }
}

function submit_self(accion, formName){
    if( formName == 'undefined' || formName==null)
        formName = 'buscar';
    var form = document.forms[formName];
    form.accion.value = accion;
    form.target = '_self';
    form.submit();
}

function submit_blank(accion, formName){
    if( formName == 'undefined' || formName==null)
        formName = 'buscar';
	
	
    var form = document.forms[formName];
    input = document.getElementById('validate');
    if(input!=null)
        input.value = "false";
    form.accion.value = accion;
    form.target = '_blank';
    form.submit();
}

/** ****************** V ************************* */

/**
 * se verifica si se ingresó un criterio de
 * búsqueda en las ventas de listados.
 */
function checkFilter() {
    if (document.getElementById('filterValue').value == "") {
        if (document.getElementById('validate').value == "true") {
            jAlert("Fill filter value");
            return false;
        }
    }
    return true;
}

function disabledEnter(e){
    tecla=(document.all) ? e.keyCode : e.which;
    if(tecla==13){
  		
        return false;
    }else
  		
        return true;
}
function functionOnEnter(e, callback){ 
    
    tecla=(document.all) ? e.keyCode : e.which;
    if(tecla==13){
        callback();
        return false;
    }else
  		
        return true;
  	
}

function loadjQueryPopup(  url, divpopup, title, height, width ){

    if( !height )
        height = 500;

    if( !width )
        width = 750;

    var dialogOpts = {
        title : title,
        modal : true,
        autoOpen : false,
        bgiframe : true,
        height : height,
        width : width,
        open : function() {
            jQuery("#" + divpopup).load(url);
        }
    };
    jQuery("#" + divpopup).children().remove();
    jQuery("#" + divpopup).dialog("destroy");
    jQuery("#" + divpopup).dialog(dialogOpts);
    jQuery("#" + divpopup).dialog("open");

}

function proximasincronizacion(){
    jQuery.ajax({
        url:'doAction?action=next_syncronization',
        success: function(datos){
        	var data = $.parseJSON(datos);
            //document.getElementById('proxima').innerHTML = data;
        	nextSyncTime =  data;
        	
        	showNextSyncTime(nextSyncTime);
        	
        	setTimeout("setNextSyncTime()",60000);
        }
    });
    //setTimeout("proximasincronizacion()",60000);
    
}

function showNextSyncTime( data ){
	if( !data )
		document.getElementById("proxima").innerHTML= "in progress...";
	else{
		document.getElementById("proxima").innerHTML= ("0" + data[0]).slice (-2)  + ":" + ("0" + data[1]).slice (-2);
	}
}

/**
 * 
 * @param data array [0] horas, [1] minutos
 */
function setNextSyncTime()
{
	
	if( !nextSyncTime ){
		
		proximasincronizacion();
		 
		
	}else{
		var hora = 0;
		var min = 1;
		if( nextSyncTime[hora] == 0 ){
			if( nextSyncTime[min] == 0 ){
				nextSyncTime = false;
			}else{
				nextSyncTime[min] = nextSyncTime[min]-1;
			}
		}else{
			if( nextSyncTime[min] == 0 ){
				nextSyncTime[min] = 59;	
				nextSyncTime[hora] = nextSyncTime[hora]-1;
			}else{
				nextSyncTime[min] = nextSyncTime[min]-1;
			}
			
		}
		
		showNextSyncTime(nextSyncTime);
    	
    	setTimeout("setNextSyncTime()",60000);
	}
	
	
	        		
}