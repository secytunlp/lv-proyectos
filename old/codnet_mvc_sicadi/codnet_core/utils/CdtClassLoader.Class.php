<?php

/**
 * Realiza la carga y el include de las clases.
 * 
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 02-03-2010
 */
class CdtClassLoader{
	
	
	/*
	 * Mantener esta clase como singleton ya no tiene sentido.
	 * 
	 * Ahora las clases se cargan en un hashmap en sesi�n.
	 * 
	 */
	
	private static $instancia;
	private $classpath;
	
	
	private function __construct(){
		 
	}

	public static function getInstance(){
		if (  !self::$instancia instanceof self ) {
			self::$instancia = new self;
			
			//self::$instancia->setClasspath(self::$instancia->armarDirectorios(APP_PATH.CLASS_PATH));
			
			
		}
		
		return self::$instancia;
	}
	
	
	/**
	 * carga la clase (include_once).
	 * @param $ds_class_name nombre de la clase a cargar.
	 * @return null.
	 */
	static function loadClass($ds_class_name){
		if(!class_exists($ds_class_name)){
			
			$current = self::getInstance();
			$ds_file_name = $current->getClassFile($ds_class_name);
			
			
			include_once $ds_file_name;
			
			
			
		}
	}

	/**
	 * obtiene la ubicaci�n de la clase.
	 * @param $ds_class_name nombre de la clase a buscar.
	 * @return $filename url de la clase.
	 */
	public function getClassFile($ds_class_name){

		if( CLASS_LOADER_FROM_SESSION )
			return $this->getClassFileSession($ds_class_name);
		else
			return $this->getClassFileCache($ds_class_name);
		
	}
	
	/**
	 * obtiene la ubicaci�n de la clase.
	 * @param $ds_class_name nombre de la clase a buscar.
	 * @return $filename url de la clase.
	 */
	public function getClassFileCache($ds_class_name){

		$filenameHashMap = APP_PATH . 'conf/cache/hashClassMap'  . session_id()  .   '.php';
		
		if ( file_exists ( $filenameHashMap ) ){
			include $filenameHashMap;
		}
		
		
		$key = $ds_class_name.'.Class.php';
		
		if (!isset($hash["$key"]) || !is_file($hash["$key"]) ){
			$directorios = explode(",", CLASS_PATH);
			$hashAux = array();
			for ($index = 0; $index < count($directorios); $index++) {
				$dir = $directorios[$index];
				$hashAux = array_merge ($hashAux,  $this->buildHash($dir) );
			}
			$fp = fopen($filenameHashMap, 'w');
			fwrite($fp,"<?php \n");

			foreach ($hashAux as $key => $value) {
				//fwrite($fp, '$hash["'.$key.'"]="'.$value.'";');
				fwrite($fp, "\t\$hash[\"$key\"]=\"$value\";\n");
			}
			fwrite($fp,' ?>');
			fclose($fp);
			include $filenameHashMap;
		}

		$ds_file_name = $hash["$key"];
		$found = !empty( $ds_file_name ) ;

		
		if(!$found)
			throw new ClassNotFoundException($ds_class_name);
		return $ds_file_name;
	}

	/**
	 * obtiene la ubicaci�n de la clase.
	 * @param $ds_class_name nombre de la clase a buscar.
	 * @return $filename url de la clase.
	 */
	public function getClassFileSession($ds_class_name){

		if ( !isset($_SESSION ["hashClasses"]) ){
			//$_SESSION ["hashClasses"] = $this->buildHash(APP_PATH.CLASS_PATH);
			$directorios = explode(",", CLASS_PATH);

			$hash = array();
			for ($index = 0; $index < count($directorios); $index++) {
				$dir = $directorios[$index];
				$hash = array_merge ($hash,  $this->buildHash($dir) );
			}

			$_SESSION ["hashClasses"] = $hash;
			
		}
//print_r($_SESSION ["hashClasses"]);
		$ds_file_name = $_SESSION ["hashClasses"][$ds_class_name . '.Class.php'];
			
		$found = !empty( $ds_file_name ) && is_file( $ds_file_name ) ;				
		
		
		//si no encuentra la clase, volvemos a generar el hashmap por si es una clase nueva.
		if(!$found){
			//$_SESSION ["hashClasses"] = $this->buildHash(APP_PATH.CLASS_PATH);
			$directorios = explode(",", CLASS_PATH);
			$hash = array();
			for ($index = 0; $index < count($directorios); $index++) {
				$dir = $directorios[$index];
				$hash = array_merge ($hash,  $this->buildHash($dir) );
			}
			$_SESSION ["hashClasses"] = $hash;
			
			$ds_file_name = $_SESSION ["hashClasses"][$ds_class_name . '.Class.php'];
			$found = !empty( $ds_file_name ) && is_file( $ds_file_name ) ;
		}
		//echo 'no esta '.$ds_class_name."<br>";
		if(!$found)
			throw new ClassNotFoundException($ds_class_name);
		return $ds_file_name;
	}


	/*
	 * arma un hash donde la key es el nombre de una clase y el value es el path al archivo de la clase.
	 * 
	 * NombreClase -> Url ubicaci�n f�sica: Cliente -> /cdt/modelo/Cliente.Class.php
	 * 
	 */
	public function buildHash($dir){
		$hash = array();
		
		//vemos los directorios a no tener en cuenta, a excluir.
		$excluded = explode(",", CLASS_PATH_EXCLUDE);
		
		if (empty ( $excluded ))
			 $excluded=array();
			 
		$excluded[] = ".";
		$excluded[] =  "..";
		$excluded[] =  ".svn";
			 
		if (is_dir($dir) && !in_array($dir, $excluded ) ) { //debe ser un directorio y no tiene que estar excluido del classpath
		    if ($dh = opendir($dir)) {
		        while (($file = readdir($dh)) !== false) {
		            $dirNext = $dir.'/'.$file;
		            if($file!='.' && $file!='..' && (!strstr($file,'svn')) && (!$this->isExcluded($file)) ){
		            	
		            	if( is_dir($dirNext) ){
		            		
		            		$hash = array_merge ($hash,  $this->buildHash($dirNext) );
		            								
		            	}elseif(strstr($file,'.Class.php')){
				            //vamos armando el hash.
				            $hash[$file] = $dirNext ; 	
		            	}
		            }
		        }
		        closedir($dh);
		    }
		}
		return $hash;
	}
	
	
	private function setClasspath($value){
		$this->classpath = $value;
	}
	
	private function getClasspath(){
		return $this->classpath;
	}

	/**
	 * retorna true si el directorio $dir est� dentro de la lista
	 * de directorios excluidos, sino false.
	 * TODO revisarlo
	 */
	protected function isExcluded( $dir ){
		$directorios_excluidos = explode(",",CLASS_PATH_EXCLUDE);
		foreach ( $directorios_excluidos as $key => $excluded ) {  
            if( $dir == $excluded ) {  
             return true;  
            }  
  		}
  		return false;   
	}
}
