<?php

/**
 * DAO para SubanteriorPlanilla
 *  
 * @author Marcos
 * @since 31-08-2017
 */
class SubanteriorPlanillaDAO extends EntityDAO {

	public function getTableName(){
		return CYT_TABLE_SUBANTERIOR_PLANILLA;
	}
	
	public function getEntityFactory(){
		return new SubanteriorPlanillaFactory();
	}
	
	public function getFieldsToAdd($entity){
		
		
	}
	
	
	
	public function getIdFieldName(){
		return "cd_subanteriorplanilla";
	}
	
	
	
	public function getFromToSelect(){
		$tSubanteriorPlanilla = $this->getTableName();
		$tSubGrupo = DAOFactory::getSubGrupoDAO()->getTableName();
		
		$sql  = parent::getFromToSelect();
		
		$sql .= " LEFT JOIN " . $tSubGrupo . " ON($tSubanteriorPlanilla.cd_subgrupo = $tSubGrupo.cd_subgrupo)";
		
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