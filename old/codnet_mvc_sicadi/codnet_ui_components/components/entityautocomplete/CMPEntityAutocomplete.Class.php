<?php
/**
 * Componente para buscar una entity por autocomplete
 * @author bernardo
 * @since 30/05/2013
 */
abstract class CMPEntityAutocomplete extends CMPComponent{

	private $inputId;
	private $inputLabel;
	private $inputName;
	private $inputSize;
	private $inputCss;
	private $inputLabelCss;
	private $inputValue;
	private $requiredMessage;

	/**
	 * propiedades a visualizar de la entidad en el listado
	 * de opciones del autocomplete
	 * @var array
	 */
	private $propertiesList = array();	

	/**
	 * función callback al seleccionar una opción
	 * del autocomplete.
	 * @var string
	 */
	private $functionCallback;
	
	/**
	 * propiedades para la función javascript que se
	 * ejecutará al elegir la entidad del listado
	 * de opciones del autocomplete (callback)
	 * @var array
	 */
	private $propertiesCallback = array();	
	
	/**
	 * valor para el parent.
	 * @var int
	 */
	private $parent="";
	
	private $disabled;
	
	
	protected abstract function getEntityClazz();
	protected abstract function getFieldCode();
	protected abstract function getFieldSearch();
	protected abstract function getEntityManager();
	
	
	
	
	protected function getFieldSearchParent(){
		return null;
	}
	
	public function getItemCode(){
		return $this->getFieldCode();
	}
	protected function getEntities($text, $parent=null){
		return $this->getEntityManager()->getEntities( $this->getCriteria($text, $parent) );
	}
	
	public function getEntityLabel($entity){
		return CdtReflectionUtils::doGetter($entity, $this->getFieldSearch());
	}
	
	public function getEntityCode(){
$entity='';
		return CdtReflectionUtils::doGetter($entity, $this->getFieldCode());
	}
	
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

		
		$this->renderInputLabel($xtpl);
		
		$xtpl->assign("inputId", $this->getInputId());
		$xtpl->assign("inputName", $this->getInputName());
		$xtpl->assign("inputCss", $this->getInputCss());
		$xtpl->assign("inputSize", $this->getInputSize());
		$xtpl->assign("inputValue", $this->getInputValue());
		$xtpl->assign("parent", $this->getParent());
		
		$disabled = $this->getDisabled();
		if( $disabled )
			$xtpl->assign('disabled',  'disabled' );
		else
			$xtpl->assign('disabled', '');
		
		if(!empty($this->requiredMessage)){
			
			$xtpl->assign("msg_required", $this->requiredMessage);
			$xtpl->parse("main.autocomplete.required");
		}
		
		$xtpl->assign("autocompleteClazz", get_class($this) );

