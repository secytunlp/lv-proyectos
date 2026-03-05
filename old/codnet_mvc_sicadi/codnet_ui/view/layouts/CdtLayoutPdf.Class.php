<?php

/**
 * Representa el layout b�sico para exportar a PDF:
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 29-03-2011
 */
class CdtLayoutPdf extends CdtLayout{
	
	//nombre del archivo pdf.
	private $fileName;

	/**
	 * (non-PHPdoc)
	 * @see CdtLayout::show();
	 */
	public function show(){
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: public");
		header("Content-Description: File Transfer");

		CdtUIUtils::setCharset("application/pdf");
		
		header("Content-Disposition: attachment; filename=". $this->getNombreArchivo() .".pdf");
		
		//obtenemos el template
		$xtpl = $this->getXTemplate ();
		
		//parseamos las secciones.
		$xtpl->assign('title', $this->getTitle());
		$xtpl->assign('header', $this->getHeader());
		$xtpl->assign('content', $this->getContent() );
		$xtpl->assign('footer', $this->getFooter());
		
		$xtpl->parse('main');
		return $xtpl->text('main');
	}

	/**
	 * retorna el contenido a mostrar en el header.
	 * @return string header.
	 */
	protected function getHeader(){
		return "";
	}
	
	/**
	 * retorna el contenido a mostrar en el footer.
	 * @return string footer.
	 */
	protected function getFooter(){
		return "";
	}
	
	/**
	 * se obtiene el template asociado al layout.
	 * @return XTemplate
	 */
	protected function getXTemplate(){
		return new XTemplate(CDT_MVC_TEMPLATE_LAYOUT_PDF);
	}	

	/* Getters & Setters */
	public function getFileName()
	{
	    return $this->fileName;
	}

	public function setFileName($fileName)
	{
	    $this->fileName = $fileName;
	}	
}
