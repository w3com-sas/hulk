<?php

namespace W3com\HulkBundle\Url;

use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use W3com\HulkBundle\Model\Column;
use W3com\HulkBundle\Model\Display;
use W3com\HulkBundle\Util\DataTransformer;

class UrlManager
{
    const KEY_WORD_TODAY = 'today';

    const INTERVAL_URL_KEY = 'interval_';

    /** @var UrlGeneratorInterface */
    private $router;

    /*** @var LoggerInterface */
    private $logger;

    public function __construct(UrlGeneratorInterface $router, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->router = $router;
    }

    public function generateLinks(Display $display, $data)
    {
        $dataTransform = [];
        foreach ($data as $line) {
            /** @var Column $column */
            foreach ($display->getColumnsWithLinks() as $column) {
                $urlParams = [];
                foreach ($line as $property => $value) {
                    if (array_key_exists($property, $column->getCellAction()->getParams())) {
                        $value = $this->transformKeyword($value);
                        $urlParams[$column->getCellAction()->getParams()[$property]] = $value;
                    }
                }

                $additionalParams = array_diff_key($column->getCellAction()->getParams(), $line);

                if (array_key_exists('Displays', $additionalParams)) {
                    unset($additionalParams['Displays']);
                }

                $urlParams = array_merge($urlParams, $additionalParams);
                $urlParams = $this->addFilenameParam($column, $urlParams);
                $url = count(array_filter($urlParams)) > 0 ? $this->generateLink($column, $urlParams, $display) : null;
                $line[$column->getCellAction()->getFunctionName() . $column->getCellAction()->getTargetEntity()] = $url;
            }
            $dataTransform[] = $line;
        }
        $display->setData($dataTransform);
    }

    public function createRouteParams(array $formData, Display $dataTable)
    {
        $routeParams = [];
        foreach ($formData['display'] as $field => $value) {
            if ($value != null && substr($field, 0, 9) !== '_interval' && $field !== 'submit' && $field !== '_token' && $field !== 'calcView') {
                $value = DataTransformer::reverseDateFormat($value);
                $routeParams[$field] = $value;
            }

            if (substr($field, 0, 9) === '_interval') {
                if ($value['min'] != "" || $value['max'] != "") {
                    $fieldName = substr($field, 9);
                    $min = DataTransformer::reverseDateFormat(array_values($value)[0]);
                    $max = DataTransformer::reverseDateFormat(array_values($value)[1]);
                    $routeParams[self::INTERVAL_URL_KEY . $fieldName] = $min . '|' . $max;
                }
            }
        }
        $routeParams['filename'] = $formData['display']['filename'];
        return $routeParams;
    }

    private function addFilenameParam(Column $column, $urlParams)
    {
        $functionName = $column->getCellAction()->getFunctionName();

        if ($functionName === Column::FUNCTION_NAME_DISPLAY_LINK) {
            $urlParams['filename'] = $column->getCellAction()->getTargetEntity();
        }

        if ($functionName === Column::FUNCTION_NAME_DISPLAY_LINKS) {
            $urlParams['filename'] = $column->getCellAction()->getParams()['Displays'][0];
        }

        return $urlParams;
    }

    private function generateLink(Column $column, array $urlParams, Display $display)
    {
        $routeName = in_array($column->getCellAction()->getFunctionName(), ['displayLink', 'displayLinks'])
            ? "w3com_display"
            : $column->getCellAction()->getTargetEntity();

        try {
            $url = $this->router->generate($routeName, array_filter($urlParams));
        } catch (\Exception $e) {
            $display->getError()->addUrlError($column->getFieldName(), $e->getMessage());
            $url = null;
        }
        return $url;
    }

    private function transformKeyword($value)
    {
        switch ($value) {
            case self::KEY_WORD_TODAY:
                return date('Y-m-d');
            default:
                return $value;
        }
    }
}