<?php

/**
 * DAO para AntproduccionMaximo
 *  
 * @author Marcos
 * @since 28-05-2014
 */
class AntproduccionMaximoDAO extends EntityDAO {

	public function getTableName(){
		return CYT_TABLE_ANTPRODUCCION_MAXIMO;
	}
	
	public function getEntityFactory(){
		return new AntproduccionMaximoFactory();
	}
	
	public function getFieldsToAdd($entity){
		
		
	}
	
	
	
	public function getIdFieldName(){
		return "cd_antproduccionmaximo";
	}
	
	public function getFromToSelect(){
		$tAntproduccionMaximo = $this->getTableName();
		$tAntproduccionPlanilla = DAOFactory::getAntproduccionPlanillaDAO()->getTableName();
		$tSubGrupo = DAOFactory::getSubGrupoDAO()->getTableName();
		$tPuntajeGrupo = DAOFactory::getPuntajeGrupoDAO()->getTableName();
		
		$sql  = parent::getFromToSelect();
		
		$sql .= " LEFT JOIN " . $tAntproduccionPlanilla . " ON($tAntproduccionMaximo.cd_antproduccionplanilla = $tAntproduccionPlanilla.cd_antproduccionplanilla)";
		$sql .= " LEFT JOIN " . $tSubGrupo . " ON($tAntproduccionPlanilla.cd_subgrupo = $tSubGrupo.cd_subgrupo)";
		$sql .= " LEFT JOIN " . $tPuntajeGrupo . " ON($tAntproduccionMaximo.cd_puntajegrupo = $tPuntajeGrupo.cd_puntajegrupo)";
		
		 return $sql;
	}
	
	public function getFieldsToSelect(){
		
		$fields = parent::getFieldsToSelect();
		
		$tAntproduccionPlanilla = DAOFactory::getAntproduccionPlanillaDAO()->getTableName();
        $fields[] = "$tAntproduccionPlanilla.cd_antproduccionplanilla as " . $tAntproduccionPlanilla . "_oid ";
        $fields[] = "$tAntproduccionPlanilla.ds_antproduccionplanilla as " . $tAntproduccionPlanilla . "_ds_antproduccionplanilla ";
        
        $tPuntajeGrupo = DAOFactory::getPuntajeGrupoDAO()->getTableName();
        $fields[] = "$tPuntajeGrupo.cd_puntajegrupo as " . $tPuntajeGrupo . "_oid ";
        $fields[] = "$tPuntajeGrupo.ds_puntajegrupo as " . $tPuntajeGrupo . "_ds_puntajegrupo ";
        $fields[] = "$tPuntajeGrupo.nu_max as " . $tPuntajeGrupo . "_nu_max ";
        $fields[] = "$tPuntajeGrupo.cd_grupopadre as " . $tPuntajeGrupo . "_cd_grupopadre ";
        
        $tSubGrupo = DAOFactory::getSubGrupoDAO()->getTableName();
        $fields[] = "$tSubGrupo.cd_subgrupo as " . $tSubGrupo . "_oid ";
        $fields[] = "$tSubGrupo.ds_subgrupo as " . $tSubGrupo . "_ds_subgrupo ";
        $fields[] = "$tSubGrupo.ds_pdf as " . $tSubGrupo . "_ds_pdf ";
        
         return $fields;
	}
	

	
	
}
?>