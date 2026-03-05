<?php

/**
 * DAO para Alfabetico
 *  
 * @author Marcos
 * @since 09-06-2023
 */
class AlfabeticoDAO extends EntityDAO {

	public function getTableName(){
		return CYT_TABLE_CARGO_ALFABETICO;
	}
	
	public function getEntityFactory(){
		return new AlfabeticoFactory();
	}


    public function getFieldsToAdd($entity){


    }
	
	
	public function getIdFieldName(){
		return "dni";
	}
	
	
public function getFromToSelect(){
		
		$tDocente = $this->getTableName();
		

		$tCargo = CYTSecureDAOFactory::getCargoDAO()->getTableName();
		$tDeddoc = CYTSecureDAOFactory::getDeddocDAO()->getTableName();
		$tFacultad = CYTSecureDAOFactory::getFacultadDAO()->getTableName();

		
		
		
        $sql  = parent::getFromToSelect();
       

        $sql .= " LEFT JOIN " . $tCargo . " ON($tDocente.cd_cargo = $tCargo.cd_cargo)";
        $sql .= " LEFT JOIN " . $tDeddoc . " ON($tDocente.cd_deddoc = $tDeddoc.cd_deddoc)";
        $sql .= " LEFT JOIN " . $tFacultad . " ON($tDocente.cd_facultad = $tFacultad.cd_facultad)";

        
       
        
        
        return $sql;
	}
	
	public function getFieldsToSelect(){
		
		

		$tCargo = CYTSecureDAOFactory::getCargoDAO()->getTableName();
		$tDeddoc = CYTSecureDAOFactory::getDeddocDAO()->getTableName();
		$tFacultad = CYTSecureDAOFactory::getFacultadDAO()->getTableName();

		
		$fields = parent::getFieldsToSelect();
		
        
        

        
        $fields[] = "$tCargo.cd_cargo as " . $tCargo . "_oid ";
        $fields[] = "$tCargo.ds_cargo as " . $tCargo . "_ds_cargo ";
        
        $fields[] = "$tDeddoc.cd_deddoc as " . $tDeddoc . "_oid ";
        $fields[] = "$tDeddoc.ds_deddoc as " . $tDeddoc . "_ds_deddoc ";
        
        $fields[] = "$tFacultad.cd_facultad as " . $tFacultad . "_oid ";
        $fields[] = "$tFacultad.ds_facultad as " . $tFacultad . "_ds_facultad ";
        

        
       
        
        return $fields;
	}
	
	

	
	
}
?>