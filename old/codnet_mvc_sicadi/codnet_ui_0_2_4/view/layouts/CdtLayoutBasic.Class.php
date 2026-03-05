<?php

/**
 * Representa un layout de la forma:
 *	
 *  <content>
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 06-04-2010
 */
class CdtLayoutBasic extends CdtLayout{

	
	/**
	 * (non-PHPdoc)
	 * @see CdtLayout::show();
	 */
	public function show(){
		//simplemente se retorna el content.
		return $this->getContent();
	}
}
