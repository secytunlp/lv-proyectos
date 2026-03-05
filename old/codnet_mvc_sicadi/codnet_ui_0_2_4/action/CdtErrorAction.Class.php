<?php 

/**
 * Acción para redireccionar a la página de error.
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 17-03-2010
 * 
 */
class CdtErrorAction extends CdtOutputAction{

	
	/**
	 * (non-PHPdoc)
	 * @see CdtOutputAction::getXTemplate();
	 */
	protected function getXTemplate(){
		return $xtpl = new XTemplate ( CDT_UI_TEMPLATE_ERROR );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtOutputAction::getOutputContent();
	 */
	protected function getOutputContent(){

		$xtpl = $this->getXTemplate ();
		
		$xtpl->assign ( 'title', $this->getOutputTitle() );
		
		if( CdtUtils::hasRequestError() ){
		
			$error = CdtUtils::getRequestError();
			
			$msg  = $error['msg'];
			$code = $error['code'];
			
		}
		
		//TODO tratamiento de los códigos de error.
		if (!empty ( $code)) {
			
			$xtpl->assign ( 'code', $code );
			if($code==-1)
				$msg = 'Functionality undefined<br><br><br>';
		
			elseif($code==1064)
				$msg = 'Database operation fail<br><br><br>' . $msg;
								
		
		}			

		if (!empty ( $msg))
			$xtpl->assign ( 'msg', $msg );
		
		$xtpl->parse ( 'main' );
		return $xtpl->text ( 'main' );
	}

	
	public function getOutputTitle(){
		return 'Unexpected error';
	}
}