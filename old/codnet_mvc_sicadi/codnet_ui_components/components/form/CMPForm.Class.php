<?php

/**
 * componente form.
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 26-02-2013
 *
 */
class CMPForm extends CMPComponent{

	/**
	 * id del formulario
	 * @var string
	 */
	private $id;
	
	/**
	 * propiedades del formulario
	 * todas las htmls válidas: "onSubmit" => "algo", "method" => "POST", etc.
	 * @var array
	 */
	private $properties;
	
	/**
	 * array de fieldset
	 * @var array
	 */
	private $fieldsets;

	/**
	 * array de fields hidden
	 * @var array
	 */
	private $hiddens;
	
	/**
	 * label para el input cancel.
	 * @var string
	 */
	private $cancelLabel;
	
	
	/**
	 * label para el input submit.
	 * @var string
	 */
	private $submitLabel;
	
	/**
	 * acción a ejecutar en el formulario (submit)
	 * @var string
	 */
	private $action;

	/**
	 * 
	 * @var string
	 */
	private $onSubmit;
	
	/**
	 * para el cancel del formulario
	 * @var string
	 */
	private $onCancel;
	
	/**
	 * responsable de renderizar el formulario.
	 * @var IFormRenderer
	 */
	private $renderer;

	/**
	 * para determinar si se utiliza ajax para enviar el formulario.
	 * @var boolean
	 */
	private $useAjaxSubmit;
	
	/**
	 * para determinar si se utiliza ajax al ejecutar
	 * el callback (onReset o después del submit).
	 * @var boolean
	 */
	private $useAjaxCallback;
	
	/**
	 * id del componente html donde mostrar la respuesta
	 * del callback en caso de que se utilice ajax.
	 * @var string
	 */
	private $idAjaxCallback;
	
	
	/**
	 * función javascript a ejecutar antes de llamar al submit
	 * @var string
	 */
	private $beforeSubmit;
	
	/**
	 * función javascript para invocar en caso de success.
	 * @var string
	 */
	private $onSuccessCallback;
	
	/**
	 * función javascript para invocar en caso de error.
	 * @var string
	 */
	private $onErrorCallback;

	/**
	 * texto de referencia sobre los campos requeridos.
	 * @var string.
	 */
	private $requiredFieldsMessage;

	/**
	 * para determinar si es editable a no.
	 * @var boolean
	 */
	private $isEditable;
	
	/**
	 * para agregar html
	 * @var string
	 */
	private $customHTML;
	
	/**
	 * para agregar html dentro del form
	 * @var string
	 */
	private $intoFormCustomHTML;
	
	
	private $buttons;
	
	
	public function __construct( $id="editform",$submitLbl= CDT_UI_LBL_SAVE, $cancelLbl=CDT_UI_LBL_CANCEL){
		$this->properties = array();
		$this->fieldsets = array();
		$this->hiddens = array();
		$this->buttons = array();
		$this->renderer = new DefaultFormRenderer();
		$this->setId($id);
		$this->setSubmitLabel($submitLbl);
		$this->setCancelLabel($cancelLbl);
		$this->setRequiredFieldsMessage( CDT_UI_MSG_REQUIRED_FIELDS );
		$this->setOnSuccessCallback("successDefault_$id");
		$this->setUseAjaxSubmit( false );
		$this->setUseAjaxCallback( false );
		$this->setIsEditable( true );

		
	}
	
	
	public function show( ){
		//renderizamos el resultado.
		return $this->getRenderer()->render( $this );
	}
	
	
	public function getProperty( $key ){
		if(array_key_exists($key, $this->properties))
			return $this->properties[$key];
		else return null;

	}



