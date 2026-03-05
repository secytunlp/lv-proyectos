<?php

/**
 * Implementación de caché utilizando APC.
 * 
 * @author bernardo
 * @since 27-11-2013
 */
class CdtApcCache implements ICdtCache{
	
	/**
     * {@inheritdoc}
     */
    public function fetch($id)
    {
        return apc_fetch($id);
    }

    /**
     * {@inheritdoc}
     */
    public function contains($id)
    {
        return apc_exists($id);
    }

    /**
     * {@inheritdoc}
     */
    public function save($id, $data, $lifeTime = 0)
    {
        return (bool) apc_store($id, $data, (int) $lifeTime);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        return apc_delete($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getStats()
    {
        $info = apc_cache_info();
        $sma  = apc_sma_info();

        return array(
            "hits"             => $info['num_hits'],
            "misses"           => $info['num_misses'],
            "uptime"           => $info['start_time'],
            "memory_usage"     => $info['mem_size'],
            "memory_available" => $sma['avail_mem'],
        );
    }

    /**
     * Flushes all cache entries.
     *
     * @return boolean TRUE if the cache entries were successfully flushed, FALSE otherwise.
     */
    public function flushAll()
    {
        return apc_clear_cache() && apc_clear_cache('user');
    }

    /**
     * Deletes all cache entries.
     *
     * @return boolean TRUE if the cache entries were successfully deleted, FALSE otherwise.
     */
    public function deleteAll()
    {
        return apc_clear_cache();
    }

}