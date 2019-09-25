<?php

namespace W3com\HulkBundle\Menu;


use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
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
        // RootName                 Item
        $menuSession = $this->menuSession->getHulkMenu($options['displayName'], $options['menuName']);
        $menu = $this->factory->createItem('root');

        foreach ($menuSession as $menuName => $menuItems) {

            if ($options['menuName'] === $menuName) {

                foreach ($menuItems as $menuItem) {

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
                            case 'index':
                                $index = $value;
                                break;
                        }
                    }

                    $displayName = !isset($displayName) ? $menuItem['uniqId'] : $displayName;

                    if (count($parameters['routeParameters']) > 1) {

                        $label = $displayName;

                        foreach ($parameters['routeParameters'] as $key => $parameter) {

                            if ($key !== 'filename') {
                                $label .= ' (' . $parameter . ')';
                            }
                        }
                    }
                    $menu->addChild($displayName, ['route' => $parameters['route'],
                        'routeParameters' => $parameters['routeParameters']])->setExtra('index', $index)
                        ->setLabel(isset($label) ? $label : $displayName);

                }
            }
        }
        $this->setLastChild($menu, $options['displayName']);
        return $menu;
    }

    private function setLastChild(ItemInterface $menu, $currentDisplayName, $currentIndex = null)
    {
        foreach ($menu->getChildren() as $child) {

            if ($currentIndex === null && $child->getName() === $currentDisplayName) {
                $this->setLastChild($menu, $currentDisplayName, $child->getExtra('index'));
            }
            if ($currentIndex - $child->getExtra('index') === 1) {
                return $menu->setExtra('lastChild', $child);
            }
        }
    }

}