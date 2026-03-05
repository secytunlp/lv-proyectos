<?php

class CMPFindObject extends CMPComponent{

	//label para el input.
	private $label;
	//id del input
	private $inputId;
	//name para el input.
	private $inputName;
	//size para el input.
	private $inputSize;
	//size para el input del code.
	private $inputCodeSize;
	//size para el label del input.
	private $inputLabelSize;
	//size mínimo para todo el componente.
	private $minWidthSize;
	//$item a visualizar.
	private $item;
	
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
	//el m�todo para obtener el valor a mostrar en el input (por lo que se busca).
	private $itemLabel;
	private $itemField;
	//el c�digo del item.
	private $itemCode;
	
	//funci�n javascript a llamar una vez seleccionado un item.
	private $functionCallback;
	//atributos que se le pasan a la funci�n callback.
	//debemos pasarle los nombres de los m�todos separadas por coma: "cd_tabtext, ds_tabtext, ..."
	private $itemAttributesCallback="";

	//para indicar que el valor es obligatorio.
	private $obligatorio;
	
	//mensaje para cuando es obligatorio.
	private $msgObligatorio;
		
	private $showCode=true;
	private $showPopup=true;

	//grid model para el popup
	private $gridModel;

	private $entityAutocompleteClazz;

	//propiedades para instanciar el add entity.
	private $hasAddEntity = false;	
	private $titleAddEntity;
	private $actionAddEntity;
	
	private $inputCodeValue;
	
	public function setLabel( $value ){ $this->label = $value; }
	public function getLabel(){ return $this->label; }

	public function setInputId( $value ){ $this->inputId = $value; }
	public function getInputId(){ return $this->inputId; }

	public function setInputName( $value ){ $this->inputName = $value; }
	public function getInputName(){ return $this->inputName; }

	public function setInputSize( $value ){ $this->inputSize = $value; }
	public function getInputSize(){ return $this->inputSize; }

	public function setInputLabelSize( $value ){ $this->inputLabelSize = $value; }
	public function getInputLabelSize(){ return $this->inputLabelSize; }
	
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

	public function setItem($value){ $this->item=$value; }
	public function getItem(){ return $this->item; }
	
	public function setItemClass($clazz){ $this->itemClass=$clazz; }
	public function getItemClass(){ return $this->itemClass; }

	public function setItemLabel($value){ $this->itemLabel=$value; }
	public function getItemLabel(){ return $this->itemLabel; }

	public function setItemCode($value){ $this->itemCode=$value; }
	public function getItemCode(){ return $this->itemCode; }

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

	public function setObligatorio( $value ){ $this->obligatorio = $value; }
	public function getObligatorio(){ return $this->obligatorio; }
	
	public function setMsgObligatorio( $value ){ $this->msgObligatorio = $value; }
	public function getMsgObligatorio(){ return $this->msgObligatorio; }
	
	public function setShowCode( $value ){ $this->showCode = $value; }
	public function getShowCode(){ return $this->showCode; }
	
	public function setShowPopup( $value ){ $this->showPopup = $value; }
	public function getShowPopup(){ return $this->showPopup; }
	
	
	public function show(){
		return $this->getContent();
	}



	public function getContent(){

		$xtpl = $this->getXTemplate();
			
		$this->parseFindObject( $xtpl );

		$this->parseAddEntity( $xtpl );
		
		$xtpl->parse("main");

		$content = $xtpl->text("main");

		return $content;
	}

	protected function parseAddEntity( $xtpl ){

		if($this->getHasAddEntity()){
			
			$xtpl->assign("title_add_entity", $this->getTitleAddEntity() );
			$xtpl->assign("add_entity_init_action", $this->getActionAddEntity() );
			$xtpl->parse('main.addentity');
			
		}
		
		
	}
	
