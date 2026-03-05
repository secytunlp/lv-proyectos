<?php 

/**
 * Acci�n para mostrar un panel de control.
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
		

		//t�tulo del panel.
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
	
	
	/**
	 * layout a utilizar para la salida.
	 * @return Layout
	 */
	protected function getLayout(){
		//el layuout ser� definido en la constante DEFAULT_LAYOUT
		//instanciamos el layout por reflection.
		
		if( defined("DEFAULT_PANEL_LAYOUT") )
			$oLayout = CdtReflectionUtils::newInstance(DEFAULT_PANEL_LAYOUT);
		else
			$oLayout = CdtReflectionUtils::newInstance(DEFAULT_LAYOUT);
		
		return $oLayout;
	}	
}