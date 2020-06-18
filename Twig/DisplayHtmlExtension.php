<?php

namespace W3com\HulkBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\Environment;

class DisplayHtmlExtension extends AbstractExtension
{
    private $template;

    public function __construct(Environment $template)
    {
        $this->template = $template;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('display_html_render', [$this, 'render'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param $params
     * @return string
     * @throws \Throwable
     */
    public function render($params)
    {
        $table = $params['display'];
        return $this->template->render('@W3comHulk/display/display.html.twig', [
            'table' => $table
        ]);
    }
}