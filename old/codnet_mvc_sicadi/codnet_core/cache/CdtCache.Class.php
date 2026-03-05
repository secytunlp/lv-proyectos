<?php

/**
 * Caché para el mvc.
 * 
 * @author bernardo
 * @since 27-11-2013
 */
class CdtCache{
	
	private static $instance;
	/**
	 * implementación de cache doctrine
	 * @var ICdtCache
	 */
	private $cache;
	
	private $cacheId;
	
	private function __construct(){
		
		//TODO podríamos tener una variable de configuración
		//para determinar el tipo de caché a utilizar.
		
		//$this->cache = new CdtApcCache();
		$this->cache = new CdtMockCache();
			
		//$this->cacheId = CACHE_ID;
	}
	
	public static function getInstance(){
		if (  !self::$instance instanceof self ) {
			self::$instance = new self;
		}
		return self::$instance;
	}
	
    /**
     * {@inheritdoc}
     */
    public function fetch($id)
    {
        return $this->cache->fetch( $this->getCacheId() . $id);
    }

    /**
     * {@inheritdoc}
     */
    public function contains($id)
    {
        return $this->cache->contains($this->getCacheId() .$id);
    }

    /**
     * {@inheritdoc}
     */
    public function save($id, $data, $lifeTime = 0)
    {
        return $this->cache->save($this->getCacheId() .$id, $data, $lifeTime);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        return $this->cache->delete($this->getCacheId() .$id);
    }

    /**
     * {@inheritdoc}
     */
    public function getStats()
    {
        return $this->cache->getStats();
    }

    /**
     * Flushes all cache entries.
     *
     * @return boolean TRUE if the cache entries were successfully flushed, FALSE otherwise.
     */
    public function flushAll()
    {
        return $this->cache->flushAll();
    }

    /**
     * Deletes all cache entries.
     *
     * @return boolean TRUE if the cache entries were successfully deleted, FALSE otherwise.
     */
    public function deleteAll()
    {
        return $this->cache->deleteAll();
    }


	public function getCacheId()
	{
	    return $this->cacheId;
	}

	public function setCacheId($cacheId)
	{
	    $this->cacheId = $cacheId;
	}
}