<?php

/**
 * DAO para SubanteriorMaximo
 *  
 * @author Marcos
 * @since 31-08-2017
 */
class SubanteriorMaximoDAO extends EntityDAO {

	public function getTableName(){
		return CYT_TABLE_SUBANTERIOR_MAXIMO;
	}
	
	public function getEntityFactory(){
		return new SubanteriorMaximoFactory();
	}
	
	public function getFieldsToAdd($entity){
		
		
	}
	
	
	
	public function getIdFieldName(){
		return "cd_subanteriormaximo";
	}
	
	public function getFromToSelect(){
		$tSubanteriorMaximo = $this->getTableName();
		$tSubanteriorPlanilla = DAOFactory::getSubanteriorPlanillaDAO()->getTableName();
		$tSubGrupo = DAOFactory::getSubGrupoDAO()->getTableName();
		$tPuntajeGrupo = DAOFactory::getPuntajeGrupoDAO()->getTableName();
		
		$sql  = parent::getFromToSelect();
		
		$sql .= " LEFT JOIN " . $tSubanteriorPlanilla . " ON($tSubanteriorMaximo.cd_subanteriorplanilla = $tSubanteriorPlanilla.cd_subanteriorplanilla)";
		$sql .= " LEFT JOIN " . $tSubGrupo . " ON($tSubanteriorPlanilla.cd_subgrupo = $tSubGrupo.cd_subgrupo)";
		$sql .= " LEFT JOIN " . $tPuntajeGrupo . " ON($tSubanteriorMaximo.cd_puntajegrupo = $tPuntajeGrupo.cd_puntajegrupo)";
		
		 return $sql;
	}
	
	public function getFieldsToSelect(){
		
		$fields = parent::getFieldsToSelect();
		
		$tSubanteriorPlanilla = DAOFactory::getSubanteriorPlanillaDAO()->getTableName();
        $fields[] = "$tSubanteriorPlanilla.cd_subanteriorplanilla as " . $tSubanteriorPlanilla . "_oid ";
        $fields[] = "$tSubanteriorPlanilla.ds_subanteriorplanilla as " . $tSubanteriorPlanilla . "_ds_subanteriorplanilla ";
        
        $tPuntajeGrupo = DAOFactory::getPuntajeGrupoDAO()->getTableName();
        $fields[] = "$tPuntajeGrupo.cd_puntajegrupo as " . $tPuntajeGrupo . "_oid ";
        $fields[] = "$tPuntajeGrupo.ds_puntajegrupo as " . $tPuntajeGrupo . "_ds_puntajegrupo ";
        $fields[] = "$tPuntajeGrupo.nu_max as " . $tPuntajeGrupo . "_nu_max ";
        
        $tSubGrupo = DAOFactory::getSubGrupoDAO()->getTableName();
        $fields[] = "$tSubGrupo.cd_subgrupo as " . $tSubGrupo . "_oid ";
        $fields[] = "$tSubGrupo.ds_subgrupo as " . $tSubGrupo . "_ds_subgrupo ";
        $fields[] = "$tSubGrupo.ds_pdf as " . $tSubGrupo . "_ds_pdf ";
        
         return $fields;
	}
	

	
	
}
?>