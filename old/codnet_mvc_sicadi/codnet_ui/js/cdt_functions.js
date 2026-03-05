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

/** ****************** B ************************* */

/** ****************** C ************************* */

/**
 * diálogo de confirmación.
 * 
 * @param cartel -mensaje de confirmación.
 * @param a - tag a html al cual se le setea el link en caso de confirmación
 * @param hred - link en caso de confirmación.
 */
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

/** ****************** G ************************* */

/** ****************** H ************************* */

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

function loadTargetBlank(  url  ){

	window.open( url);
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