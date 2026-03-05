<?php

/**
 * El actionController decide quï¿½ acciones ejecutar. * 
 * (dispatcher).
 *  
 * @author Bernardo Iribarne (bernardo.iribarne@codnet.com.ar)
 * @since 03-03-2010
 */
class CdtActionController{

	//map helper con el mapeo del archivo de navegaciï¿½n.
	private $map;
	//filtros.
	private $filters;
	
	public function __construct(){
		
	}
	
	/**
	 * se ejecuta el action indicado o el que viene por url.
	 * @param string $ds_actionName action a ejecutar (si es blanco se toma de la url)
	 * @return output string
	 */	
	public function execute( $ds_actionName='' ){
		
		if(empty($ds_actionName))
			//obtenemos el action desde el parï¿½metro.
			$ds_actionName = CdtUtils::getCurrentAction();
		
		
		try{
			
			//inicializa el mapeo de acciones.
			$map = $this->getCdtActionMapHelper();
			
			//obtenemos la acciï¿½n a ejecutar (el nombre de la clase).
			$ds_action=$map->getAction($ds_actionName);
			
			CdtUtils::log_debug("acción a ejecutar $ds_action", __CLASS__);
			
			
			//instanciamos la acción por reflection.
			$oAction = CdtReflectionUtils::newInstance($ds_action);

			
			//aplicamos los filtros.
			$this->applyFilters( $map->getFilters(), $ds_actionName, $oAction, $map );
			CdtUtils::log_debug("Filtros aplicados", __CLASS__);
			
			//ejecutamos la acción.
			$ds_actionResult = $oAction->execute();
			
			CdtUtils::log_debug("acción ejecutada", __CLASS__);
			
			//obtenemos la vista de acuerdo al resultado.
			$ds_forward = $map->getForward($ds_actionResult);
			
			
			//tiene sentido que una acción setee el forward a null cuando
			//dicha acción renderiza la vista utilizando XTemplate.
			if($ds_forward!=null)	{
				
				CdtUtils::log_debug("ds_forward $ds_forward", __CLASS__);
						
				//vemos si la acción tiene parámetros para el forward.
				if($oAction->getDs_forward_params()!=null){
					
					
					//chequeamos si el forward ya tiene parámetros (?).
					if(!$this->hasParameters($ds_forward))
						$ds_forward .= '?';
					else
						$ds_forward .= '&';
					
					$ds_forward .= $oAction->getDs_forward_params();
				}
				
				CdtUtils::log_debug("Redireccionando a $ds_forward", __CLASS__);
				$this->doForward( $ds_forward );
				
				
			}
			
			CdtUtils::log_debug("Terminando controller", __CLASS__);
			
			//se cierra la conexión a la bbdd.
			CdtDbManager::close();
			
		}catch(ReflectionException $e1){
			//no existe la acción requerida
			
			$ge = new GenericException( $e1->getMessage(), $e1->getCode());
			
			$this->doError( $ge );
			//se cierra la conexión a la bbdd.
			CdtDbManager::close();
			
			
		}catch(FailureException $fe){
			//se cierra la conexión a la bbdd.
			CdtUtils::log_debug("Cerrando conexión bbdd", __CLASS__);
			CdtDbManager::close();
			//exception que indica un error en la acción ejecutada.
			CdtUtils::log_debug("Redireccionando x FailureException:" . $fe->getDs_actionName() . " msg: " . $fe->getMessage() , __CLASS__);
			$this->doFailureForward( $fe );
			
		}catch(GenericException $e2){
			//se cierra la conexión a la bbdd.
			CdtUtils::log_debug("Cerrando conexión bbdd", __CLASS__);
			CdtDbManager::close();
			//error no esperado.
			//print_r($e2);
			$this->doError( $e2 );
			
		}catch(Exception $e3){
			//se cierra la conexión a la bbdd.
			CdtUtils::log_debug("Cerrando conexión bbdd", __CLASS__);
			CdtDbManager::close();
			//error no esperado.
			$this->doError( $e3 );
			
			
		}

	}	
	
	
	/**
	 * se trata el error  por una excepción.
	 * @param Exception $e
	 * @return output string
	 */
	protected function doError( Exception $e ){
		
		/*
		//asociamos el error al request.
		CdtUtils::setRequestError( $e );
		
		CdtUtils::log_error( get_class($this) . " > error() => Error no esperado: code => " . $e->getCode() . " msg => " . $e->getMessage(), __CLASS__);

		if( get_class($e) == "DBException" )
			echo "Error no esperado: code => " . $e->getCode() . " msg => " . $e->getMessage() ;
		return $this->execute( "error" );
		*/
		$handler = new CdtExceptionHandler();
		$action = $handler->doHandle( $e );
		if( !empty($action) )
			return $this->execute($action);
		
	}
		
