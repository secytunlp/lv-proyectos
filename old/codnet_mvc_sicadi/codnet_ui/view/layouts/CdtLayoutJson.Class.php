<?php

/**
 * Representa un layout para json
 * 
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 18-07-2011
 */
class CdtLayoutJson extends CdtLayout{

	/**
	 * (non-PHPdoc)
	 * @see CdtLayout::show();
	 */	
	public function show(){
		
		//CdtUIUtils::setCharset();
		
		//se realiza el encode para json.
		return json_encode( $this->getContent() );
	}
}
