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

    public function getHulkMenu()
    {
        if (!$this->session->has('menu')) {
            $menu = [];
            $currentMenu = $this->getCurrentMenuItem(true);
            $menu[$currentMenu['uniqId']] = $currentMenu;
            $this->session->set('menu', $menu);
            return $menu;
        }
        $menu = $this->session->get('menu');
        $currentMenu = $this->getCurrentMenuItem(false, $menu);
        return $this->manageMenu($currentMenu);
    }

    public function getCurrentMenuItem($isFirst = false, $menu = [])
    {
        $currentMenuItem = [];
        $currentMenuItem['route'] = $this->request->getCurrentRequest()->get('_route');
        $currentMenuItem['routeParameters'] = $this->getRouteParams();
        $currentMenuItem['uniqId'] = $currentMenuItem['route'] . implode('_', $currentMenuItem['routeParameters']);

        if (array_key_exists($currentMenuItem['uniqId'], $menu)) {
            return $menu[$currentMenuItem['uniqId']];
        }

        $currentMenuItem['index'] = $isFirst ? 1 : $this->getLastItemIndex() + 1;
        return $currentMenuItem;
    }

    private function manageMenu(array $currentMenu)
    {
        $newMenu = [];
        foreach ($this->session->get('menu') as $menu) {

            if ($menu['index'] < $currentMenu['index']) {

                $newMenu[$menu['uniqId']] = $menu;
            }
        }
        $newMenu[$currentMenu['uniqId']] = $currentMenu;
        $this->session->set('menu', $newMenu);
        return $newMenu;
    }

    private function getLastItemIndex()
    {
        return max(array_column($this->session->get('menu'), 'index'));
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
                $params[$key] = $value;
            }
        }
        return $params;
    }
}