	/**
	 * se realiza el forward.
	 * @param string $ds_forward forward a realizar.
	 */
	protected function doForward( $ds_forward ){
		/*
		//agarrar los parámetros que están en ds_forward y agregarlos al GET, agarrar la acción,
		// y hacer un execute (similar a doFailureException).
		
		/*
		//tomamos los parámetros y los dejamos en el get.
		$params = $this->getParameters( $ds_forward );
		
		foreach ($params as $param) {
			
			$_GET[$param["key"]] = $param["value"];
		}
		
		//buscamos la acción a ejecutar.
		$ds_action = CdtUtils::getActionFromUrl( $ds_forward );
		
		//ejecutamos la acción.
		$this->execute( $ds_action );
		*/
		
		
		/* TODO voy a probar dejandolo en session
		 * el que lo muestra lo borra.
		 * 
		if( CdtUtils::hasRequestError()){
			$error = CdtUtils::getRequestError();
			
			$ds_forward .= "&error_msg=" .  $error["msg"] ;
		}
		*/
		/* 
		if( CdtUtils::hasRequestInfo()){
			$info = CdtUtils::getRequestInfo();
			
			$ds_forward .= "&info_msg=" .  $info["msg"] ;
		}
		*/
		header ( 'Location: '.  $ds_forward );
	}
	
	
	/**
	 * para determinar si el forward viene con parámetros
	 * @param string $ds_forward forward a evaluar.
	 * @return boolean
	 */
	private function hasParameters($ds_forward){
		
		return strpos($ds_forward, '?');
		
	}
	
	/**
	 * obtiene los parámetros del forwared.
	 * @param string $ds_forward forward de donde obtener los parámetros
	 * @return array(string) arreglo con los parámetros.
	 */
	private function getParameters($ds_forward){
		
		
		if($this->hasParameters( $ds_forward )){
			
			$parameters = strrchr($ds_forward, '?');
			
			//le quitamos el "?"
			$parameters = substr( $parameters, 1, strlen($parameters)-1);
			
			CdtUtils::log_debug( "parametros => $parameters " , __CLASS__);
			
			//lo pasamos a array.
			$parameters = explode( "&", $parameters );
			
			$result = array();
			
			//cada elemento es del estilo  'nombre=valor'
			foreach ($parameters as $param) {

				if($param[0]!="action"){
				
					CdtUtils::log_debug( "param => $param " , __CLASS__);
								
					$param = explode( "=", $param );	
	
					CdtUtils::log_debug( "param[0] " . $param[0], __CLASS__ );
					CdtUtils::log_debug( "param[1] " . $param[1], __CLASS__ );
					
					array_push ( $result , array( "key" => $param[0], "value" => $param[1] ) ) ;
					
				}
				
				
			}
			
			
		}else
			$result = array();
		
		
		return $result;
	}

	/**
	 * 
	 * @deprecated
	 */
	protected function doFailureForward( FailureException $fe ){
		//asociamos el error al request.
		CdtUtils::setRequestError( $fe);
		
		return $this->execute( $fe->getDs_actionName() );
	}

	/**
	 * retorna el helper de navegación.
	 * @return ActionMapHelper
	 */
	protected function getCdtActionMapHelper(){
		
		//chequeamos en caché
		
		$cache = CdtCache::getInstance();
		$cacheKey = "myCdtActionMapHelper";
		if( $cache->contains( $cacheKey ) ){
			$mapHelper = $cache->fetch( $cacheKey );
		}else{
			$mapHelper = new CdtActionMapHelper();
			$cache->save( $cacheKey, $mapHelper );
		}
		
		return  $mapHelper;
	}
	
	/**
	 * se aplican los filtros seteados en la navegación.
	 * @param array $filters filtros a aplicar
	 * @param string $ds_actionName nombre de la acción a la cual se le aplican los filtros.
	 * @param CdtAcion $oAction acción a la cual se le aplican los filtros.
	 * @param ActionMapHelper $map helper de navegación.
	 */
	protected function applyFilters($filters, $ds_actionName, $oAction, CdtActionMapHelper $map){
		
		if (!is_null($_SERVER) && isset($_SERVER['REQUEST_URI'])) {
			$url = $_SERVER['REQUEST_URI'];
		
			//se evalúan cada uno de los filtros
			foreach ($filters as $nombre => $filter) {

				$urlPattern = $filter['urlPattern'];

				//el filtro se aplica si la url matchea con el patrón del filtro.
				if( CdtUtils::match($url, $urlPattern) ){
					
					//instanciamos el filtro por reflection.
					$oFiltro = CdtReflectionUtils::newInstance( $filter['class'] );
					
					//lo aplicamos.
					$oFiltro->apply( $ds_actionName, $oAction );
				}
			}
		}
	}
	
	/**
	 * @deprecated
	 */
	protected function getFiltersForUrl( $filters, $url = "/" ){
		$filterRes = array();
		
		foreach ($filters as $nombre => $filter) {

			$urlPattern = $filter['urlPattern'];
			
			$filterRes[] = $filter;
		}
		return $filterRes;
		
	}
	
	
	
}