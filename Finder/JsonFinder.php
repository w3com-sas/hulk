<?php

namespace W3com\HulkBundle\Finder;

use Exception;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\PhpArrayAdapter;
use W3com\BoomBundle\Service\BoomManager;
use W3com\HulkBundle\Model\Display;
use W3com\HulkBundle\Service\CacheManager;

class JsonFinder
{
    const DISPLAY_CACHE_DIRECTORY = '../var/cache/hulk/';
    const DISPLAY_CACHE_KEY = 'display';

    /**
     * @var BoomManager
     */
    private $boom;

    private $config;

    /**
     * @var mixed
     */
    private $baseUri;

    /**
     * @var mixed
     */
    private $jsonUri;

    /**
     * @var PhpArrayAdapter
     */
    private $cache;

    public function __construct(BoomManager $boom, $config)
    {
        $this->boom = $boom;
        $this->config = $config;
        $this->baseUri = $this->boom->config['odata_service']['connections']['default']['uri'];
        $this->jsonUri = $this->config['json_display']['url_files'];
        $this->cache = new PhpArrayAdapter(
            self::DISPLAY_CACHE_DIRECTORY.self::DISPLAY_CACHE_KEY.'.cache',
            new FilesystemAdapter()
        );
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getOnlineJson($filename, Display $display = null)
    {
        $cacheItem = $this->cache->getItem(CacheManager::DISPLAY_CACHE_KEY);
        $displays = $cacheItem->get() ?? [];
        $file = null;
        $context = $this->createContext();
        $display = null === $display ? new Display() : $display;
        $display->getError()->setFileExist(true);

        try {
            $file = file_get_contents($this->baseUri.$this->jsonUri.$filename.'.json', false, $context);
        } catch (Exception $e) {
            $display->getError()->setFileExist(false);
        }

        $displays[$filename] = $file;
        $this->cache->warmUp([CacheManager::DISPLAY_CACHE_KEY => $displays]);

        return $file;
    }

    private function createContext()
    {
        $login =
            $this->boom->config['odata_service']['connections']['default']['username']
            .':'.
            $this->boom->config['odata_service']['connections']['default']['password']
        ;
        $encodedLogin = base64_encode($login);
        $opts = [
            'http' => [
                'method' => 'GET',
                'header' => [
                    'Authorization: Basic '.$encodedLogin,
                ],
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ];

        return stream_context_create($opts);
    }
}