	private function parseFindObject( $xtpl ){

		$label = $this->getLabel();
		if(!empty($label)){

			$xtpl->assign('label', $label );
			$xtpl->assign('labelCss', $this->getLabelCss() );
			$xtpl->parse('main.label');
		}

		$xtpl->assign('inputId', $this->getInputId());
		$xtpl->assign('inputName', $this->getInputName() );
		
		
		$valueCode = $this->getInputCodeValue();
		if(empty($valueCode))
			$valueCode = $this->getValue($this->getItem(), $this->getItemCode(), $this->getItemClass());
		
		$xtpl->assign('inputValue', $valueCode);
		$xtpl->assign('inputValueDesc', $this->getValue($this->getItem(), $this->getItemLabel(), $this->getItemClass()));
		
		$codeSize = $this->getInputCodeSize();
		if(empty($codeSize))
		$codeSize=3;
		$xtpl->assign('inputCodeSize', $codeSize);
		
		$size = $this->getInputLabelSize();
		if(empty($size))
		$size=30;
		$xtpl->assign('inputLabelSize', $size);
		
		$minWidthSize = $this->getMinWidthSize();
		if(empty($minWidthSize))
		$minWidthSize="350px";
		$xtpl->assign('minWidth', $minWidthSize);
		
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

		$callback = $this->getFunctionCallback();
		if( !empty($callback) )
		$xtpl->assign('functionCallback', $callback . "" );
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
			$xtpl->assign('desc_obligatorio', $validate);
			
			$validate = "jVal=\"{valid:function (val) { return required(val,'$msg'); }}\"";
			$xtpl->assign('code_obligatorio', $validate);
			
			
		}
			
		
		$xtpl->assign('code_type', ($this->showCode)?'text':'hidden');
			
			
		
		//seteamos el autocomplete.
		$oAutocomplete = null;
		if( empty( $this->entityAutocompleteClazz) ){
			$oAutocomplete = new CMPAutocomplete();
			$oAutocomplete->setDisabled($this->getDisabled());
			$oAutocomplete->setInputName( 'ds_' . $this->getInputId() );
			$oAutocomplete->setInputId(  'ds_' . $this->getInputId() );
			$oAutocomplete->setRequestClass( $this->getRequestClass() );
			$oAutocomplete->setItemCode( $this->getItemCode() );
			$oAutocomplete->setItemLabel( $this->getItemLabel() );
			$oAutocomplete->setItemField( $this->getItemField() );
			$oAutocomplete->setItemClass( $this->getItemClass() );
			$oAutocomplete->setInputValue( $this->getValue($this->getItem(), $this->getItemLabel(), $this->getItemClass()) );
			$oAutocomplete->setItemAttributesList( $this->getItemCode() .','.$this->getItemLabel() );
			$oAutocomplete->setFunctionCallback( "autocomplete_change_" . $this->getInputId() );
			$oAutocomplete->setItemAttributesCallback( $this->getItemCode() );
			$oAutocomplete->setInputSize( $this->getInputSize() );
			//$oAutocomplete->setFunctionCallback( $this->getFunctionCallback() );
			//$oAutocomplete->setItemAttributesCallback( $this->getItemAttributesCallback() );
			$oAutocomplete->setObligatorio( $this->getObligatorio() );
			$oAutocomplete->setMsgObligatorio( $this->getMsgObligatorio());
				
		}else{
			$oAutocomplete = CdtReflectionUtils::newInstance($this->entityAutocompleteClazz);
			$oAutocomplete->setFunctionCallback( "autocomplete_change_" . $this->getInputId() );
			$oAutocomplete->setInputSize( $this->getInputSize() );
			$oAutocomplete->setInputName( 'ds_' . $this->getInputId() );
			$oAutocomplete->setInputId(  'ds_' . $this->getInputId() );
			
		}
				
