<?php

/**
 * componente grilla.
 * caracter�sticas: 
 *  - acciones individuales o m�ltiples sobre los items.
 *  - filtros de b�squeda.
 *  - ajax para buscar, paginar y ordenar. 
 *
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 14-12-2011
 *
 */
class CMPGrid extends CMPComponent{

	private $id;
	private $oGridModel;
	private $oGridRenderer;
	private $oCriteria;
	
	public function __construct( IGridModel $oGridModel=null, GridRenderer $oGridRenderer=null, $id=1){
		
		$this->setGridModel( $oGridModel );
		$this->setGridRenderer( $oGridRenderer );
		$this->setId( $id );
				
	}
	
	public function show( ){
		//renderizamos el resultado.
		return $this->getGridRenderer()->render( $this );
	}
	
	public function getTitle()
	{
	    return $this->oGridModel->getTitle();
	}
	
	public function getGridModel()
	{
	    return $this->oGridModel;
	}

	public function setGridModel($oGridModel){
		
		if( $oGridModel!=null ){
			
			$gridModelClazz = get_class( $oGridModel );		
			
			//agregamos acciones para exportar a pdf y xls.
			
			$oGridModel->buildExportPdfAction( $this->getId() );
			
			$oGridModel->buildExportXlsAction( $this->getId() );
			
		}
	    $this->oGridModel = $oGridModel;
	}

	public function getGridRenderer()
	{
	    return $this->oGridRenderer;
	}

	public function setGridRenderer($oGridRenderer)
	{
	    $this->oGridRenderer = $oGridRenderer;
	}
	

	public function getId()
	{
	    return $this->id;
	}

	public function setId($id)
	{
	    $this->id = $id;
	}
	
	public function getCriteria()
	{
	    return $this->oCriteria;
	}

	public function setCriteria($oCriteria)
	{
	    $this->oCriteria = $oCriteria;
	}
}