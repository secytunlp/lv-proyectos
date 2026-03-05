<?php

/**
 * input para buscar una entity.
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 30/05/2013
 */
class CMPFormFindEntity extends CMPFormInput{

	/**
	 * input para el código
	 * @var CMPFormInput
	 */
	private $inputCode;

	/**
	 * input para el autocomplete
	 * @var CMPFormEntityAutocomplete
	 */
	private $inputAutocomplete;

	/**
	 * nombre de la clase que implementa IObjectFinder para obtener la entity.
	 * @var string 
	 */
	private $finderClazz;
	
	/**
	 * true si mostramos la lupa para buscar con el popup
	 * @var boolean
	 */
	private $hasPopup;

	/**
	 * nombre de la clase del gridmodel
	 * @var string 
	 */
	private $gridModelClazz;
	
	/**
	 * nombre de la clase del grid
	 * @var string 
	 */
	private $gridClazz;
	
	/**
	 * acción para el link de agregar nueva entity
	 * @var string
	 */
	private $addEntityAction;

	/**
	 * width para el popup del grid para buscar.
	 *
	 * @var int
	 */
	private $widthPopup;
	
	/**
	 * height para el popup del grid para buscar.
	 * @var int
	 */
	private $heightPopup;
	
	/**
	 * width para el popup para agregar una entity.
	 * @var int
	 */
	private $widthAddEntityPopup;
	
	/**
	 * height para el popup para agregar una entity.
	 * @var int
	 */
	private $heightAddEntityPopup;
	
	/**
	 * para filtrar las entitades relacionadas a un parent.
	 * @var string
	 */
	private $parent;

	/**
	 * atributos que se le pasan a la función callback.
	 * debemos pasarle los nombres de los métodos separadas por coma: "cd_tabtext, ds_tabtext, ..."
	 * @var unknown_type
	 */
	private $itemAttributesCallback="";

	/**
	 * función callback
	 * @var string
	 */
	private $functionCallback="";

	/**
	 * size para los inputs del code y del label
	 * @var array
	 */
	private $inputsSize = array();

	/**
	 * Tamanio ancho mínimo para el find.
	 * (para evitar que salga en varias filas)
	 * @var string
	 */
	private $minWidth="";
	
	public function __construct($id, $name, $requiredMessage="", $value=null, $codeSize=5, $labelSize=25){
		$size='';
		parent::__construct($id, $name, $requiredMessage, $value, $size);
		
		$this->setRenderer(new InputFindEntityRenderer() );
		
		$this->setHasPopup( true );
		
		$this->setAddEntityAction( null );
		
		$this->setWidthPopup(700);
		$this->setHeightPopup(500);
		
		$this->setWidthAddEntityPopup(700);
		$this->setHeightAddEntityPopup(300);
		
		$this->setInputSize($codeSize,$labelSize);
	}
	
	
	public function setInputValue($value){
	    parent::setInputValue($value);
	    
	    if($this->getInputCode() != null)
	    	$this->getInputCode()->setInputValue($value);
	    
	}
	

	public function getInputCode()
	{
	    return $this->inputCode;
	}

	public function setInputCode($inputCode)
	{
	    $this->inputCode = $inputCode;
	    $inputCode->addProperty("size", $this->inputsSize[0]);
	}

	public function getInputAutocomplete()
	{
	    return $this->inputAutocomplete;
	}

	public function setInputAutocomplete($inputAutocomplete)
	{
	    $this->inputAutocomplete = $inputAutocomplete;
	    
	    $autocomplete =  $inputAutocomplete->getAutocomplete();
	    $autocomplete->setFunctionCallback( "findentity_autocomplete_callback" . $this->getId() );
	    
	    $autocomplete->setParent( $this->getParent() );
	    
	    $autocomplete->setInputSize($this->inputsSize[1]);
	    
	}

	public function getHasPopup()
	{
	    return $this->hasPopup;
	}

	public function setHasPopup($hasPopup)
	{
	    $this->hasPopup = $hasPopup;
	}

	public function getHasAddEntity()
	{
	    return !empty($this->addEntityAction);
	}


	public function getAddEntityAction()
	{
	    return $this->addEntityAction;
	}

	public function setAddEntityAction($addEntityAction)
	{
	    $this->addEntityAction = $addEntityAction;
	}

	public function getFinderClazz()
	{
	    return $this->finderClazz;
	}

	public function setFinderClazz($finderClazz)
	{
	    $this->finderClazz = $finderClazz;
	}

	public function getGridModelClazz()
	{
	    return $this->gridModelClazz;
	}

	public function setGridModelClazz($gridModelClazz)
	{
	    $this->gridModelClazz = $gridModelClazz;
	}

	public function getWidthPopup()
	{
	    return $this->widthPopup;
	}

	public function setWidthPopup($widthPopup)
	{
	    $this->widthPopup = $widthPopup;
	}

	public function getHeightPopup()
	{
	    return $this->heightPopup;
	}

	public function setHeightPopup($heightPopup)
	{
	    $this->heightPopup = $heightPopup;
	}

	public function getWidthAddEntityPopup()
	{
	    return $this->widthAddEntityPopup;
	}

	public function setWidthAddEntityPopup($widthAddEntityPopup)
	{
	    $this->widthAddEntityPopup = $widthAddEntityPopup;
	}

	public function getHeightAddEntityPopup()
	{
	    return $this->heightAddEntityPopup;
	}

	public function setHeightAddEntityPopup($heightAddEntityPopup)
	{
	    $this->heightAddEntityPopup = $heightAddEntityPopup;
	}

	public function getGridClazz()
	{
	    return $this->gridClazz;
	}

	public function setGridClazz($gridClazz)
	{
	    $this->gridClazz = $gridClazz;
	}

	public function getParent()
	{
	    return $this->parent;
	}

	public function setParent($parent)
	{
		$this->parent = $parent;
		$this->inputAutocomplete->getAutocomplete()->setParent( $this->getParent() );
	}

	public function getItemAttributesCallback()
	{
	    return $this->itemAttributesCallback;
	}

	public function setItemAttributesCallback($itemAttributesCallback)
	{
	    $this->itemAttributesCallback = $itemAttributesCallback;
	}
	

	public function getFunctionCallback()
	{
	    return $this->functionCallback;
	}

	public function setFunctionCallback($functionCallback)
	{
	    $this->functionCallback = $functionCallback;
	}
	
	
	public function setInputSize($codeSize=8, $autocompleteSize=30){
		
		$this->inputsSize[] = $codeSize;
		$this->inputsSize[] = $autocompleteSize;
		
		if($this->getInputAutocomplete()!=null )
			$this->getInputAutocomplete()->getAutocomplete()->setInputSize($autocompleteSize);
			
		if($this->getInputCode()!=null)
			$this->getInputCode()->addProperty("size", $codeSize);
	}

	public function getInputsSize()
	{
	    return $this->inputsSize;
	}

	public function setInputsSize($inputsSize)
	{
	    $this->inputsSize = $inputsSize;
	}

	public function getMinWidth()
	{
	    return $this->minWidth;
	}

	public function setMinWidth($minWidth)
	{
	    $this->minWidth = $minWidth;
	}
	
	public function setIsEditable($isEditable)
	{
		parent::setIsEditable($isEditable);
		
		if($this->getInputAutocomplete()!=null )
			$this->getInputAutocomplete()->setIsEditable($isEditable);
			
		if($this->getInputCode()!=null)
			$this->getInputCode()->setIsEditable($isEditable);		
	}
	
	public function setIsVisible($isVisible)
	{
		$this->isVisible = $isVisible;
	}
}