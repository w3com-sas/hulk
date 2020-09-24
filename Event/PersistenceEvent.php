<?php
namespace W3com\HulkBundle\Event;


use Symfony\Contracts\EventDispatcher\Event;
use W3com\BoomBundle\HanaEntity\AbstractEntity;

class PersistenceEvent extends Event
{
    public const NAME = 'display_persistence';
    public const TYPE_PRE_UPDATE = 'display_pre_update';

    /** @var AbstractEntity */
    protected $object;

    /** @var string */
    protected $type;

    /**
     * @param object $object
     * @param string $type
     */
    public function __construct($object, $type)
    {
        $this->object = $object;
        $this->type = $type;
    }

    /**
     * @return object
     */
    public function getObject()
    {
        return $this->object;
    }

}
