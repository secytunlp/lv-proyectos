<?php

/**
 * Representa filtro para la grilla 
 *
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 14-12-2011
 *
 */
class GridFilterModel{

	private $ds_id;	
	private $ds_name;	
	private $ds_label;
	private $ds_field;
	private $ds_sqlField;
	private $oFormat;
	private $bl_hidden;
	private $ds_value;//valor para cuando es hidden.
	//operador para el filtro ("=", "LIKE", ">", etc );
	private $ds_operator;
	private $type;//tipo de filtro (ver constantes CDT_CMP_GRID_FILTER_TYPE_... )
	//formato para el criteria.
	private $oCriteriaFormatValue;
	
	//estas variables las vamos a utilizar para las opciones del filtro.
	//podemos setearle items para que muestre un combo con las opciones para filtrar,
	//o bien, podemos indicar el nombre del manager y el m�todo a invocar para obtener los items.
	//cada item deber� ser un arreglo asociativo ("label", "value").
	private $items;
	private $managerClazz;
	private $methodName;
	
	public function __construct(){
		$this->oFormat = new GridValueFormat();
		$this->ds_operator = "=";
		$this->oCriteriaFormatValue = new CdtCriteriaFormatLikeValue();
		$this->bl_hidden = false;
		$this->type = CDT_CMP_GRID_FILTER_TYPE_STRING;
	}
	
	public function hasOptions(){
		return !empty($this->items) || ( $this->managerClazz!=null && $this->methodName!=null ); 
	}
	
	public function getOptions(){
	
		$options = array();
		
		if(!empty($this->items))	
			$options = $this->items;
			
		elseif( $this->managerClazz!=null && $this->methodName!=null){
		
			$oInstance = CdtReflectionUtils::newInstance( $this->managerClazz );

			$options = CdtReflectionUtils::invoke( $oInstance, $this->methodName );
		}

		return $options;
	}
	
	public function addToCriteria( CMPGrid $oGrid, CdtSearchCriteria $oCriteria ){

		$gridId = $oGrid->getId();
		
		$field = $this->getDs_sqlField();
         if (empty($field))
         	$field = $this->getDs_field();

		//el get convierte "." por "_" as� que lo convertimos.
		$value = CdtUtils::getParam(str_replace(".", "_", $field) . "_" . $gridId, "","",false);
		
		if (!empty($value)) {
		
			$oCriteria->addFilter($field, $value, $this->ds_operator, $this->oCriteriaFormatValue );
								
		}
	}
	
	public function getDs_name()
	{
	    return $this->ds_name;
	}

	public function setDs_name($ds_name)
	{
	    $this->ds_name = $ds_name;
	}

	public function getDs_label()
	{
	    return $this->ds_label;
	}

	public function setDs_label($ds_label)
	{
	    $this->ds_label = $ds_label;
	}

	public function getDs_field()
	{
	    return $this->ds_field;
	}

	public function setDs_field($ds_field)
	{
	    $this->ds_field = $ds_field;
	}

	public function getFormat()
	{
	    return $this->oFormat;
	}

	public function setFormat($oFormat)
	{
	    $this->oFormat = $oFormat;
	}
		

	public function getDs_sqlField()
	{
	    return $this->ds_sqlField;
	}

	public function setDs_sqlField($ds_sqlField)
	{
	    $this->ds_sqlField = $ds_sqlField;
	}


	public function getBl_hidden()
	{
	    return $this->bl_hidden;
	}

	public function setBl_hidden($bl_hidden)
	{
	    $this->bl_hidden = $bl_hidden;
	}

	public function getItems()
	{
	    return $this->items;
	}

	public function setItems($items)
	{
	    $this->items = $items;
	}

	public function getManagerClazz()
	{
	    return $this->managerClazz;
	}

	public function setManagerClazz($managerClazz)
	{
	    $this->managerClazz = $managerClazz;
	}

	public function getMethodName()
	{
	    return $this->methodName;
	}

	public function setMethodName($methodName)
	{
	    $this->methodName = $methodName;
	}

	public function getDs_operator()
	{
	    return $this->ds_operator;
	}

	public function setDs_operator($ds_operator)
	{
	    $this->ds_operator = $ds_operator;
	}

	public function getCriteriaFormatValue()
	{
	    return $this->oCriteriaFormatValue;
	}

	public function setCriteriaFormatValue($oCriteriaFormatValue)
	{
	    $this->oCriteriaFormatValue = $oCriteriaFormatValue;
	}

	public function getDs_value()
	{
	    return $this->ds_value;
	}

	public function setDs_value($ds_value)
	{
	    $this->ds_value = $ds_value;
	}

	public function getType()
	{
	    return $this->type;
	}

	public function setType($type)
	{
	    $this->type = $type;
	}

	public function getDs_id()
	{
	    return $this->ds_id;
	}

	public function setDs_id($ds_id)
	{
	    $this->ds_id = $ds_id;
	}
}