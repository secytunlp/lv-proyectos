<?php
/**
 * acci�n para los request del componente grilla
 * 
 * para cada grilla espec�fica tenemos que crear una subclase
 * que define el entitymanager y el columnmodel.
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 15-12-2011
 *
 */
class CMPGridAction extends CdtOutputAction{

	protected $oGrid;
		
	protected function buildGrid(){
		$oGrid = $this->getNewGrid();
		
		//obtenemos el modelo de la grilla y el renderer
		$oGridModel = $this->getGridModel( $oGrid );
		$oGridRenderer = $this->getGridRenderer( $oGrid );
		
		//creamos el criteria y obtenemos las entities.
		$oCriteria = $this->getCdtSearchCriteria( $oGrid, $oGridModel );
		$oManager = $oGridModel->getEntityManager();
		$entities = $oManager->getEntities ( $oCriteria );
		$totalRows = $oManager->getEntitiesCount (  $oCriteria );
		
		//les seteamos el resultado al modelo.
		$oGridModel->setEntities( $entities );
		$oGridModel->setTotalRows( $totalRows );

		$oGrid->setGridModel( $oGridModel );
		$oGrid->setCriteria( $oCriteria );
		$oGrid->setGridRenderer( $oGridRenderer );

		$this->oGrid = $oGrid;
		return $oGrid;
	}
	
	protected function getOutputContent(){

		try{
			
			$oGrid = $this->buildGrid();
			//$oGrid->show();
			return $oGrid->show();
			
		}catch(GenericException $ex){
			
			 CdtUtils::log_error ( "ERROR =>" . $ex->getMessage() );
			
		}
		return "";
	}

	protected function getNewGrid(){
		$grid = new CMPGrid();
		$grid->setId( $this->getIdGrid() ) ;
		return $grid;	
	}

	
	public function getOutputTitle(){
		if( $this->oGrid != null )
			return $this->oGrid->getTitle();
		else "error";
	}

	protected function getLayout(){
		/*
		$layoutClazz = CdtUtils::getParam("layout") ;
		if(!empty($layouClazz)){
			
			$oLayout = CdtReflectionUtils::newInstance( $layoutClazz );
			
		}else{
			
			if( CdtUtils::getParam("search"))
				$oLayout = new CdtLayoutBasicAjax();
			else
				$oLayout = parent::getLayout();
						
		}*/
		
		if( CdtUtils::getParam("search"))
			$oLayout = new CdtLayoutBasicAjax();
			
		elseif( defined("CMP_GRID_DEFAULT_LAYOUT") )
				$oLayout = CdtReflectionUtils::newInstance(CMP_GRID_DEFAULT_LAYOUT);
		else	
			$oLayout = parent::getLayout();
				
		return $oLayout;
	}	
		
	protected function getGridModel( CMPGrid $oGrid ){
		$gridModelClazz  =  CdtUtils::getParam("model_" . $oGrid->getId()) ;
		return CdtReflectionUtils::newInstance( $gridModelClazz );
	}
	
	protected function getIdGrid(){
		$gridId  =  CdtUtils::getParam("gridId") ;
		if( empty($gridId) )
			$gridId = get_class($this);
		return $gridId;
	}
	
	
	protected function getGridRenderer( $oGrid ){
		
		$rendererClazz  =  CdtUtils::getParam("renderer_" . $oGrid->getId()) ;
		
		if(!empty( $rendererClazz)){
			
			$renderer = CdtReflectionUtils::newInstance( $rendererClazz );
			
		}else{
			
			$renderer = CdtReflectionUtils::newInstance( CDT_CMP_DEFAULT_GRID_RENDERER );
		}
		
		return $renderer;
		
	}
	
