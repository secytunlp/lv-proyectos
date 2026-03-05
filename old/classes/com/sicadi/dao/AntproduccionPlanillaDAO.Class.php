<?php

/**
 * DAO para AntproduccionPlanilla
 *  
 * @author Marcos
 * @since 28-05-2014
 */
class AntproduccionPlanillaDAO extends EntityDAO {

	public function getTableName(){
		return CYT_TABLE_ANTPRODUCCION_PLANILLA;
	}
	
	public function getEntityFactory(){
		return new AntproduccionPlanillaFactory();
	}
	
	public function getFieldsToAdd($entity){
		
		
	}
	
	
	
	public function getIdFieldName(){
		return "cd_antproduccionplanilla";
	}
	
	
	
	public function getFromToSelect(){
		$tAntproduccionPlanilla = $this->getTableName();
		$tSubGrupo = DAOFactory::getSubGrupoDAO()->getTableName();
		
		$sql  = parent::getFromToSelect();
		
		$sql .= " LEFT JOIN " . $tSubGrupo . " ON($tAntproduccionPlanilla.cd_subgrupo = $tSubGrupo.cd_subgrupo)";
		
		 return $sql;
	}
	
	public function getFieldsToSelect(){
		
		$fields = parent::getFieldsToSelect();
		
		
        $tSubGrupo = DAOFactory::getSubGrupoDAO()->getTableName();
        $fields[] = "$tSubGrupo.cd_subgrupo as " . $tSubGrupo . "_oid ";
        $fields[] = "$tSubGrupo.ds_subgrupo as " . $tSubGrupo . "_ds_subgrupo ";
        $fields[] = "$tSubGrupo.ds_pdf as " . $tSubGrupo . "_ds_pdf ";
        
         return $fields;
	}
	

	
	
}
?>