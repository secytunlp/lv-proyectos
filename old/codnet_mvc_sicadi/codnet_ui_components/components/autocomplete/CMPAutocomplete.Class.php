<?php
/*
 * Componente para autocomplete de una entidad x.
 *
 */

class CMPAutocomplete extends CMPComponent{

	//label para el input.
	private $label;
	//id del input
	private $inputId;
	//name para el input.
	private $inputName;
	//value default para el input.
	private $inputValue;
	//size para el input.
	private $inputSize;

	private $extraParam=""; //TODO ver para qu� es.
	private $disabled=false;

	//estilos para el input y el label.
	private $labelCss;
	private $inputCss;

	//clase que atiende el listado.
	private $requestClass;
	//m�todo a invocar sobre la clase anterior para obtener los items.
	private $requestMethod;

	//para determinar la clase del item.
	//si no se define nada el item ser� un array.
	private $itemClass;
	//el método para obtener el valor a mostrar en el input (por lo que se busca).
	private $itemLabel;
	private $itemField;
	
	//el c�digo del item.
	private $itemCode;
	//atributos a visualizar en el dropdown list.
	//debemos pasarle los nombres de los m�todos separadas por coma: "cd_tabtext, ds_tabtext, ..."
	private $itemAttributesList="";

	//funci�n javascript a llamar una vez seleccionado un item.
	private $functionCallback;
	//atributos que se le pasan a la funci�n callback.
	//debemos pasarle los nombres de los m�todos separadas por coma: "cd_tabtext, ds_tabtext, ..."
	private $itemAttributesCallback="";

	//para indicar que el valor es obligatorio.
	private $obligatorio;
	
	//mensaje para cuando es obligatorio.
	private $msgObligatorio;

	public function setLabel( $value ){ $this->label = $value; }
	public function getLabel(){ return $this->label; }

	public function setInputId( $value ){ $this->inputId = $value; }
	public function getInputId(){ return $this->inputId; }

	public function setInputName( $value ){ $this->inputName = $value; }
	public function getInputName(){ return $this->inputName; }

	public function setInputValue( $value ){ $this->inputValue = $value; }
	public function getInputValue(){ return $this->inputValue; }

	public function setInputSize( $value ){ $this->inputSize = $value; }
	public function getInputSize(){ return $this->inputSize; }

	public function setExtraParam( $value ){ $this->extraParam = $value; }
	public function getExtraParam(){ return $this->extraParam; }

	public function setDisabled( $value ){ $this->disabled = $value; }
	public function getDisabled(){ return $this->disabled; }

	public function setLabelCss( $value ){ $this->labelCss = $value; }
	public function getLabelCss(){ return $this->labelCss; }

	public function setInputCss( $value ){ $this->inputCss = $value; }
	public function getInputCss(){ return $this->inputCss; }

	public function setRequestClass( $value ){ $this->requestClass = $value; }
	public function getRequestClass(){ return $this->requestClass; }

	public function setRequestMethod( $value ){ $this->requestMethod = $value; }
	public function getRequestMethod(){ return $this->requestMethod; }

	public function setItemClass($clazz){ $this->itemClass=$clazz; }
	public function getItemClass(){ return $this->itemClass; }

	public function setItemLabel($value){ $this->itemLabel=$value; }
	public function getItemLabel(){ return $this->itemLabel; }

	public function setItemCode($value){ $this->itemCode=$value; }
	public function getItemCode(){ return $this->itemCode; }

	public function setItemAttributesList( $value ){ 
		
		//nos aseguramos de que no existen espacios
		$attributes = explode("," , $value );
		$results = array();
		foreach ($attributes as $attribute) {
			$results[] = trim($attribute);
		}
		
		$this->itemAttributesList = implode(",", $results); 
	
	}
	public function getItemAttributesList(){ return $this->itemAttributesList; }

