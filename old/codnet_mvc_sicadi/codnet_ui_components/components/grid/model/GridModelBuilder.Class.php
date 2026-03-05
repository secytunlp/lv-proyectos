<?php

/**
 * colabora en la creación de columnos del grid
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 04-03-2013
 *
 */
class GridModelBuilder{


	public static function buildColumn( $name, $label, $width, $textAlign=CDT_CMP_GRID_TEXTALIGN_LEFT, $sqlField="", $format=null){
		
		$oColumn = new GridColumnModel();
		$oColumn->setDs_field( $name );
		$oColumn->setDs_sqlField( $sqlField );
		$oColumn->setDs_name( $name );
		$oColumn->setDs_label( $label );
		$oColumn->setBl_visible( true );
		$oColumn->setNu_width( $width );

		if( empty( $format ))
		$format = new GridValueFormat();
			
		$oColumn->setFormat( $format );
		$oColumn->setTextAlign($textAlign);
		
		return $oColumn;
	}
	
	public static function buildFilterModelFromColumn( GridColumnModel $columnModel, $type="", $bl_hidden=false, $ds_value="", $ds_operator="LIKE", $oCriteriaFormatValue = null){

		return self::buildFilterModel($columnModel->getDs_name(), $columnModel->getDs_label(), $columnModel->getDs_sqlField(), $columnModel->getFormat(), $type, $bl_hidden, $ds_value, $ds_operator, $oCriteriaFormatValue);
	}
	
	public static function buildFilterModel( $name, $label, $sqlField="", $format=null, $type="", $bl_hidden=false, $ds_value="", $ds_operator="LIKE", $oCriteriaFormatValue = null){

		$oFilter = new GridFilterModel();
		$oFilter->setDs_field( $name );
		$oFilter->setDs_sqlField( $sqlField );
		$oFilter->setDs_name( $name );
		$oFilter->setDs_id( $name );
		$oFilter->setDs_label( $label );
		$oFilter->setBl_hidden( $bl_hidden );
		$oFilter->setDs_value( $ds_value );

		if(empty($type))
		$type= CDT_CMP_GRID_FILTER_TYPE_STRING ;
		$oFilter->setType( $type );
			
		if(empty($ds_operator))
		$ds_operator="LIKE";
		$oFilter->setDs_operator( $ds_operator );

		if( $oCriteriaFormatValue == null )
		$oCriteriaFormatValue= new CdtCriteriaFormatLikeValue();
		$oFilter->setCriteriaFormatValue( $oCriteriaFormatValue );


		if( empty( $format ))
		$format = new GridValueFormat();
			
		$oFilter->setFormat( $format );

		return $oFilter;
	}
	
}