<?php
namespace W3com\HulkBundle\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use W3com\HulkBundle\Service\DisplayProvider;

class DisplayDataCollector extends DataCollector
{

    /** @var DisplayProvider  */
    protected $displayManager;

    public function __construct(DisplayProvider $displayManager)
    {
        $this->displayManager = $displayManager;
    }

    /**
     * Collects data for the given Request and Response.
     *
     * @param Request $request
     * @param Response $response
     */
    public function collect(Request $request, Response $response)
    {
        $this->data = $this->displayManager->getCollectedData();
    }

    /**
     * Returns the name of the collector.
     *
     * @return string The collector name
     */
    public function getName()
    {
        return 'hulk.data_collector';
    }

    public function reset()
    {
        $this->data = [];
    }
}