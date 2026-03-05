<?php 

/**
 * Action para indicar que la página no existe.
 *  
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 16-03-2010
 * 
 */
class CdtPageNotFoundAction extends CdtAction{

	/**
	 * (non-PHPdoc)
	 * @see CdtAction::execute();
	 */
	public function execute(){
		
		$xtpl = new XTemplate ( CDT_MVC_TEMPLATE_PAGE_NOT_FOUND );
		
		$xtpl->assign ('title', 'Page Not Found');
		$xtpl->assign ( 'WEB_PATH', WEB_PATH );
		$xtpl->parse ( 'main' );
		$xtpl->out ( 'main' );
		
		$forward = null;
		return $forward;
	}
	

}