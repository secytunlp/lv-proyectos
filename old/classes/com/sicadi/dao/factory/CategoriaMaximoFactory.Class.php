<?php

/**
 * Factory para CategoriaMaximo
 *  
 * @author Marcos
 * @since 05-12-2013
 */
class CategoriaMaximoFactory extends CdtGenericFactory {

    public function build($next) {

        $this->setClassName('CategoriaMaximo');
        $categoriamaximo = parent::build($next);
        if(array_key_exists('cd_categoriamaximo',$next)){
        	$categoriamaximo->setOid( $next["cd_categoriamaximo"] );
        }
        
        $factory = new CategoriaFactory();
        $factory->setAlias( CYT_TABLE_CATEGORIA . "_" );
        $categoriamaximo->setCategoria( $factory->build($next) );

        return $categoriamaximo;
    }

}
?>
