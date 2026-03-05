<?php

/**
 * Factory para Subarea
 *
 * @author Marcos
 * @since 26-06-2023
 */
class SubareaFactory extends CdtGenericFactory {

	public function build($next) {

		$this->setClassName('Subarea');
		$Subarea = parent::build($next);
		if(array_key_exists('cd_subarea',$next)){
			$Subarea->setOid( $next["cd_subarea"] );
		}

		$factory = new AreaFactory();
		$factory->setAlias( CYT_TABLE_AREA . "_" );
		$Subarea->setArea( $factory->build($next) );

		return $Subarea;
	}

}
?>