	public function setFunctionCallback( $value ){ $this->functionCallback = $value; }
	public function getFunctionCallback(){ return $this->functionCallback; }

	public function setItemAttributesCallback( $value ){ 

		//nos aseguramos de que no existen espacios
		$attributes = explode("," , $value );
		$results = array();
		foreach ($attributes as $attribute) {
			$results[] = trim($attribute);
		}
		$this->itemAttributesCallback = implode(",", $results); 
	
	}
	public function getItemAttributesCallback(){ return $this->itemAttributesCallback; }

	public function setShowAttributes( $value ){ $this->showAttributes = $value; }
	public function getShowAttributes(){ return $this->showAttributes; }

	public function setObligatorio( $value ){ $this->obligatorio = $value; }
	public function getObligatorio(){ return $this->obligatorio; }
	
	public function setMsgObligatorio( $value ){ $this->msgObligatorio = $value; }
	public function getMsgObligatorio(){ return $this->msgObligatorio; }
	

	public function show(){
		return $this->getContent();
	}

	public function getContent(){

		$xtpl = $this->getXTemplate();
			
		$this->parseAutocomplete( $xtpl );

		$xtpl->parse("main.autocomplete");

		$xtpl->parse("main");
		$content = $xtpl->text("main");

		return $content;
	}

	private function parseAutocomplete( $xtpl ){

		$label = $this->getLabel();
		if(!empty($label)){

			$xtpl->assign('label', $label );
			$xtpl->assign('labelCss', $this->getLabelCss() );
			$xtpl->parse('main.autocomplete.label');
		}

		$xtpl->assign('inputId', $this->getInputId());
		$xtpl->assign('inputName', $this->getInputName() );
		$xtpl->assign('inputValue', $this->getInputValue());

		$size = $this->getInputSize();
		if(empty($size))
		$size=30;
		$xtpl->assign('inputSize', $size);

		$extraParam = $this->getExtraParam();
		if( !empty($extraParam) )
		$xtpl->assign('extraParam', ", extraParamFromInput: '#" . $extraParam."'");
		else
		$xtpl->assign('extraParam', '');

		$disabled = $this->getDisabled();
		if( $disabled )
		$xtpl->assign('disabled',  'disabled' );
		else
		$xtpl->assign('disabled', '');

			
		$xtpl->assign('inputCss', $this->getInputCss() );

		$xtpl->assign('requestClass', $this->getRequestClass() );
		$xtpl->assign('requestMethod', $this->getRequestMethod() );
			
		$xtpl->assign('itemClass', $this->getItemClass() );
		$xtpl->assign('itemCode', $this->getItemCode() );
		$xtpl->assign('itemLabel', $this->getItemLabel());
		$xtpl->assign('itemField', $this->getItemField());

		$attributes = $this->getItemAttributesList();
		if( !empty($attributes) ){
			$xtpl->assign('itemAttrList', $attributes);
		}

		$callback = $this->getFunctionCallback();
		if( !empty($callback) )
		$xtpl->assign('functionCallback', ", " . $callback );
		else
		$xtpl->assign('functionCallback', "" );

		$attributes = $this->getItemAttributesCallback();
		if( !empty($attributes) ){
			$xtpl->assign('attributeCallback', ", attrCallBack: 'rel'");
			$xtpl->assign('itemAttrCallback', $attributes);
		}
		else
		$xtpl->assign('attributeCallback', '');
			
		if($this->obligatorio){
			$msg = (empty($this->msgObligatorio))?"Ingrese un valor":$this->msgObligatorio;
			$validate = "jVal=\"{valid:function (val) { return required(val,'$msg'); }}\"";
			$xtpl->assign('obligatorio', $validate);
		}
	}

