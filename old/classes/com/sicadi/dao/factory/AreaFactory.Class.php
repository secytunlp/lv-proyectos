<?php

/**
 * Factory para Area
 *
 * @author Marcos
 * @since 26-06-2023
 */
class AreaFactory extends CdtGenericFactory {

	public function build($next) {

		$this->setClassName('Area');
		$Area = parent::build($next);
		if(array_key_exists('cd_area',$next)){
			$Area->setOid( $next["cd_area"] );
		}

		return $Area;
	}

}
?>
