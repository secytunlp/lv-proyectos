<?php

/**
 * Representa una columna en la grilla 
 *
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 14-12-2011
 *
 */

class GridColumnModel{

	private $ds_name;	
	private $ds_label;
	private $nu_width;
	private $bl_visible;
	private $ds_field;
	private $ds_sqlField;
	private $oFormat;
	private $ds_cssClass;
	private $ds_cssStyle;

	
	private $textAlign;
	
	private $ds_group;

	public function __construct(){
		$this->setFormat( new GridValueFormat() );
		$this->setTextAlign( CDT_CMP_GRID_TEXTALIGN_CENTER );
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

	public function getNu_width()
	{
	    return $this->nu_width;
	}

	public function setNu_width($nu_width)
	{
	    $this->nu_width = $nu_width;
	}

	public function getBl_visible()
	{
	    return $this->bl_visible;
	}

	public function setBl_visible($bl_visible)
	{
	    $this->bl_visible = $bl_visible;
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

	public function getDs_group()
	{
	    return $this->ds_group;
	}

	public function setDs_group($ds_group)
	{
	    $this->ds_group = $ds_group;
	}
	
	public function hasGroup(){
		$group = $this->getDs_group();
		return !empty( $group );
	}

	public function getDs_cssClass()
	{
	    return $this->ds_cssClass;
	}

	public function setDs_cssClass($ds_cssClass)
	{
	    $this->ds_cssClass = $ds_cssClass;
	}

	public function getDs_cssStyle()
	{
	    return $this->ds_cssStyle;
	}

	public function setDs_cssStyle($ds_cssStyle)
	{
	    $this->ds_cssStyle = $ds_cssStyle;
	}

	public function getTextAlign()
	{
	    return $this->textAlign;
	}

	public function setTextAlign($textAlign)
	{
	    $this->textAlign = $textAlign;
	}
}