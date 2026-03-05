<?php 

/**
 * Acci�n para inicializar el contexto para editar
 * una entidad.
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 21-04-2010
 * 
 */
abstract class CdtEditInitAction extends CdtOutputAction{

	//instancia de la entidad a editar.
	protected $oEntity;
	
	/**
	 * layout a utilizar para la salida.
	 * @return Layout
	 */
	protected function getLayout(){
		//el layuout ser� definido en la constante DEFAULT_LAYOUT
		
		//instanciamos el layout por reflection.
		$oLayout = CdtReflectionUtils::newInstance(DEFAULT_EDIT_LAYOUT);
		
		return $oLayout;
	}
	

	/**
	 * (non-PHPdoc)
	 * @see CdtOutputAction::getOutputContent();
	 */
	protected function getOutputContent(){
				
		try{
			
			//ciertas validaciones antes de mostrar la p�gina.
			$this->doValidate();
			
			//se vizualiza el contenido para editar.	
			$content = $this->getOutputContentImpl();
			
				
		}catch(GenericException $ex){
			
			//tratamos la excepci�n.
			$content = $this->doValidateException( $ex );
			
		}
		
		return $content;
	}
	
	/**
	 * Se obtiene el contenido a mostrar en la 
	 * salida del action.
	 * @return output string
	 */
	protected function getOutputContentImpl(){
		
		//obtenemos el template.
		$xtpl = $this->getXTemplate();

		$xtpl->assign ( 'WEB_PATH', WEB_PATH );
		
		//obtenemos la entidad a editar.
		$this->oEntity = $this->getEntity();
		
		//parseamos en el template la entidad a editar.
		$this->parseEntity( $this->oEntity , $xtpl);
		
		//se chequean los errores.
		$this->parseErrors( $xtpl );		

		$xtpl->assign ( 'title', $this->getOutputTitle() );
		
		//retornamos el contenido del template parseado.
		$xtpl->parse ( 'main' );
		return $xtpl->text ( 'main' );
	}
		
	/**
	 * xtemplate para editar la entidad.
	 * @return XTemplate
	 */
	protected abstract function getXTemplate();
	
	
	/**
	 * entidad a editar.
	 * @return object
	 */
	protected abstract function getEntity();

	/**
	 * parsea la entidad en el template para ser editada.
	 * @param object $oEntity entidad a parsear
	 * @param XTemplate $xtpl template donde parsear la entidad
	 */
	protected abstract function parseEntity($oEntity, XTemplate $xtpl);
	

	/**
	 * se parsean los errores.
	 * @param XTemplate $xtpl
	 */
	protected function parseErrors( XTemplate $xtpl ){
		
		//chequemos si existen errores asociados al request.
		if( CdtUtils::hasRequestError() && $xtpl->existsBlock( "main.msg" ) ){
		
			//los parseamos en el template.
			$error = CdtUtils::getRequestError();
			
			$msg  = $error['msg'];
			$code = $error['code'];
			
			$xtpl->assign ( 'msg', urldecode( $msg ) );
			$xtpl->assign ( 'code', $code );
			$xtpl->assign ( 'classMsg', 'msjerror' );
			$xtpl->parse ( 'main.msg' );

			
			
			CdtUtils::cleanRequestError();
		}
		
		
	}
	
	/**
	 * dejamos esta puerta para que se realicen ciertas validaciones
	 * antes de mostrar la p�gina.
	 */
	protected function doValidate(){
		//
	}

	/**
	 * En caso de que la validaci�n lance alguna excepci�n
	 * se ejecuta este m�todo.
	 * @param GenericException $ex excepci�n lanzada por la validaci�n.
	 * @return output string
	 */
	protected function doValidateException( GenericException $ex ){	
		
		//asociamos el error al request.
		CdtUtils::setRequestError( $ex );
		
		return $this->getExceptionOutputContentImpl($ex);
		
	}
	
	/**
	 * este es el contenido que se muestra cuando se lanza una exepci�n
	 * por validaci�n (siempre previa a la edici�n, ser�a en el init.
	 */
	protected function getExceptionOutputContentImpl(GenericException $ex){
		
		$xtpl =  new XTemplate(CDT_UI_TEMPLATE_EDIT_INIT_VALIDATION);
		$xtpl->assign('lbl_back', CDT_UI_LBL_BACK);
	
		$this->parseErrors( $xtpl );
        
		$xtpl->parse ( 'main' );
		return $xtpl->text ( 'main' );	
	}
	

	
}