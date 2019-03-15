<?php

namespace W3com\HulkBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SearchViewJsExtension extends AbstractExtension
{

    private $template;

    public function __construct(\Twig_Environment $template)
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
     * @return string
     * @throws \Throwable
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function render($params)
    {
        $searchView = $params['searchView'];
        return $this->template->render('@W3comHulk/search_view/search_view.js.twig', [
            'searchView' => $searchView
        ]);
    }

}