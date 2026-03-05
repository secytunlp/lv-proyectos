<?php

/**
 * Factory para Categoria
 *
 * @author Marcos
 * @since 07-06-2023
 */
class CategoriasicadiFactory extends CdtGenericFactory {



	public function build($next) {


		$this->setClassName('Categoriasicadi');
		$cat = parent::build($next);
		if(array_key_exists('cd_categoriasicadi',$next)){

					$cat->setOid( $next["cd_categoriasicadi"] );

		}

		return $cat;
	}

}
?>
