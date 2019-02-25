<?php

namespace W3com\HulkBundle\Url;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use W3com\HulkBundle\Model\Column;
use W3com\HulkBundle\Model\DataTable;

class UrlManager
{
    const DISPLAY_LINK_NAME = 'displayLink';

    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    public function generateDisplayLink(DataTable $dataTable, $data)
    {
        /** @var Column $column */
        foreach ($dataTable->getColumns() as $column) {

            if ($column->getCellAction() !== null && $column->getCellAction()->getFunctionName() === self::DISPLAY_LINK_NAME) {


                foreach ($column->getCellAction()->getParams() as $fieldName => $targetFieldName) {

                    $newData = [];
                    $urlParams = [];
                    foreach ($data as $lines) {

                        foreach ($lines as $nameField => $valueField) {

                            if ($nameField == $fieldName) {
                                $urlParams[$targetFieldName] = $valueField;
                            }
                            $urlParams['filename'] = $column->getCellAction()->getTargetEntity();
                            $url = $this->router->generate('w3com_display',
                                $urlParams);
                            $lines[self::DISPLAY_LINK_NAME.$column->getCellAction()->getTargetEntity()] = $url;
                        }
                        $newData[] = $lines;
                    }
                    $dataTable->setData($newData);
                }

            }
        }
        return $dataTable;
    }
}