	protected function getCdtSearchCriteria( CMPGrid $oGrid, GridModel $oGridModel ){
	
        $gridId = $oGrid->getId();

        //recuperamos los par�metros.
        $filterValue = CdtUtils::getParam("filterValue_$gridId", "", false, false);
        $page = CdtUtils::getParam("page_$gridId", 1);
        $orderType = CdtUtils::getParam("orderType_$gridId", $oGridModel->getDefaultOrderType());
        $orderField = CdtUtils::getParam("orderField_$gridId", $oGridModel->getDefaultOrderField());
        $filterField = CdtUtils::getParam("filterField_$gridId", "");

        //armamos el criteria
        $oCriteria = new CdtSearchCriteria();

        if (empty($filterField))
        	$filterField = $oGridModel->getDefaultFilterField();
        	
		//agregamos cada filter que tenga valor.
        for ($index = 0; $index < $oGridModel->getFiltersCount(); $index++) {

            $oFilterModel = $oGridModel->getFilterModel($index);

            $name = $oFilterModel->getDs_name();
            $field = $oFilterModel->getDs_sqlField();
            if (empty($field))
                $field = $oFilterModel->getDs_field();

        	//$value = utf8_decode($oFilterModel->getDs_value());
			$value = mb_convert_encoding($oFilterModel->getDs_value(), 'ISO-8859-1', 'UTF-8');
            if(empty($value)){
               	//el get convierte "." por "_" as� que lo convertimos.
            	//$value = utf8_decode(CdtUtils::getParam(str_replace(".", "_", $name) . "_" . $gridId, "","",false));
				$value = mb_convert_encoding(CdtUtils::getParam(str_replace(".", "_", $name) . "_" . $gridId, "","",false), 'ISO-8859-1', 'UTF-8');
        	}			
            $this->addSelectedFilter($oCriteria, $field, $value, $oFilterModel);
            
            
            //agregamos el filtro por default.
            if( ($name == $filterField) ){
            	//$inputFilter = new InputFilter();
            	//$filterValue =  $inputFilter->decode( $filterValue );
            	//$filterValue =  utf8_decode( $filterValue );
				$filterValue = mb_convert_encoding($filterValue, 'ISO-8859-1', 'UTF-8');
            	CdtUtils::log("seteando filter default $field = $filterValue", __CLASS__, LoggerLevel::getLevelDebug());
	        	$this->addSelectedFilter($oCriteria, $field, $filterValue, $oFilterModel);            	
            }
            
            
        }
        
        //pueden venir varios en el orderField.
		$orderFields = explode("," , $orderField );
        for( $index=0; $index<count($orderFields); $index++) {
			$oCriteria->addOrder($orderFields[$index], $orderType); 
		}
        $oCriteria->setPage($page);
        $oCriteria->setRowPerPage(ROW_PER_PAGE);
		
        
        $oGridModel->enhanceCriteria( $oCriteria );
        
        return $oCriteria;
    }

	/**
     * se agrega el filtro seleccionado
     * @param CdtSearchCriteria $oCriteria criterio de b�squeda
     * @param string $filterField campo por el cual filtrar
     * @param string $filterValue valor a filtrar
     */
    protected function addSelectedFilter(CdtSearchCriteria $oCriteria, $filterField, $filterValue, GridFilterModel $oFilterModel) {
        
    	CdtUtils::log("addSelectedFilter filterField $filterField, filterValue $filterValue", __CLASS__, LoggerLevel::getLevelDebug());
		if( $filterValue == "yes"){
			
			$ds_operator = $oFilterModel->getDs_operator();
			$oCriteriaFormat = $oFilterModel->getCriteriaFormatValue();
        	$oCriteria->addFilter($filterField, 1, $ds_operator , $oCriteriaFormat );
        	
		}elseif( $filterValue == "no"){
			
			$ds_operator = $oFilterModel->getDs_operator();
			$oCriteriaFormat = $oFilterModel->getCriteriaFormatValue();
        	$oCriteria->addFilter($filterField, 0, $ds_operator , $oCriteriaFormat );
        	
		}elseif( $filterValue==-1){
			
			return;
			
		}elseif (!empty($filterValue) ) {
        	
        	//$filterValue = $oFilterModel->getFormat()->format( $filterValue );
        	$ds_operator = $oFilterModel->getDs_operator();
        	$oCriteriaFormat = $oFilterModel->getCriteriaFormatValue();
        	$oCriteria->addFilter($filterField, $filterValue, $ds_operator , $oCriteriaFormat );

       }
    }
	
	
}
?>