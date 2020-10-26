<?php

namespace W3com\HulkBundle\Twig;

use Throwable;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DisplayFilterHtmlExtension extends AbstractExtension
{
    private $template;

    public function __construct(Environment $template)
    {
        $this->template = $template;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('display_filter_html_render', [$this, 'render'], ['is_safe' => ['html']]),
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
        $form = $params['form'];

        return $this->template->render('@W3comHulk/display/display_form_filter.html.twig', [
            'display' => $table, 'form' => $form,
        ]);
    }
}
