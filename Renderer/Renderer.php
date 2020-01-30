<?php

namespace W3com\HulkBundle\Renderer;

use W3com\HulkBundle\Model\Column;
use W3com\HulkBundle\Model\Display;
use W3com\HulkBundle\Model\RenderElement;

class Renderer
{

    public function buildTemplate(Display $display)
    {
        /** @var Column $column */
        foreach ($display->getColumns() as $column){
            $this->addColumnRenderElements($column, $column->getRenderElementOptions());
        }
    }

    private function addColumnRenderElements(Column $column, $renderElementOptions = [])
    {
        switch ($column->getType()){
            case Column::COL_TYPE_INPUT_NUMBER:
                $renderElement = new RenderElement($column,'input', array_merge(['type' => 'number'], $renderElementOptions));
                $column->setType(Column::COL_TYPE_RENDER_ELEMENT);
                $column->setRenderElement($renderElement);
                break;
            case Column::COL_TYPE_INPUT_TEXT:
                $renderElement = new RenderElement($column, 'input', array_merge(['type' => 'text'], $renderElementOptions));
                $column->setType(Column::COL_TYPE_RENDER_ELEMENT);
                $column->setRenderElement($renderElement);
                break;
        }
    }
}