<?php
/**
 * 
 * Componente para autocomplete solicitudes.
 * 
 * @author Marcos
 * @since 16/11/2013
 */

class CMPSolicitudAutocomplete extends CMPEntityAutocomplete{

	protected function getEntityClazz(){
		return "Solicitud";
	}

	protected function getFieldCode(){
		return "cd_solicitud";
	}

	protected function getFieldSearch(){
		return "ds_apellido,ds_nombre";
	}

	protected function getEntityManager(){
		return ManagerFactory::getSolicitudManager();
	}


	public function __construct(){
		$properties = array();
		$properties[] = "ds_apellido";
		$properties[] = "ds_nombre";
		$this->setPropertiesList($properties);
	}

	protected function getCriteria($text, $parent=null){
		
		$criterio = new CdtSearchCriteria();

		$tDocente = CYTSecureDAOFactory::getDocenteDAO()->getTableName();
		
		
		$filter = new CdtSimpleExpression( "($tDocente.ds_apellido like '$text%' OR $tDocente.ds_nombre like '$text%')");

		$criterio->setExpresion($filter);

		return $criterio;
	}

	protected function getItemDropDown( $entity ){
		$dropdownItem = "<div id='autocomplete_item_desc'><table><tr>";
		$dropdownItem .= "<td>".  $entity->getDocente()->getDs_apellido()  . "</td>";
		$dropdownItem .= "<td>".  $entity->getDocente()->getDs_nombre()  . "</td>";
		$dropdownItem .= "</tr></table></div>";
		return $dropdownItem;
	}


}
?>