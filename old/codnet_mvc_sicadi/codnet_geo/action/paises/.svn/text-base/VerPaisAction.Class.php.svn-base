<?php 

/**
 * Acción para visualizar un pais.
 *  
 * @author bernardo
 * @since 18-03-2010
 * 
 */
class VerPaisAction extends OutputAction{

	
	public function getXTemplate(){
		return new XTemplate ( CDT_GEO_TEMPLATE_VER_PAIS );
	}
	
	/**
	 * consulta una pais.
	 * @return forward.
	 */
	protected function getContenido(){
			
		$xtpl = $this->getXTemplate();
		
		if (isset ( $_GET ['id'] )) {
			$cd_pais = FormatUtils::getParam('id');
			
			$manager = new PaisManager();
			
			try{
				$oPais = $manager->getPaisPorId ( $cd_pais );
			}catch(GenericException $ex){
				$oPais = new Pais();
				//TODO ver si se muestra un mensaje de error.
			}			

			//se muestra la pais.
			$xtpl->assign ( 'cd_pais', $oPais->getCd_pais());
			$xtpl->assign ( 'ds_pais', stripslashes ( $oPais->getDs_pais()) );
						
		}
		
		$xtpl->assign ( 'titulo', 'Detalle de pais' );
		$xtpl->parse ( 'main' );
		return $xtpl->text ( 'main' );
	}
	
	public function getTitulo(){
		return "Detalle de Pais";
	}
	
	
}