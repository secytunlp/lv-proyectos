<?php

/**
 * Implementación de caché utilizando APC.
 * 
 * @author bernardo
 * @since 27-11-2013
 */
class CdtMockCache implements ICdtCache{
	
	/**
     * {@inheritdoc}
     */
    public function fetch($id)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function contains($id)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function save($id, $data, $lifeTime = 0)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getStats()
    {
        

        return array(
            "hits"             => 0,
            "misses"           => 0,
            "uptime"           => 0,
            "memory_usage"     => 0,
            "memory_available" => 0,
        );
    }

    /**
     * Flushes all cache entries.
     *
     * @return boolean TRUE if the cache entries were successfully flushed, FALSE otherwise.
     */
    public function flushAll()
    {
        return true;
    }

    /**
     * Deletes all cache entries.
     *
     * @return boolean TRUE if the cache entries were successfully deleted, FALSE otherwise.
     */
    public function deleteAll()
    {
        return true;
    }

}