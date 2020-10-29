<?php

namespace W3com\HulkBundle\Twig;

use Throwable;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SearchViewJsExtension extends AbstractExtension
{
    private $template;

    public function __construct(Environment $template)
    {
        $this->template = $template;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('search_view_js_render', [$this, 'render'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param $params
     *
     * @throws Throwable
     *
     * @return string
     */
    public function render($params)
    {
        $searchView = $params['searchView'];

        return $this->template->render('@W3comHulk/search_view/search_view.js.twig', [
            'searchView' => $searchView,
        ]);
    }
}
