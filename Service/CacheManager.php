<?php

namespace W3com\HulkBundle\Service;

use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\PhpArrayAdapter;

class CacheManager
{
    const DISPLAY_CACHE_DIRECTORY = '../var/cache/hulk/';
    const DISPLAY_CACHE_KEY = 'displays';

    /**
     * @var PhpArrayAdapter 
     */
    private $cache;

    public function __construct()
    {
        $this->cache = new PhpArrayAdapter(
            self::DISPLAY_CACHE_DIRECTORY.self::DISPLAY_CACHE_KEY.'.cache',
            new FilesystemAdapter()
        );
    }

    /**
     * @throws InvalidArgumentException
     */
    public function isInCache(string $displayName): bool
    {
        $cacheItem = $this->cache->getItem(self::DISPLAY_CACHE_KEY);
        $displays = $cacheItem->get();

        return array_key_exists($displayName, $displays ?? []);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getCacheItem(string $key)
    {
        return $this->cache->getItem($key);
    }
}