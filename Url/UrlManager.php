<?php

namespace W3com\HulkBundle\Url;

use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use W3com\HulkBundle\Model\Column;
use W3com\HulkBundle\Model\DataTable;

class UrlManager
{
    const DISPLAY_LINK_NAME = 'displayLink';

    const GLOBAL_LINK_NAME = 'link';

    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    public function generateLink(DataTable $dataTable, $data)
    {
        $newData = [];
        foreach ($data as $lines) {

            /** @var Column $column */
            foreach ($dataTable->getColumns() as $column) {

                if ($column->getCellAction() !== null &&
                    ($column->getCellAction()->getFunctionName() === self::DISPLAY_LINK_NAME ||
                        $column->getCellAction()->getFunctionName() === self::GLOBAL_LINK_NAME)) {

                    $urlParams = [];

                    foreach ($column->getCellAction()->getParams() as $fieldName => $targetFieldName) {

                        foreach ($lines as $nameField => $valueField) {

                            if ($nameField == $fieldName) {
                                $urlParams[$targetFieldName] = $valueField;
                            }

                        }

                        if (!isset($urlParams[$targetFieldName])){
                            $urlParams[$fieldName] = $targetFieldName;
                        }

                    }

                    if ($column->getCellAction()->getFunctionName() === self::DISPLAY_LINK_NAME) {

                        $urlParams['filename'] = $column->getCellAction()->getTargetEntity();
                        $url = $this->router->generate('w3com_display',
                            $urlParams);

                    } else {

                        try {
                            $url = $this->router->generate($column->getCellAction()->getTargetEntity(),
                                $urlParams);
                        } catch (MissingMandatoryParametersException $e) {
                            $dataTable->getError()->addUrlError($column->getFieldName(), $e->getMessage());
                        } catch (InvalidParameterException $e) {
                            $dataTable->getError()->addUrlError($column->getFieldName(), $e->getMessage());
                        } catch (\Exception $e) {
                            $dataTable->getError()->addUrlError($column->getFieldName(), $e->getMessage());
                        }

                    }
                    if (isset($url)) {
                        $lines[$column->getCellAction()->getFunctionName() . $column->getCellAction()->getTargetEntity()]
                            = $url;
                    }
                }
            }
            $newData[] = $lines;
        }
        $dataTable->setData($newData);
    }
}