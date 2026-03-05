<?php

/**
 * DAO para PosgradoMaximo
 *  
 * @author Marcos
 * @since 22-05-2014
 */
class PosgradoMaximoDAO extends EntityDAO {

	public function getTableName(){
		return CYT_TABLE_POSGRADOMAXIMO;
	}
	
	public function getEntityFactory(){
		return new PosgradoMaximoFactory();
	}
	
	public function getFieldsToAdd($entity){
		
		
	}
	
	
	
	public function getIdFieldName(){
		return "cd_posgradomaximo";
	}
	
	public function getFromToSelect(){
		$tPosgradoMaximo = $this->getTableName();
		$tPosgradoPlanilla = DAOFactory::getPosgradoPlanillaDAO()->getTableName();
		$tPuntajeGrupo = DAOFactory::getPuntajeGrupoDAO()->getTableName();
		
		$sql  = parent::getFromToSelect();
		
		$sql .= " LEFT JOIN " . $tPosgradoPlanilla . " ON($tPosgradoMaximo.cd_posgradoplanilla = $tPosgradoPlanilla.cd_posgradoplanilla)";
		$sql .= " LEFT JOIN " . $tPuntajeGrupo . " ON($tPosgradoMaximo.cd_puntajegrupo = $tPuntajeGrupo.cd_puntajegrupo)";
		
		 return $sql;
	}
	
	public function getFieldsToSelect(){
		
		$fields = parent::getFieldsToSelect();
		
		$tPosgradoPlanilla = DAOFactory::getPosgradoPlanillaDAO()->getTableName();
        $fields[] = "$tPosgradoPlanilla.cd_posgradoplanilla as " . $tPosgradoPlanilla . "_oid ";
        $fields[] = "$tPosgradoPlanilla.ds_posgradoplanilla as " . $tPosgradoPlanilla . "_ds_posgradoplanilla ";
        
        $tPuntajeGrupo = DAOFactory::getPuntajeGrupoDAO()->getTableName();
        $fields[] = "$tPuntajeGrupo.cd_puntajegrupo as " . $tPuntajeGrupo . "_oid ";
        $fields[] = "$tPuntajeGrupo.ds_puntajegrupo as " . $tPuntajeGrupo . "_ds_puntajegrupo ";
        $fields[] = "$tPuntajeGrupo.nu_max as " . $tPuntajeGrupo . "_nu_max ";
        
         return $fields;
	}
	

	
	
}
?>