<?php
require_once( CDT_EXTERNAL_LIB_PATH . 'fpdf17/fpdf.php' );
require_once( CDT_EXTERNAL_LIB_PATH . 'fpdi/fpdi.php' );

/**
 * Clase para unir documentos PDF
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 *
 */
class CdtPDFConcat extends FPDI {

	var $files = array();

	function setFiles($files) {
		$this->files = $files;
	}
	
	function addFile($file){
		$this->files[] = $file;
	}

	function concat() {
		foreach($this->files AS $file) {
			$pagecount = $this->setSourceFile($file);
			for ($i = 1; $i <= $pagecount; $i++) {
				$tplidx = $this->ImportPage($i);
				$s = $this->getTemplatesize($tplidx);
				$this->AddPage('P', array($s['w'], $s['h']));
				$this->useTemplate($tplidx);
			}
		}
	}

}