	/**
	 * setea en los inputs los valores de la entidad.
	 * @param unknown_type $entity
	 */
	public function fillInputValues( $entity ){
		
		$type='';
		foreach ($this->getFieldsets() as $fieldset) {
			foreach ($fieldset->getFields() as $field) {
				$input = $field->getInput();
				$inputName = $input->getProperty("name");
				$value = CdtReflectionUtils::doGetter( $entity, $inputName );
				$input->setInputValue($value );
			}
		}
		
		foreach ($this->getHiddens() as $input) {
			$inputName = $input->getProperty("name");
			$inputValue = ( $type == "POST")?CdtUtils::getParamPOST($inputName):CdtUtils::getParam($inputName);
			$value = CdtReflectionUtils::doGetter( $entity, $inputName );
			$input->setInputValue( $value );
		}
	}	
	
	/**
	 * setea en la entidad los valores del form.
	 * @param unknown_type $entity
	 */
	public function fillEntityValues( $entity ){
		
		//determinamos si es por POST o por GET.
		$type = $this->getProperty("method");
		
		foreach ($this->getFieldsets() as $fieldset) {
			foreach ($fieldset->getFields() as $field) {
				
				$input = $field->getInput();
				/*
				
				$inputName = $input->getProperty("name");
				//si tiene puntos (tipoDoc.oid), reemplazamos por "_".
				$inputNameForm = str_replace(".", "_", $inputName);

				$inputValue = ( $type == "POST")?CdtUtils::getParamPOST($inputNameForm):CdtUtils::getParam($inputNameForm);
				
				CdtUtils::log("setting...type $type...  $inputName = $inputValue ", __CLASS__, LoggerLevel::getLevelDebug());
				
				
				CdtReflectionUtils::doSetter( $entity, $inputName, $inputValue );
				
				
				CdtUtils::log("getter "   . CdtReflectionUtils::doGetter( $entity, $inputName ) , __CLASS__, LoggerLevel::getLevelDebug());
				*/
				
				$input->fillEntityValue($entity, $type);
			}
		}

		//setea en la entidad los valores del form.
		foreach ($this->getHiddens() as $input) {
			/*
			$inputName = $input->getProperty("name");
			$inputNameForm = str_replace(".", "_", $inputName);
			$inputValue = ( $type == "POST")?CdtUtils::getParamPOST($inputNameForm):CdtUtils::getParam($inputNameForm);
			//$inputValue = ( $type == "POST")?CdtUtils::getParamPOST($inputName):CdtUtils::getParam($inputName);
			CdtUtils::log("setting hidden...  $inputName = $inputValue ", __CLASS__, LoggerLevel::getLevelDebug());
			CdtReflectionUtils::doSetter( $entity, $inputName, $inputValue );
			*/
			$input->fillEntityValue($entity, $type);
		}
		
		//una vez que la entity tiene los valores seteados, llenamos los inputs de sesión
		//$this->fillInputSessionValues($entity);
		
	}	

	public function getId()
	{
	    return $this->id;
	}

	public function setId($id)
	{
	    $this->id = $id;
	    $this->addProperty("id", $id);
	    $this->addProperty("name", $id);
	    //$this->setOnSubmit("return validate('$id');return false;");
	}

	public function getProperties()
	{
	    return $this->properties;
	}

	public function setProperties($properties)
	{
	    $this->properties = $properties;
	}

	public function getFieldsets()
	{
	    return $this->fieldsets;
	}

	public function setFieldsets($fieldsets)
	{
	    $this->fieldsets = $fieldsets;
	}

	public function getRenderer()
	{
	    return $this->renderer;
	}

	public function setRenderer($renderer)
	{
	    $this->renderer = $renderer;
	}

	public function addProperty( $name, $value ){
		$this->properties[$name] = $value;
	}
	
	public function addFieldset( FormFieldset $fieldset ){
		$this->fieldsets[] = $fieldset;
	}

	public function addHidden( CMPFormInputHidden $input ){
		$this->hiddens[$input->getId()] = $input;
	}
	
	public function getHidden( $inputId ){
		return $this->hiddens[$inputId];
	}
	