	public function getItems( $text ){

		//si no se define el m�todo requestMethod, usamos por default "getEntidades($criterio)" de la interface "IListar"
		$method = ($this->getRequestMethod())?$this->getRequestMethod():"getEntities";
		
		//vemos si el par�metros es un criterio de b�squeda o s�lo un texto.
		$oParameter = new ReflectionParameter(array($this->getRequestClass(), $method), 0);

		try{
		
			if( ($oParameter->getClass()!=null) && $oParameter->getClass()->getName() == 'CdtSearchCriteria' ){
				
				$items = $this->invokeMethod($this->getRequestClass() , $method, $this->getCriteria($text) );
				
			}else{
				
				$items = $this->invokeMethod($this->getRequestClass() , $method, $text );
			}
		}catch(Exception $ex){

			echo $ex->getMessage();
		}
		/*
		try{

			$items = $this->invokeMethod($this->getRequestClass() , $method, $this->getCriterio($text) );

		}catch(Exception $ex){

			$items = $this->invokeMethod($this->getRequestClass() , $method, $text );
		}*/
			
		return $this->formatItems( $items, $text );
	}

	public  function formatItems( $items, $text ){
		$content =  '<ul>'."\n";
		$even = true;
		$index=0;

		if( $items !=null )
		foreach ($items as $item) {

			$label = $this->getValue($item, $this->itemLabel, $this->itemClass);
			$code = $this->getValue($item, $this->itemCode, $this->itemClass);

			$p =  $label ;
			$p = preg_replace('/(' . $text . ')/i', '<span style="font-weight:bold;font-style:italic;">$1</span>', $p);

			//armamos el atributo "rel" con los atributos adicionales (separados por _ )
			$rel = $this->getRel( $item );

			//armamos lo que se muestra del item en el dropdown.
			$label_list = $this->getItemDropDown( $item );

			$p .= "<br /> $label_list"  ;

			$li_style = ($even)? 'autocomplete_li_even': 'autocomplete_li_odd';

			$content .= "\t".'<li class="' .  $li_style  .  '" id="autocomplete_'. $code .'" label="' .  $label .  '" rel="'. $rel .'">'. ( $p ) .'</li>'."\n";

			$even = !$even;
			
			$index++;
			
			if($index>10)
				break;
		}
		$content .= '</ul>';
		return $content;
	}

	protected function getItemDropDown( $item ){

		$label_list = "";
		if(!empty($this->itemAttributesList)){

			$attributes = explode("," , $this->itemAttributesList );

			foreach ($attributes as $attribute) {
				$value = $this->getValue( $item, $attribute,$this->itemClass ) ;
				$label_list .= "- $value "   ;
			}
			$label_list = substr( $label_list, 2, strlen($label_list)-2); //quitamos el primer "- "
		}else
			$label_list = "$code - $label";
			
		return $label_list;	
	}

	protected function getRel( $item ){
		//armamos el atributo "rel" con los atributos adicionales (separados por _ )
		$rel = "";
		if(!empty($this->itemAttributesCallback)){

			$attributes = explode("," , $this->itemAttributesCallback );

			foreach ($attributes as $attribute) {
				$value = $this->getValue( $item, $attribute, $this->itemClass ) ;
				$rel .= "_*_$value"   ;
			}
			$rel = substr( $rel, 3, strlen($rel)-3); //quitamos el primer "_*_"

		}
		return $rel;
	}

	public function getCriteria($text){
		$criterio = new CdtSearchCriteria();
		
		$field = $this->getItemField();
		
		if( empty($field) ){
			$field = $this->getItemLabel();
		}
		
		$criterio->addFilter( $field , $text, "like", new CdtCriteriaFormatLikeValue());
		
		return $criterio;
		
	}


	public function getXTemplate(){

		$xtpl = new XTemplate( CDT_CMP_TEMPLATE_AUTOCOMPLETE );

		$xtpl->assign('WEB_PATH', WEB_PATH);


		return $xtpl;

	}

	public function getItemField()
	{
	    return $this->itemField;
	}

	public function setItemField($itemField)
	{
	    $this->itemField = $itemField;
	}
}
?>