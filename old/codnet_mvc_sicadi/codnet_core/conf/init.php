<?php 	

include_once('constants.php');
include_once('messages.php');

//incluimos el classLoader.
include_once CDT_CORE_PATH . 'utils/CdtClassLoader.Class.php';

function autoload($class_name) {

	if ($class_name != 'CdtClassLoader'){
			//el class loader se encarga de incluir la clase.
			try{
				CdtClassLoader::loadClass($class_name);
			}catch(ClassNotFoundException $e){
				//TODO hacer algo!!!					
			}
			
	}
}
spl_autoload_register('autoload');

include_once('error_handler.php');
?>