		if(!empty($this->functionCallback))
			$xtpl->assign("functionCallback", ", " . $this->getFunctionCallback() );
		
	}

	protected function renderInputLabel( $xtpl ){

		
		$label = $this->getInputLabel();
		if(!empty($label)){
			$xtpl->assign("label", $label);
			$xtpl->assign("labelCss", $this->getInputLabelCss());
			$xtpl->parse("main.autocomplete.label");	
		}
	}
	
	public function getItems( $text, $parent=null ){

		$text = urldecode($text);
		
		//CdtUtils::log("getItems $text ", __CLASS__, LoggerLevel::getLevelDebug());
		
		if( strlen( $text ) > 2){
		
			$entities = $this->getEntities( $text, $parent );	
		
			return $this->formatEntities( $entities, $text );
			
		}
		
		
	}

	
	
	
	public function formatEntities( ItemCollection $entities, $text ){
		
		/* TODO hacerlo con xtemplate */
		
		$content =  '<ul>'."\n";
		//$content =  '<table>'."\n";
		$even = true;
		$index=0;

		foreach ($entities as $entity) {

			$label = $this->getEntityLabel( $entity );
			$code = $this->getEntityCode( $entity );

			$p =  $label ;
			$p = preg_replace('/(' . $text . ')/i', '<span style="font-weight:bold;font-style:italic;">$1</span>', $p);

			//armamos el atributo "rel" con los atributos adicionales (separados por _ )
			$rel = $this->getRel( $entity );

			//armamos lo que se muestra de la entity  en el dropdown.
			$label_list = $this->getItemDropDown( $entity );

			$p .= "<br /> $label_list"  ;

			$li_style = ($even)? 'autocomplete_li_even': 'autocomplete_li_odd';

			//$content .= $p; 
			$content .= "\t".'<li class="' .  $li_style  .  '" id="autocomplete_'. $code .'" label="' .  $label .  '" rel="'. $rel .'">'. ( $p ) .'</li>'."\n";

			$even = !$even;
			
			$index++;
			
			if($index>10)
				break;
		}
		
		if( !$entities->isEmpty() )
		
			$content .= '</ul>';
		else 
			$content = $this->getEmptyResultContent();	
		
		
		
		//$content .=  '</table>';
		return $content;
	}

	protected function getItemValues( $entity ){
		//si no hay nada mostramos código+label
		if( count($this->propertiesList) == 0){
			$this->propertiesList[] = $this->getFieldCode($entity);
			$this->propertiesList[] = $this->getFieldSearch($entity);
		}
		
		$propertiesValues= array();
		foreach ($this->propertiesList as $property) {
			
			$propertiesValues[] = CdtReflectionUtils::doGetter($entity, $property); 
		}
		return $propertiesValues;
				
	}
	
	protected function getItemDropDown( $entity ){

		$dropdownItem = "<div id='autocomplete_item_desc'><table><tr>";
		
		$propertiesValues= $this->getItemValues($entity);
		foreach ($propertiesValues as $value) {
			
			$dropdownItem .= "<td>$value</td>";
		}
		
		$dropdownItem .= "</tr></table></div>";
		return $dropdownItem;
		//return implode("-", $propertiesValues );	
	}

	protected function getRel( $entity ){
		
		//si no se definió nada, agregamos código
		if( count($this->propertiesCallback) == 0){
			$this->propertiesCallback[] = $this->getFieldCode($entity);
		}
		
		//armamos el atributo "rel" con los atributos para el callback(separados por _*_ )
		$propertiesValues= array();
		foreach ($this->propertiesCallback as $property) {
			
			$propertiesValues[] = CdtReflectionUtils::doGetter($entity, $property); 
		}
		
		$rel = implode("_*_", $propertiesValues);
		
		return $rel;
	}

	protected function getCriteria($text,$parent=null){
		$criterio = new CdtSearchCriteria();
		$criterio->addFilter( $this->getFieldSearch() , $text, "like", new CdtCriteriaFormatLikeValue());
		
		$fieldParent = $this->getFieldSearchParent();
		if( !empty($parent) && !empty($fieldParent))
			$criterio->addFilter( $fieldParent , $parent, "=");
		
		return $criterio;
	}


	public function getXTemplate(){

		$xtpl = new XTemplate( CDT_CMP_TEMPLATE_ENTITYAUTOCOMPLETE );

		$xtpl->assign('WEB_PATH', WEB_PATH);


		return $xtpl;

	}


	public function getPropertiesList()
	{
	    return $this->propertiesList;
	}

	public function setPropertiesList($propertiesList)
	{
	    $this->propertiesList = $propertiesList;
	}

	public function getPropertiesCallback()
	{
	    return $this->propertiesCallback;
	}

	public function setPropertiesCallback($propertiesCallback)
	{
	    $this->propertiesCallback = $propertiesCallback;
	}

	public function getInputId()
	{
	    return $this->inputId;
	}

	public function setInputId($inputId)
	{
	    $this->inputId = $inputId;
	}

	public function getInputName()
	{
	    return $this->inputName;
	}

	public function setInputName($inputName)
	{
	    $this->inputName = $inputName;
	}

	public function getInputSize()
	{
	    return $this->inputSize;
	}

	public function setInputSize($inputSize)
	{
	    $this->inputSize = $inputSize;
	}

	public function getInputCss()
	{
	    return $this->inputCss;
	}

	public function setInputCss($inputCss)
	{
	    $this->inputCss = $inputCss;
	}

	public function getInputValue()
	{
	    return $this->inputValue;
	}

	public function setInputValue($inputValue)
	{
	    $this->inputValue = $inputValue;
	}

	public function getRequiredMessage()
	{
	    return $this->requiredMessage;
	}

	public function setRequiredMessage($requiredMessage)
	{
	    $this->requiredMessage = $requiredMessage;
	}

	public function getInputLabel()
	{
	    return $this->inputLabel;
	}

	public function setInputLabel($inputLabel)
	{
	    $this->inputLabel = $inputLabel;
	}

	public function getInputLabelCss()
	{
	    return $this->inputLabelCss;
	}

	public function setInputLabelCss($inputLabelCss)
	{
	    $this->inputLabelCss = $inputLabelCss;
	}

	public function getFunctionCallback()
	{
	    return $this->functionCallback;
	}

	public function setFunctionCallback($functionCallback)
	{
	    $this->functionCallback = $functionCallback;
	}
	
	protected function getEmptyResultContent(){
		
		return "";
		
	}
	
	

	public function getParent()
	{
	    return $this->parent;
	}

	public function setParent($parent)
	{
	    $this->parent = $parent;
	}

	public function getDisabled()
	{
	    return $this->disabled;
	}

	public function setDisabled($disabled)
	{
	    $this->disabled = $disabled;
	}
}
?>