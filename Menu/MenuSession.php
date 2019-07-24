<?php

namespace W3com\HulkBundle\Menu;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class MenuSession
{
    private $request;

    private $session;

    public function __construct(RequestStack $request, SessionInterface $session)
    {
        $this->session = $session;
        $this->request = $request;
    }

    public function getHulkMenu($currentDisplayName, $currentMenuName)
    {
       // $this->session->remove('menu');
        if (!$this->session->has('menu')) {
            $menus = [];
            $menus[$currentMenuName] = [];
            $currentMenu = $this->getCurrentMenuItem($currentDisplayName, true, [], $currentMenuName);
            $menus[$currentMenuName][$currentMenu['uniqId']] = $currentMenu;
            $this->session->set('menu', $menus);
            return $menus;
        }
        $menus = $this->session->get('menu');
        $currentMenu = $this->getCurrentMenuItem($currentDisplayName, false, $menus, $currentMenuName);
        return $this->manageMenu($currentMenu, $currentMenuName);
    }

    public function getCurrentMenuItem($currentDisplayName, $isFirst = false, $menus = [], $currentMenuName = '')
    {
        $currentMenuItem = [];
        $currentMenuItem['route'] = null !== $this->request->getCurrentRequest()->get('_route') ?
            $this->request->getCurrentRequest()->get('_route') : $this->request->getMasterRequest()->attributes->get('_route');
        $currentMenuItem['routeParameters'] = $this->getRouteParams();
        $currentMenuItem['uniqId'] = $currentMenuItem['route'] . implode('_', $currentMenuItem['routeParameters']);
        $currentMenuItem['displayName'] = $currentDisplayName;

        foreach ($menus as $menu) {
            if (array_key_exists($currentMenuItem['uniqId'], $menu)) {
                return $menu[$currentMenuItem['uniqId']];
            }
        }

        $currentMenuItem['index'] = $isFirst ? 1 : $this->getLastItemIndex($currentMenuName) + 1;
        return $currentMenuItem;
    }

    private function manageMenu(array $currentMenu, $currentMenuName)
    {
        $newMenu = [];
        $newMenu[$currentMenuName] = [];
        foreach ($this->session->get('menu') as $menuName => $menus) {


            foreach ($menus as $menu) {

                if ($menuName === $currentMenuName) {
                    // Remove useless item
                    if ($menu['index'] < $currentMenu['index']) {
                        $newMenu[$currentMenuName][$menu['uniqId']] = $menu;
                    }
                } else {
                    $newMenu[$currentMenuName][$menu['uniqId']] = $menu;
                }
            }
        }
        $newMenu[$currentMenuName][$currentMenu['uniqId']] = $currentMenu;
        $this->session->set('menu', $newMenu);
        return $newMenu;
    }

    private function getLastItemIndex($currentMenuName)
    {
        return max(array_column($this->session->get('menu')[$currentMenuName], 'index'));
    }

    private function getRouteParams()
    {
        $params = [];

        if (!empty($this->request->getCurrentRequest()->get('_route_params'))) {
            foreach ($this->request->getCurrentRequest()->get('_route_params') as $key => $value) {
                $params[$key] = $value;
            }
        }

        if (!empty($this->request->getCurrentRequest()->query->all())) {
            foreach ($this->request->getCurrentRequest()->query->all() as $key => $value) {

                if ($key !== '_path') {
                    $params[$key] = $value;
                }
            }
        }
        return $params;
    }
}