	public function getCancelLabel()
	{
	    return $this->cancelLabel;
	}

	public function setCancelLabel($label)
	{
	    $this->cancelLabel = $label;
	}

	public function getSubmitLabel()
	{
	    return $this->submitLabel;
	}

	public function setSubmitLabel($submitLabel)
	{
	    $this->submitLabel = $submitLabel;
	}

	public function getAction()
	{
	    return $this->action;
	}

	public function setAction($action)
	{
	    $this->action = $action;
	    $this->addProperty("action", $action);
	}

	public function getUseAjaxSubmit()
	{
	    return $this->useAjaxSubmit;
	}

	public function setUseAjaxSubmit($useAjax)
	{
	    $this->useAjaxSubmit = $useAjax;
	}

	public function getOnSuccessCallback()
	{
	    return $this->onSuccessCallback;
	}

	public function setOnSuccessCallback($onSuccessCallback)
	{
	    $this->onSuccessCallback = $onSuccessCallback;
	}

	public function getOnErrorCallback()
	{
	    return $this->onErrorCallback;
	}

	public function setOnErrorCallback($onErrorCallback)
	{
	    $this->onErrorCallback = $onErrorCallback;
	}

	public function getOnSubmit()
	{
	    return $this->onSubmit;
	}

	public function setOnSubmit($onSubmit)
	{
	    $this->onSubmit = $onSubmit;
	    $this->addProperty("onsubmit", $onSubmit);
	}

	public function getRequiredFieldsMessage()
	{
	    return $this->requiredFieldsMessage;
	}

	public function setRequiredFieldsMessage($requiredFieldsMessage)
	{
	    $this->requiredFieldsMessage = $requiredFieldsMessage;
	}

	public function getHiddens()
	{
	    return $this->hiddens;
	}

	public function setHiddens($hiddens)
	{
	    $this->hiddens = $hiddens;
	}

	public function getOnCancel()
	{
	    return $this->onCancel;
	}

	public function setOnCancel($onCancel)
	{
	    $this->onCancel = $onCancel;
	}

	public function getUseAjaxCallback()
	{
	    return $this->useAjaxCallback;
	}

	public function setUseAjaxCallback($useAjaxCallback)
	{
	    $this->useAjaxCallback = $useAjaxCallback;
	}

	public function getIdAjaxCallback()
	{
	    return $this->idAjaxCallback;
	}

	public function setIdAjaxCallback($idAjaxCallback)
	{
	    $this->idAjaxCallback = $idAjaxCallback;
	}

	public function getIsEditable()
	{
	    return $this->isEditable;
	}

	public function setIsEditable($isEditable)
	{
	    $this->isEditable = $isEditable;
		foreach ($this->getFieldsets() as $fieldset) {
			foreach ($fieldset->getFields() as $field) {
				$input = $field->getInput();
				$input->setIsEditable( $isEditable );
			}
		}
	}
	
	public function getCustomHTML(){
		return $this->customHTML;
	}
	
	public function setCustomHTML($customHTML)
	{
	    $this->customHTML = $customHTML;
	}
	
	public function getIntoFormCustomHTML(){
		return $this->intoFormCustomHTML;
	}
	
	public function setIntoFormCustomHTML($customHTML)
	{
		$this->intoFormCustomHTML = $customHTML;
	}
	
	public function getInput($name){
	
		foreach ($this->getFieldsets() as $fieldset) {
			foreach ($fieldset->getFields() as $field) {
				$input = $field->getInput();
				$inputName = $input->getProperty("name");
				if( $inputName  == $name )
					return $input;
			}
		}
	}

	public function getBeforeSubmit()
	{
	    return $this->beforeSubmit;
	}

	public function setBeforeSubmit($beforeSubmit)
	{
	    $this->beforeSubmit = $beforeSubmit;
	}
	
