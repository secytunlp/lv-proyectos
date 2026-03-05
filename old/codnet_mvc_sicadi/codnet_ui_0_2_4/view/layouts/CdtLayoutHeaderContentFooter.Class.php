<?php

/**
 * Representa un layout de la forma:
 * 
 *	{<meta-tags>, <scripts>, <estilos>} 
 *  <header>
 *  <content>
 *  <footer>
 * 
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 06-04-2010
 */
abstract class CdtLayoutHeaderContentFooter extends CdtLayout{

	/**
	 * (non-PHPdoc)
	 * @see CdtLayout::show();
	 */
	public function show(){
		
		//obtenemos el template
		$xtpl = $this->getXTemplate ();
		
		$xtpl->assign('title', $this->getTitle());
		$xtpl->assign('header', $this->getHeader());
		$xtpl->assign('content', $this->getContent());
		$xtpl->assign('footer', $this->getFooter());
		$this->parseMetaTags($xtpl);
		$this->parseEstilos($xtpl);
		$this->parseScripts($xtpl);
		
		$this->parseException($xtpl);
		
		$xtpl->parse('main');

		return $xtpl->text('main');
	}

	/**
	 * se obtiene el template asociado al layout.
	 * @return XTemplate
	 */
	protected function getXTemplate(){
		return new XTemplate(CDT_MVC_TEMPLATE_LAYOUT_HEADER_CONTENT_FOOTER);
	}
	
	/**
	 * retorna el contenido a mostrar en el header.
	 * @return string header.
	 */
	protected abstract function getHeader();
	
	/**
	 * retorna el contenido a mostrar en el footer.
	 * @return string footer.
	 */
	protected abstract function getFooter();

	/**
	 * parsea meta-tags del contenido.
	 * @param XTemplate $xtpl xtemplate donde se parsean los meta-tags
	 */
	protected abstract function parseMetaTags($xtpl);
	
	/**
	 * parsea los estilos css del contenido.
	 * @param XTemplate $xtpl xtemplate donde se parsean los estilos
	 */
	protected abstract function parseStyles($xtpl);
	
	/**
	 * parsea los scripts incluidos en el contenido.
	 * @param XTemplate $xtpl xtemplate donde se parsean los scripts
	 */
	protected abstract function parseScripts($xtpl);
	
	
	/**
	 * parsea le mensaje de error por exception.
	 * @param XTemplate $xtpl xtemplate donde se parsea el mensaje de error.
	 */
	protected function parseException(XTemplate $xtpl){
		$exception = $this->getException();
		if( !empty($exception) ){
			$xtpl->assign('error_message', $exception->getMessage() );
			$xtpl->parse('main.error_message');
		}		
		
	}
}
