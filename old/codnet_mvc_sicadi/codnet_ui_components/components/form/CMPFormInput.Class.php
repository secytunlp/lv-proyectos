<?php

/**
 * input field de un formulario.
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 26-02-2013
 *
 */
class CMPFormInput extends CMPComponent{

	/**
	 * id del iput
	 * @var string
	 */
	private $id;
	
	/**
	 * propiedades del input
	 * todas las htmls válidas: "onBlur" => "algo", "name" => "nombre", etc.
	 * @var array
	 */
	private $properties;

	private $isEditable;
	
	private $isRequired;
	
	private $requiredMessage;
	
	private $requiredLabel;

	private $format;
	
	private $invalidFormatMessage;
	
	private $isVisible;
	
	private $isFilter;
	
	/**
	 * para renderizar el input.
	 * @var IFormInputRenderer
	 */
	private $renderer;

	private $value;
	
	public function __construct($id, $name, $requiredMessage="", $value="", $size=30){
	
		$this->properties = array();
		$this->isEditable = true;
		$this->isVisible = true;
		$this->isFilter = true;
		$this->setId($id);
		$this->addProperty("name", $name);
		$this->setInputValue($value);
		$this->addProperty("size", $size);
		if( !empty( $requiredMessage) ){
		
			$this->setIsRequired( true );
			$this->setRequiredLabel( "*" );
			$this->setRequiredMessage($requiredMessage); 
		}
		
		
	}
	
	
	public function show( ){
		//renderizamos el resultado.
		if( $this->isEditable )
			$renderer = $this->getRenderer();
		else
			$renderer = $this->getReadOnlyRenderer();
		$renderer = $this->getRenderer();
		return $renderer->render( $this );
	}
	
	public function getReadOnlyRenderer(){
		return new InputReadOnlyRenderer();
	}
	
	public function getId()
	{
	    return $this->id;
	}

	public function setId($id)
	{
	    $this->id = $id;
	    $this->addProperty("id", $id);
	}

	public function getProperties()
	{
	    return $this->properties;
	}

	public function setProperties($properties)
	{
	    $this->properties = $properties;
	}


	public function getIsRequired()
	{
	    return $this->isRequired;
	}

	public function getRequiredMessage()
	{
	    return $this->requiredMessage;
	}

	public function getRequiredLabel()
	{
	    return $this->requiredLabel;
	}

	public function setIsRequired($isRequired)
	{
	    $this->isRequired = $isRequired;
	}

	public function setRequiredMessage($requiredMessage)
	{
	    $this->requiredMessage = $requiredMessage;
	    //$this->addProperty("placeholder", $requiredMessage . "...");
	    
	}

	public function setRequiredLabel($requiredLabel)
	{
	    $this->requiredLabel = $requiredLabel;
	}
	
	public function addProperty( $name, $value ){
		$this->properties[$name] = $value;
	}

	public function getInvalidFormatMessage()
	{
	    return $this->invalidFormatMessage;
	}

	public function setInvalidFormatMessage($invalidFormatMessage)
	{
	    $this->invalidFormatMessage = $invalidFormatMessage;
	}

	public function getFormat()
	{
	    return $this->format;
	}

	public function setFormat($format)
	{
	    $this->format = $format;
	}

	public function setRenderer($renderer)
	{
	    $this->renderer = $renderer;
	}

	public function getRenderer(){
		return $this->renderer;
	}

	public function getType(){
		return "text";
	}
	
	public function getProperty( $key ){
		
		if(array_key_exists($key, $this->properties))
			return $this->properties[$key];
		else null;	
	}	

	public function getIsEditable()
	{
	    return $this->isEditable;
	}

	public function setIsEditable($isEditable)
	{
	    $this->isEditable = $isEditable;
	}

	public function getIsVisible()
	{
	    return $this->isVisible;
	}

	public function setIsVisible($isVisible)
	{
	    $this->isVisible = $isVisible;
	}

	public function getInputValue()
	{
	    return $this->value;
	}

	public function setInputValue($value)
	{
	    $this->value = $value;
	    $this->addProperty("value", "$value");
	}
	
	public function fillEntityValue( $entity, $formMethodType ){
	
	
		$inputName = $this->getProperty("name");

		//si tiene puntos (tipoDoc.oid), reemplazamos por "_".
		$inputNameForm = str_replace(".", "_", $inputName);
		
		
		
		$inputValue = ( $formMethodType == "POST")?CdtUtils::getParamPOST($inputNameForm,'',$this->getIsFilter()):CdtUtils::getParam($inputNameForm,'',$this->getIsFilter());
				
		CdtUtils::log("setting...type   $inputName = $inputValue ", __CLASS__, LoggerLevel::getLevelDebug());
				
				
		CdtReflectionUtils::doSetter( $entity, $inputName, $inputValue );
				
				
		CdtUtils::log("getter "   . CdtReflectionUtils::doGetter( $entity, $inputName ) , __CLASS__, LoggerLevel::getLevelDebug());
		
	}
	

	public function getIsFilter()
	{
	    return $this->isFilter;
	}

	public function setIsFilter($isFilter)
	{
	    $this->isFilter = $isFilter;
	}
}