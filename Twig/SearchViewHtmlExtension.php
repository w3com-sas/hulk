<?php

namespace W3com\HulkBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\Environment;
use Twig\TwigFunction;

class SearchViewHtmlExtension extends AbstractExtension
{
    private $template;

    public function __construct(Environment $template)
    {
        $this->template = $template;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('search_view_html_render', [$this, 'render'], ['is_safe' => ['html']]),
            ];
    }

    /**
     * @param $params
     * @return string
     * @throws \Throwable
     */
    public function render($params)
    {
        $searchView = $params['searchView'];
        return $this->template->render('@W3comHulk/search_view/search_view.html.twig', [
            'searchView' => $searchView
            ]);
    }
}