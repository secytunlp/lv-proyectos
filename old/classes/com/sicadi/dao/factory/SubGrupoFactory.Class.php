<?php

/**
 * Factory para SubGrupo
 *  
 * @author Marcos
 * @since 27-05-2014
 */
class SubGrupoFactory extends CdtGenericFactory {

    public function build($next) {

        $this->setClassName('SubGrupo');
        $subgrupo = parent::build($next);
        if(array_key_exists('cd_subgrupo',$next)){
        	$subgrupo->setOid( $next["cd_subgrupo"] );
        }

        return $subgrupo;
    }

}
?>
