<?php

/**
 * Realiza la carga y el include de las clases.
 * 
 * 
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 02-03-2010
 */
class CdtClassLoader{
	
	
	
	/***************************/
	/* EDIT CONFIGURATION HERE */
	/***************************/
	
	/**
	 * Only files ending with this suffix will be taken into account
	 */
	const CLASSFILE_SUFFIX = '.Class.php';
	
	/**
	 * Set whether or not to use the cache. If you have a lot of classes,
	 * highly recommended to true!
	 */
	const USE_CACHE = true;
	
	/**
	 * What to use for cache
	 * 'APC': the fastest, but must be supported by your configuration
	 * 'FILE': second best choice, but you need a writable directory somewhere
	 * 'SESSION': last choice, if APC and FILE not possible. You have to care about session_start()!
	 */
	const CACHE_TYPE = 'APC';
	
	/**
	 * The path to the cache file when using a file for cache
	 * Make sure PHP can write in the directory
	 */
	const CACHEFILE_PATH = "conf/cache/classLoader.cache";
	
	/**
	 * The cache lifetime in seconds when using APC cache
	 * Default to 0, never expire. Clear the cache manually with classLoader::clearCache_APC()
	 */
	const APC_CACHE_LIFETIME = 0;
	
	const APP_ID = CACHE_ID;
	
	
	
	/**
	 * carga la clase (include_once).
	 * @param $className nombre de la clase a cargar.
	 * @return null.
	 */
	public static function loadClass($className){
		
		if(!class_exists($className)){
			//echo $className . "<br />";
			switch(self::CACHE_TYPE){
				
				case 'APC': $path = self::getAndCache_APC($className); break;
				case 'FILE': $path = self::getAndCache_file($className); break;
				case 'SESSION': $path = self::getAndCache_session($className); break;
				
				default: trigger_error('Configuration: cache type not supported: "'.self::USE_SESSION_CACHE.'".', E_USER_ERROR);
			}
			if(is_null($path)){
				//trigger_error('Could not load class ' . $className . '.', E_USER_ERROR);
			}else{
				require_once $path;
			}
			
			
		}
	}

	
	/**
	 * Called by __autoload function. Get and cache the path of given class
	 * into APC cache.
	 *
	 * @param string $className
	 * @return string the path to the file
	 */
	public static function getAndCache_APC($className){
		$path = null;
		if(!self::USE_CACHE){
			return self::getFromAllClassPath($className.self::CLASSFILE_SUFFIX);
		}
		$path = apc_fetch(self::APP_ID.$className);
		if($path !== false){
			return $path;
		}
		else{
			$path = self::getFromAllClassPath($className.self::CLASSFILE_SUFFIX);
			apc_store(self::APP_ID.$className, $path, self::APC_CACHE_LIFETIME);
		}
		return $path;
	}
	
	/**
	 * Called by __autoload function. Get and cache the path of given class
	 * into session. Don't forget session_start()!
	 *
	 * @param string $className
	 * @return string the path to the file
	 */
	public static function getAndCache_session($className){
		$path = null;
		if(!self::USE_CACHE){
			return self::getFromAllClassPath($className.self::CLASSFILE_SUFFIX);
		}
		
		if(isset($_SESSION['classLoader'])){
			if(isset($_SESSION['classLoader'][$className])){
				$path = $_SESSION['classLoader'][$className];
			}
			else{
				$path = self::getFromAllClassPath($className.self::CLASSFILE_SUFFIX);
				$_SESSION['classLoader'][$className] = $path;
			}
		}
		else{
			$path = self::getFromAllClassPath($className.self::CLASSFILE_SUFFIX);
			$_SESSION['classLoader'] = array();
			$_SESSION['classLoader'][$className] = $path;
		}
		return $path;
	}
		
	public static function getAndCache_file($className){
		$path = null;
		if(!self::USE_CACHE){
			return self::getFromAllClassPath($className.self::CLASSFILE_SUFFIX);
		}
		
		if(file_exists(self::CACHEFILE_PATH)){
			// the cache file exist, we try to extract the class path
			$fp = fopen(self::CACHEFILE_PATH, 'r+b');
			$cacheData = unserialize(fread($fp, filesize(self::CACHEFILE_PATH) > 0 ? filesize(self::CACHEFILE_PATH) : 1));
			// the classname was found
			if(isset($cacheData[$className])){
				$path = $cacheData[$className];
			}
			// else get the path and update the cache
			else{
				$path = self::getFromAllClassPath($className.self::CLASSFILE_SUFFIX);
				$cacheData[$className] = $path;
				rewind($fp);
				if(fwrite($fp, serialize($cacheData)) === false){
					trigger_error('Could not write in cache file ' . self::CACHEFILE_PATH . '.');
				}
			}
			fclose($fp);
		}
		else{
			$path = self::getFromAllClassPath($className.self::CLASSFILE_SUFFIX);
			$toStore = serialize(array($className => $path));
			$fp = @fopen(self::CACHEFILE_PATH, 'wb');
			if($fp){
				if(fwrite($fp, self::CACHEFILE_PATH, $toStore) === false){
					trigger_error('Could not write in cache file ' . self::CACHEFILE_PATH . '.');
				}
				fclose($fp);
			}
			else{
				trigger_error('Could not open cache file: ' . self::CACHEFILE_PATH, E_USER_ERROR . '.');
			}
		}
		return $path;
	}

	/**
	 * get a class name and a directory path to search
	 * Tries to find the corresponding file ending with CLASSFILE_SUFFIX and
	 * return its path
	 * Recursive function
	 *
	 * @param string $className "Site.class.php" for example
	 * @param string $basePath
	 * @return string the path to the file
	 */
	private static function getFromAllClassPath($className){
		
		$classpath = explode(",", CLASS_PATH );
		$classpath_exclude = explode(",", CLASS_PATH_EXCLUDE );
		
		foreach ($classpath as $basePath) {
		
			$path = self::getClassPath($className, $basePath, $classpath_exclude);
			
			if(!empty($path))
				return $path;
		
		}
		
		
		return null;
	}	
	
	
	/**
	 * get a class name and a directory path to search
	 * Tries to find the corresponding file ending with CLASSFILE_SUFFIX and
	 * return its path
	 * Recursive function
	 *
	 * @param string $className "Site.class.php" for example
	 * @param string $basePath
	 * @return string the path to the file
	 */
	private static function getClassPath($className, $basePath,$classpath_exclude){
		$handle = @opendir($basePath);
		if($handle === false){
			//trigger_error('Could not open directory ' . $basePath . '.', E_USER_ERROR);
			return null;
		}
		while(false !== ($file = readdir($handle))){
			if($file != '.' && $file != '..'){
				if(is_dir($basePath.'/'.$file) && $file != '.svn' && !self::isExcluded($file,$classpath_exclude)){
					$in_dir = self::getClassPath($className, $basePath.'/'.$file,$classpath_exclude);
					if(!is_null($in_dir)){
						return $in_dir;
					}
				}
				else{
					if(strtolower($file) == strtolower($className)){
						return $basePath.'/'.$file;
					}
				}
			}
		}
		return null;
	}	
	
	private static function isExcluded($file, $classpath_exclude){
		foreach ( $classpath_exclude as $key => $excluded ) {  
            if( $file == $excluded ) {  
             return true;  
            }  
  		}
  		return false;
	}
}
