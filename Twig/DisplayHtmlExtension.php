<?php

namespace W3com\HulkBundle\Twig;

use Throwable;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

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
     *
     * @throws Throwable
     *
     * @return string
     */
    public function render($params)
    {
        $table = $params['display'];

        return $this->template->render('@W3comHulk/display/display.html.twig', [
            'table' => $table,
        ]);
    }
}
