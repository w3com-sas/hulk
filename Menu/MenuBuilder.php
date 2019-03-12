<?php

namespace W3com\HulkBundle\Menu;


use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class MenuBuilder
{
    use ContainerAwareTrait;

    private $factory;

    private $menuSession;

    public function __construct(FactoryInterface $factory, MenuSession $menuSession)
    {
        $this->menuSession = $menuSession;
        $this->factory = $factory;
    }

    public function createMainMenu(array $options)
    {


        $menuSession = $this->menuSession->getHulkMenu($options['displayName']);
        $menu = $this->factory->createItem('root');

        foreach ($menuSession as $menuName => $menuItem) {
            $parameters = [];
            foreach ($menuItem as $key => $value) {

                switch ($key) {
                    case 'route':
                        $parameters['route'] = $value;
                        break;
                    case 'routeParameters':
                        $parameters['routeParameters'] = $value;
                        break;
                    case 'displayName':
                        $displayName = $value;
                        break;
                }
            }
            if (!isset($displayName)){
                $displayName = $menuItem['uniqId'];
            }

            $menu->addChild($displayName, ['route' => $parameters['route'],
                'routeParameters' => $parameters['routeParameters']]);
        }
        return $menu;
    }



}