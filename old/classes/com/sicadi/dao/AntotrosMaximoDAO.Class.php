<?php

/**
 * DAO para AntotrosMaximo
 *  
 * @author Marcos
 * @since 27-05-2014
 */
class AntotrosMaximoDAO extends EntityDAO {

	public function getTableName(){
		return CYT_TABLE_ANTOTROS_MAXIMO;
	}
	
	public function getEntityFactory(){
		return new AntotrosMaximoFactory();
	}
	
	public function getFieldsToAdd($entity){
		
		
	}
	
	
	
	public function getIdFieldName(){
		return "cd_antotrosmaximo";
	}
	
	public function getFromToSelect(){
		$tAntotrosMaximo = $this->getTableName();
		$tAntotrosPlanilla = DAOFactory::getAntotrosPlanillaDAO()->getTableName();
		$tSubGrupo = DAOFactory::getSubGrupoDAO()->getTableName();
		$tPuntajeGrupo = DAOFactory::getPuntajeGrupoDAO()->getTableName();
		
		
		$sql  = parent::getFromToSelect();
		
		$sql .= " LEFT JOIN " . $tAntotrosPlanilla . " ON($tAntotrosMaximo.cd_antotrosplanilla = $tAntotrosPlanilla.cd_antotrosplanilla)";
		$sql .= " LEFT JOIN " . $tSubGrupo . " ON($tAntotrosPlanilla.cd_subgrupo = $tSubGrupo.cd_subgrupo)";
		$sql .= " LEFT JOIN " . $tPuntajeGrupo . " ON($tAntotrosMaximo.cd_puntajegrupo = $tPuntajeGrupo.cd_puntajegrupo)";
		
		 return $sql;
	}
	
	public function getFieldsToSelect(){
		
		$fields = parent::getFieldsToSelect();
		
		$tAntotrosPlanilla = DAOFactory::getAntotrosPlanillaDAO()->getTableName();
        $fields[] = "$tAntotrosPlanilla.cd_antotrosplanilla as " . $tAntotrosPlanilla . "_oid ";
        $fields[] = "$tAntotrosPlanilla.ds_antotrosplanilla as " . $tAntotrosPlanilla . "_ds_antotrosplanilla ";
        
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