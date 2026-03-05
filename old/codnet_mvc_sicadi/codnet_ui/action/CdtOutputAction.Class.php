<?php 

/**
 * Acci�n para inicializar la vista.
 * Las acciones con salida a pantalla extender�n
 * esta clase.
 * Cada subclase implementar� m�todos para devolver el contenido
 * a mostrar, el t�tulo y el layout a utilizar.
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 07-04-2010
 * 
 */
abstract class CdtOutputAction extends CdtAction{

	//instancia del layout asociado al action.
	protected $layout;
	
	/**
	 * @return instancia del layout
	 */
	public function getLayoutInstance(){
		return $this->layout;
	}
		
	/**
	 * (non-PHPdoc)
	 * @see CdtAction::execute();
	 */
	public function execute(){
		
		//armamos el layout.
		$this->layout = $this->getLayout();
		
		try{
			$this->layout->setContent( $this->getOutputContent() );
			$this->layout->setTitle( $this->getOutputTitle() );
					
		}catch(GenericException $ex){
			//capturamos la excepci�n y la parseamos en el layout.
			$this->layout->setException( $ex );
		}
		
		//mostramos la salida formada por el layout.
		echo $this->layout->show();
		
		//no hay forward.
		$forward = null;
				
		return $forward;
	}
	
	/**
	 * layout a utilizar para la salida.
	 * @return Layout
	 */
	protected function getLayout(){
		//el layuout ser� definido en la constante DEFAULT_LAYOUT
		
		//instanciamos el layout por reflection.
		$oLayout = CdtReflectionUtils::newInstance(DEFAULT_LAYOUT);
		
		$l = CdtUtils::getParam("layout") ;
		if( !empty($l) )
			$oLayout = CdtReflectionUtils::newInstance($l);
		
		return $oLayout;
	}
	
	/**
	 * contenido a mostrar.
	 * Cada action concreto definir� el espec�fico.
	 * @return output
	 */
	protected abstract function getOutputContent();
	
	/**
	 * t�tulo del output.
	 * @return title
	 */
	protected abstract function getOutputTitle();
	
	
}