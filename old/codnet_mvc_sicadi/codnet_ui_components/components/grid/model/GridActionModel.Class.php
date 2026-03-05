<?php

/**
 * Representa una acción sobre la grilla. 
 *
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 14-12-2011
 *
 */
class GridActionModel{

	private $ds_name;	
	private $ds_label;
	private $ds_action;
	private $bl_multiple;
	private $ds_image;	
	private $ds_style;
	private $ds_callback;
	private $ds_confirmationMsg;
	private $bl_popUp;
 	private $nu_heightPopup;
 	private $nu_widthPopup;
 	private $bl_targetblank;
 	private $bl_allPageItems;
 	
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

	public function getDs_action()
	{
	    return $this->ds_action;
	}

	public function setDs_action($ds_action)
	{
	    $this->ds_action = $ds_action;
	}

	public function getDs_image()
	{
	    return $this->ds_image;
	}

	public function setDs_image($ds_image)
	{
	    $this->ds_image = $ds_image;
	}

	public function getBl_multiple()
	{
	    return $this->bl_multiple;
	}

	public function setBl_multiple($bl_multiple)
	{
	    $this->bl_multiple = $bl_multiple;
	}

	public function getDs_style()
	{
	    return $this->ds_style;
	}

	public function setDs_style($ds_style)
	{
	    $this->ds_style = $ds_style;
	}

	public function getDs_callback()
	{
	    return $this->ds_callback;
	}

	public function setDs_callback($ds_callback)
	{
	    $this->ds_callback = $ds_callback;
	}

	public function getDs_confirmationMsg()
	{
	    return $this->ds_confirmationMsg;
	}

	public function setDs_confirmationMsg($ds_confirmationMsg)
	{
	    $this->ds_confirmationMsg = $ds_confirmationMsg;
	}



	public function getBl_popUp()
	{
	    return $this->bl_popUp;
	}

	public function setBl_popUp($bl_popUp)
	{
	    $this->bl_popUp = $bl_popUp;
	}

	public function getNu_heightPopup()
	{
	    return $this->nu_heightPopup;
	}

	public function setNu_heightPopup($nu_heightPopup)
	{
	    $this->nu_heightPopup = $nu_heightPopup;
	}

	public function getNu_widthPopup()
	{
	    return $this->nu_widthPopup;
	}

	public function setNu_widthPopup($nu_widthPopup)
	{
	    $this->nu_widthPopup = $nu_widthPopup;
	}



	

	public function getBl_targetblank()
	{
	    return $this->bl_targetblank;
	}

	public function setBl_targetblank($bl_targetblank)
	{
	    $this->bl_targetblank = $bl_targetblank;
	}

	public function getBl_allPageItems()
	{
	    return $this->bl_allPageItems;
	}

	public function setBl_allPageItems($bl_allPageItems)
	{
	    $this->bl_allPageItems = $bl_allPageItems;
	}
}