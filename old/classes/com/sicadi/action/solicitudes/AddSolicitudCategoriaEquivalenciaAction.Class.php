<?php

/**
 * Se traen las categorias
 * 
 * @author Marcos
 * @since 07-06-2023
 *
 */
class AddSolicitudCategoriaEquivalenciaAction extends CdtAction{


	/**
	 * (non-PHPdoc)
	 * @see CdtAction::execute();
	 */
	public function execute(){

		
		$result = "";
		
		try{
			
			$cd_equivalencia = CdtUtils::getParam("cd_equivalencia");
            $cd_categoria = CdtUtils::getParam("cd_categoria");

            $catssicadi='';
            switch($cd_equivalencia) {
                case '1':
                    $catssicadi=$cd_categoria;
                    break;
                case '2':
                    $catssicadi=6;
                    break;
                case '3':
                    $catssicadi=7;
                    break;
                case '4':
                    $catssicadi=8;
                    break;
                case '5':
                    $catssicadi=8;
                    break;
                case '6':
                    $catssicadi=9;
                    break;
                case '7':
                    $catssicadi=9;
                    break;
                case '8':
                    $catssicadi=10;
                    break;
                case '9':
                    $catssicadi=6;
                    break;
            }
			
			$result = CYTUtils::getCategoriasItems($catssicadi);
			
			
		}catch(Exception $ex){
			
			$result['error'] = $ex->getMessage();
			
		}
		echo json_encode( $result ); 
		return null;
	}
	
	
	
}