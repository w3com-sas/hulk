<?php

namespace W3com\HulkBundle\Controller;

use Exception;
use Psr\Cache\InvalidArgumentException;
use ReflectionException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\KernelInterface;
use W3com\BoomBundle\Generator\AppInspector;
use W3com\HulkBundle\Model\Display;
use W3com\HulkBundle\Service\CacheManager;
use W3com\HulkBundle\Service\DisplayProvider;

class DisplayController extends AbstractController
{
    /**
     * @var DisplayProvider
     */
    private $displayProvider;

    /**
     * @var RequestStack
     */
    private $request;

    public function __construct(DisplayProvider $provider, RequestStack $request)
    {
        $this->displayProvider = $provider;
        $this->request = $request;
    }

    /**
     * @throws InvalidArgumentException|ReflectionException
     */
    public function display($filename): Response
    {
        $display = $this->displayProvider->getDisplay(
            $filename,
            $this->request->getCurrentRequest()->query
        );

        return $this->render('@W3comHulk/display/all.html.twig', [
            'display' => $display,
            'filename' => $filename,
        ]);
    }

    /**
     * Clear Hulk and Boom caches via a route.
     * unlink() is used instead of the CacheManager or the hulk:clear command ...
     * ... because, for some odd reasons, clear the cache isn't doable in a controller.
     *
     * @throws Exception
     */
    public function cacheClear(): Response
    {
        // Allow to determinate the message display in the view.
        // 3 means : both cleaning failed.
        $commandResponse = 3;
        $hulkCacheFile = CacheManager::DISPLAY_CACHE_DIRECTORY.CacheManager::DISPLAY_CACHE_KEY.'.cache';
        $boomCacheFile = AppInspector::ENTITIES_CACHE_DIRECTORY.AppInspector::ENTITIES_CACHE_KEY.'.cache';

        if ($boomCacheFile) {
            if (file_exists($boomCacheFile)) {
                // 1 means : Boom cache has been cleared but not Hulk cache.
                if (unlink($boomCacheFile)) $commandResponse = 1;
            }
        }

        if (file_exists($hulkCacheFile)) {
            // 2 means : Hulk cache has been cleared but not Boom cache.
            if (unlink($hulkCacheFile)) $commandResponse = 2;
        }

        if (!file_exists($boomCacheFile) && !file_exists($hulkCacheFile)) {
            // 0 means : both cleaning succeeded.
            $commandResponse = 0;
        }

        return $this->render('@W3comHulk/display/cache_clear.html.twig', [
            'commandResponse' => $commandResponse,
            // base.html.twig needs the following parameter.
            'display' => new Display(),
        ]);
    }
}
