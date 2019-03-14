<?php

namespace W3com\HulkBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DisplayHtmlExtension extends AbstractExtension
{
    private $template;

    public function __construct(\Twig_Environment $template)
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
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function render($params)
    {
        $table = $params['display'];
        return $this->template->render('@W3comHulk/display/display.html.twig', [
            'table' => $table
        ]);
    }
}