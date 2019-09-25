<?php

namespace W3com\HulkBundle\Url;

use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use W3com\HulkBundle\Model\Column;
use W3com\HulkBundle\Model\Display;
use W3com\HulkBundle\Util\DataTransformer;

class UrlManager
{
    const DISPLAY_LINK_NAME = 'displayLink';

    const GLOBAL_LINK_NAME = 'link';

    const KEY_WORD_TODAY = 'today';

    const INTERVAL_URL_KEY = 'interval_';

    private $router;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(UrlGeneratorInterface $router, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->router = $router;
    }

    public function generateLink(Display $dataTable, $data)
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

                        if ($targetFieldName === self::KEY_WORD_TODAY) {
                            $urlParams[$fieldName] = date('Y-m-d');
                        }

                        foreach ($lines as $nameField => $valueField) {
                            if ($nameField == $fieldName && $valueField != null) {
                                $urlParams[$targetFieldName] = $valueField;
                            }
                        }

                        if (!isset($urlParams[$targetFieldName]) && !isset($urlParams[$fieldName])) {
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
                        } catch (\Exception $e) {
                            $dataTable->getError()->addUrlError($column->getFieldName(), $e->getMessage());
                            $url = null;
                        }
                    }
                    $lines[$column->getCellAction()->getFunctionName() . $column->getCellAction()->getTargetEntity()]
                        = $url;
                }
            }
            $newData[] = $lines;
        }
        $dataTable->setData($newData);
    }

    public function createRouteParams(array $formData, Display $dataTable)
    {

        $routeParams = [];
        foreach ($formData['display_filter'] as $field => $value) {

            if ($value != null && substr($field, 0, 9) !== '_interval' &&
                $field !== 'submit' && $field !== '_token') {
                $value = $this->reverseDateFormat($value);
                $routeParams[$field] = $value;
            }

            if (substr($field, 0, 9) === '_interval') {

                if ($value['min'] != "" || $value['max'] != "") {
                    $fieldName = substr($field, 9);
                    $min = $this->reverseDateFormat(array_values($value)[0]);
                    $max = $this->reverseDateFormat(array_values($value)[1]);
                    $routeParams[self::INTERVAL_URL_KEY . $fieldName] = $min . '|' .
                        $max;
                }

            }
        }

        $routeParams['filename'] = $dataTable->getLabel();
        return $routeParams;
    }

    private function reverseDateFormat($value)
    {
        $date = \DateTime::createFromFormat('d/m/Y', $value);
        if ($date !== false) {
            return $date->format('Y-m-d');
        }
        return $value;
    }
}