<?php

/**
 * DAO para AntjustificacionPlanilla
 *  
 * @author Marcos
 * @since 27-05-2014
 */
class AntjustificacionPlanillaDAO extends EntityDAO {

	public function getTableName(){
		return CYT_TABLE_ANTJUSTIFICACION_PLANILLA;
	}
	
	public function getEntityFactory(){
		return new AntjustificacionPlanillaFactory();
	}
	
	public function getFieldsToAdd($entity){
		
		
	}
	
	
	
	public function getIdFieldName(){
		return "cd_antjustificacionplanilla";
	}
	
	
	
	public function getFromToSelect(){
		$tAntjustificacionPlanilla = $this->getTableName();
		$tSubGrupo = DAOFactory::getSubGrupoDAO()->getTableName();
		
		$sql  = parent::getFromToSelect();
		
		$sql .= " LEFT JOIN " . $tSubGrupo . " ON($tAntjustificacionPlanilla.cd_subgrupo = $tSubGrupo.cd_subgrupo)";
		
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