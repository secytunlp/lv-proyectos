<?php 

/**
 * Acción para mostrar un panel de control.
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 08-03-2011
 * 
 */
abstract class CdtPanelAction extends CdtOutputAction{

	/**
	 * se muestra un panel de control para
	 * acceder a las operaciones.
	 * 
	 * (non-PHPdoc)
	 * @see CdtOutputAction::getOutputContent();
	 */
	protected function getOutputContent(){

		$xtpl = $this->getXTemplate();
		$xtpl->assign('WEB_PATH', WEB_PATH);	
		

		//título del panel.
		$xtpl->assign( 'title', $this->getOutputTitle() );
		
		//generamos el contenido.
		$this->parsePanel( $xtpl );
		
		$xtpl->parse('main' );
		return $xtpl->text( 'main' );

	}

	/**
	 * (non-PHPdoc)
	 * @see CdtOutputAction::getXTemplate();
	 */
	protected abstract function getXTemplate();

	/**
	 * (non-PHPdoc)
	 * @see CdtOutputAction::getOutputTitle();
	 */
	protected function getOutputTitle(){
		return CDT_UI_MSG_PANEL_TITLE;
	}
	
	/**
	 * Se parsea el panel de control en el template.
	 * @param XTemplate $xtpl template donde parsear el panel.
	 */
	protected abstract function parsePanel( XTemplate $xtpl );	
	
	
	
}