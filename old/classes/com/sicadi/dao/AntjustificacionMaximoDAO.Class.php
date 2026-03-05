<?php

/**
 * DAO para AntjustificacionMaximo
 *  
 * @author Marcos
 * @since 27-05-2014
 */
class AntjustificacionMaximoDAO extends EntityDAO {

	public function getTableName(){
		return CYT_TABLE_ANTJUSTIFICACION_MAXIMO;
	}
	
	public function getEntityFactory(){
		return new AntjustificacionMaximoFactory();
	}
	
	public function getFieldsToAdd($entity){
		
		
	}
	
	
	
	public function getIdFieldName(){
		return "cd_antjustificacionmaximo";
	}
	
	public function getFromToSelect(){
		$tAntjustificacionMaximo = $this->getTableName();
		$tAntjustificacionPlanilla = DAOFactory::getAntjustificacionPlanillaDAO()->getTableName();
		$tSubGrupo = DAOFactory::getSubGrupoDAO()->getTableName();
		$tPuntajeGrupo = DAOFactory::getPuntajeGrupoDAO()->getTableName();
		
		$sql  = parent::getFromToSelect();
		
		$sql .= " LEFT JOIN " . $tAntjustificacionPlanilla . " ON($tAntjustificacionMaximo.cd_antjustificacionplanilla = $tAntjustificacionPlanilla.cd_antjustificacionplanilla)";
		$sql .= " LEFT JOIN " . $tSubGrupo . " ON($tAntjustificacionPlanilla.cd_subgrupo = $tSubGrupo.cd_subgrupo)";
		$sql .= " LEFT JOIN " . $tPuntajeGrupo . " ON($tAntjustificacionMaximo.cd_puntajegrupo = $tPuntajeGrupo.cd_puntajegrupo)";
		
		 return $sql;
	}
	
	public function getFieldsToSelect(){
		
		$fields = parent::getFieldsToSelect();
		
		$tAntjustificacionPlanilla = DAOFactory::getAntjustificacionPlanillaDAO()->getTableName();
        $fields[] = "$tAntjustificacionPlanilla.cd_antjustificacionplanilla as " . $tAntjustificacionPlanilla . "_oid ";
        $fields[] = "$tAntjustificacionPlanilla.ds_antjustificacionplanilla as " . $tAntjustificacionPlanilla . "_ds_antjustificacionplanilla ";
        
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