	public function cleanSavedFields(){
		//CdtUtils::log("cleanSavedFields(" . $this->getId()  . ")", __CLASS__, LoggerLevel::getLevelDebug());
		unset( $_SESSION[$this->getId()] );
	}
	
	public function saveField($name, $value){
		
		$nametosave = str_replace('.', '_', $name);
		$_SESSION[$this->getId()][$nametosave] = $value;
		//CdtUtils::log("saveField($nametosave, $value)", __CLASS__, LoggerLevel::getLevelDebug());
	}
	
	public function getSavedField($name){
		
		$nametosave = str_replace('.', '_', $name);
		$value = (isset($_SESSION[$this->getId()][$nametosave] ))?$_SESSION[$this->getId()][$nametosave] :null;
		//CdtUtils::log("getSavedField($nametosave) = $value", __CLASS__, LoggerLevel::getLevelDebug());
		return $value;
	}

	/**
	 * setea en la entidad los valores del form guardados en sesión.
	 * @param unknown_type $entity
	 */
	public function fillEntitySavedFields( $entity ){
		
		$type='';
		foreach ($this->getFieldsets() as $fieldset) {
			foreach ($fieldset->getFields() as $field) {
				$input = $field->getInput();
				$inputName = $input->getProperty("name");
				//si tiene puntos (tipoDoc.oid), reemplazamos por "_".
				$inputNameForm = str_replace(".", "_", $inputName);

				$inputValue = $this->getSavedField($inputNameForm);
				
				if( !empty($inputValue) ){
					//CdtUtils::log("fillEntitySessionValues...type $type...  $inputName = $inputValue ", __CLASS__, LoggerLevel::getLevelDebug());
					CdtReflectionUtils::doSetter( $entity, $inputName, $inputValue );
				}
				
			}
		}

		//setea en la entidad los valores del form.
		/*
		foreach ($this->getHiddens() as $input) {
			$inputName = $input->getProperty("name");
			$inputValue = $this->getSavedField($inputNameForm);
			CdtReflectionUtils::doSetter( $entity, $inputName, $inputValue );
		}*/
		
	}

	/**
	 * setea en sesión los valores de la entidad.
	 * @param unknown_type $entity
	 */
	public function saveFields( $entity ){
		
		//primero limpiamos la búsqueda anterior.
		$this->cleanSavedFields();
		
		foreach ($this->getFieldsets() as $fieldset) {
			foreach ($fieldset->getFields() as $field) {
				$input = $field->getInput();
				$inputName = $input->getProperty("name");
				$value = CdtReflectionUtils::doGetter( $entity, $inputName );

				if( !empty($value) ){
					
					$this->saveField($inputName, $value);
					
				}
			}
		}
		
		/*
		foreach ($this->getHiddens() as $input) {
			$inputName = $input->getProperty("name");
			$value = CdtReflectionUtils::doGetter( $entity, $inputName );
			$this->saveField($inputName, $value);
		}*/
	}	
	
	/**
	 * retorna un array con los campos que hay que filtrar.
	 * @param unknown_type $entity
	 */
	public function getFieldsValues( $entity ){
		
		$fieldValues = array();		
		foreach ($this->getFieldsets() as $fieldset) {
			foreach ($fieldset->getFields() as $field) {
				$input = $field->getInput();
				$inputName = $input->getProperty("name");
				$value = CdtReflectionUtils::doGetter( $entity, $inputName );

				if( !empty($value) ){
					
					$fieldValues[$inputName] = $value;
					
				}
			}
		}
		return $fieldValues;
		/*
		foreach ($this->getHiddens() as $input) {
			$inputName = $input->getProperty("name");
			$value = CdtReflectionUtils::doGetter( $entity, $inputName );
			$this->saveField($inputName, $value);
		}*/
	}
	
	public function addButton( $label, $onclick ){
	
		$this->buttons[ $label ] = $onclick ;
	}
	
	public function getButtons(){
		return $this->buttons;
	}
	
}