		$xtpl->assign('autocomplete', $oAutocomplete->show() );
		
		
		//popup
		if($this->showPopup && !$this->getDisabled()){
			$xtpl->assign('gridModel', $this->getGridModel());
			$xtpl->assign('callback', 'autocomplete_change_' . $this->getInputId());
			$xtpl->parse('main.popup');
		}
		
	}

	public function findItem( $text ){

		if(empty($text)){
			$jsondata['find'] = false;
			$jsondata['msg'] = "";
			return $jsondata;
		}
		
		$method = ($this->getRequestMethod())?$this->getRequestMethod():"getEntity";
		
		try{

			$item = $this->invokeMethod($this->getRequestClass() , $method, $this->getCriteria($text) );

		}catch(Exception $ex){

			$item = $this->invokeMethod($this->getRequestClass() , $method, $text );
		}
					
		$this->setItem( $item );
		
		
		//la salida es en json, hay que hacer el encode de los caracteres.
		if(!empty($item)){
			$jsondata['code'] = $this->getValue( $item, $this->getItemCode(), $this->getItemClass());
			//$jsondata['label'] = CdtUIUtils::encodeCharacters( $this->getValue( $item, $this->getItemLabel(), $this->getItemClass()) );	
			$jsondata['label'] =  $this->getValue( $item, $this->getItemLabel(), $this->getItemClass()) ;
			$jsondata['attributes'] = $this->getItemAttributes($item);
			$jsondata['find'] = true;
		}else{
			$jsondata['find'] = false;
			$jsondata['msg'] = "no existe";
		}
		
			
		return $jsondata;
	}



	public function getCriteria( $text ){
		$criterio = new CdtSearchCriteria();
		$criterio->addFilter( $this->itemCode , $text, "=", new CdtCriteriaFormatStringValue());
		return $criterio;
	}

	public function getXTemplate(){

		$xtpl = new XTemplate( CDT_CMP_TEMPLATE_FINDOBJECT );

		$xtpl->assign('WEB_PATH', WEB_PATH);


		return $xtpl;

	}
	

	public function getGridModel()
	{
	    return $this->gridModel;
	}

	public function setGridModel($gridModel)
	{
	    $this->gridModel = $gridModel;
	}
	
	/**
	 * retornamos los atributos del item encontrado
	 * @param unknown_type $item
	 * @return array atributos del item
	 */
	protected function getItemAttributes( $item ){
		
		$result = array();
		if(!empty($this->itemAttributesCallback)){

			$attributes = explode("," , $this->itemAttributesCallback );

			foreach ($attributes as $attribute) {
				$attribute = trim($attribute);
				$value = $this->getValue( $item, $attribute, $this->itemClass ) ;
				$result[$attribute] = $value   ;
			}
		}
		return $result;
	}

	public function getInputCodeSize()
	{
	    return $this->inputCodeSize;
	}

	public function setInputCodeSize($inputCodeSize)
	{
	    $this->inputCodeSize = $inputCodeSize;
	}

	public function getMinWidthSize()
	{
	    return $this->minWidthSize;
	}

	public function setMinWidthSize($minWidthSize)
	{
	    $this->minWidthSize = $minWidthSize;
	}

	public function getItemField()
	{
	    return $this->itemField;
	}

	public function setItemField($itemField)
	{
	    $this->itemField = $itemField;
	}

	public function getEntityAutocompleteClazz()
	{
	    return $this->entityAutocompleteClazz;
	}

	public function setEntityAutocompleteClazz($entityAutocompleteClazz)
	{
	    $this->entityAutocompleteClazz = $entityAutocompleteClazz;
	}

	public function getHasAddEntity()
	{
	    return $this->hasAddEntity;
	}

	public function setHasAddEntity($hasAddEntity)
	{
	    $this->hasAddEntity = $hasAddEntity;
	}

	public function getTitleAddEntity()
	{
	    return $this->titleAddEntity;
	}

	public function setTitleAddEntity($titleAddEntity)
	{
	    $this->titleAddEntity = $titleAddEntity;
	}

	public function getActionAddEntity()
	{
	    return $this->actionAddEntity;
	}

	public function setActionAddEntity($actionAddEntity)
	{
	    $this->actionAddEntity = $actionAddEntity;
	}

	public function getInputCodeValue()
	{
	    return $this->inputCodeValue;
	}

	public function setInputCodeValue($inputCodeValue)
	{
	    $this->inputCodeValue = $inputCodeValue;
	}
}
?>