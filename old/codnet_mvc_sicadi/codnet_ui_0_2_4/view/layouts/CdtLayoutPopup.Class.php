<?php

/**
 * Representa un layout de la forma:
 * 
 *  <content>
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 06-04-2010
 * @deprecated
 */
abstract class CdtLayoutPopup extends CdtLayoutHeaderContentFooter{

	/**
	 * (non-PHPdoc)
	 * @see CdtLayoutHeaderContentFooter::getXTemplate();
	 */
	protected function getXTemplate(){
		return new XTemplate(CDT_MVC_TEMPLATE_LAYOUT_POPUP);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtLayoutHeaderContentFooter::getHeader();
	 */
	protected function getHeader(){}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtLayoutHeaderContentFooter::getFooter();
	 */
	protected function getFooter(){}

	/**
	 * (non-PHPdoc)
	 * @see CdtLayoutHeaderContentFooter::parseMetaTags();
	 */
	protected function parseMetaTags($xtpl){}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtLayoutHeaderContentFooter::parseStyles();
	 */
	protected function parseStyles($xtpl){}
	
	/**
	 * (non-PHPdoc)
	 * @see CdtLayoutHeaderContentFooter::parseScripts();
	 */
	protected function parseScripts($xtpl){}
	
	
	
}
