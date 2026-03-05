<?php

/**
 * Representa el layout b�sico para exportar a Excel
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 03-06-2010
 */
class CdtLayoutXls extends CdtLayout{
	
	//nombre del archivo xls.
	private $fileName;
	
	
	/**
	 * (non-PHPdoc)
	 * @see CdtLayout::show();
	 */	
	public function show(){
		
		//se modifica el header para indicar la salida a un archivo xls.
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: public");
		header("Content-Description: File Transfer");
		
		CdtUIUtils::setCharset("application/vnd.ms-excel");
		
		header("Content-Disposition: attachment; filename=". $this->getFileName() .".xls");
				
		
		//obtenemos el template
		$xtpl = $this->getXTemplate ();
		
		//parseamos las secciones.
		$xtpl->assign('title', $this->getTitle());
		$xtpl->assign('header', $this->getHeader());
		$xtpl->assign('content', $this->getContent());
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
		return new XTemplate(CDT_UI_TEMPLATE_LAYOUT_XLS);
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
