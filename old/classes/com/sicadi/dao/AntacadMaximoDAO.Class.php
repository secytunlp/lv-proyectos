<?php

/**
 * DAO para AntacadMaximo
 *  
 * @author Marcos
 * @since 23-05-2014
 */
class AntacadMaximoDAO extends EntityDAO {

	public function getTableName(){
		return CYT_TABLE_ANTACAD_MAXIMO;
	}
	
	public function getEntityFactory(){
		return new AntacadMaximoFactory();
	}
	
	public function getFieldsToAdd($entity){
		
		
	}
	
	
	
	public function getIdFieldName(){
		return "cd_antacadmaximo";
	}
	
	public function getFromToSelect(){
		$tAntacadMaximo = $this->getTableName();
		$tAntacadPlanilla = DAOFactory::getAntacadPlanillaDAO()->getTableName();
		$tPuntajeGrupo = DAOFactory::getPuntajeGrupoDAO()->getTableName();
		
		$sql  = parent::getFromToSelect();
		
		$sql .= " LEFT JOIN " . $tAntacadPlanilla . " ON($tAntacadMaximo.cd_antacadplanilla = $tAntacadPlanilla.cd_antacadplanilla)";
		$sql .= " LEFT JOIN " . $tPuntajeGrupo . " ON($tAntacadMaximo.cd_puntajegrupo = $tPuntajeGrupo.cd_puntajegrupo)";
		
		 return $sql;
	}
	
	public function getFieldsToSelect(){
		
		$fields = parent::getFieldsToSelect();
		
		$tAntacadPlanilla = DAOFactory::getAntacadPlanillaDAO()->getTableName();
        $fields[] = "$tAntacadPlanilla.cd_antacadplanilla as " . $tAntacadPlanilla . "_oid ";
        $fields[] = "$tAntacadPlanilla.ds_antacadplanilla as " . $tAntacadPlanilla . "_ds_antacadplanilla ";
        
        $tPuntajeGrupo = DAOFactory::getPuntajeGrupoDAO()->getTableName();
        $fields[] = "$tPuntajeGrupo.cd_puntajegrupo as " . $tPuntajeGrupo . "_oid ";
        $fields[] = "$tPuntajeGrupo.ds_puntajegrupo as " . $tPuntajeGrupo . "_ds_puntajegrupo ";
        $fields[] = "$tPuntajeGrupo.nu_max as " . $tPuntajeGrupo . "_nu_max ";
        
         return $fields;
	}
	

	
	
}
?>