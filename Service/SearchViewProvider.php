<?php

namespace W3com\HulkBundle\Service;

use Doctrine\Common\Annotations\AnnotationException;
use W3com\BoomBundle\HanaEntity\AbstractEntity;
use W3com\BoomBundle\Parameters\Parameters;
use W3com\BoomBundle\Service\BoomManager;

class SearchViewProvider
{
    private $boom;

    public function __construct(BoomManager $boom)
    {
        $this->boom = $boom;
    }

    /**
     * @param $params
     * @return array
     * @throws AnnotationException
     * @throws \ReflectionException
     */
    public function getBoomResults($params)
    {
        $repo = $this->boom->getRepository($params['entity']);

        if (array_key_exists('filters', $params) && !empty($params['filters'])){
            $rawFilter = ' and (';
        } else {
            $rawFilter = '(';
        }

        $columns = array_key_exists('columns', $params) ? $params['columns'] : null;
        $search = array_key_exists('search', $params) ? $params['columns'] : null;
        $top = array_key_exists('top', $params) ? $params['top'] : 100;
        $filters = array_key_exists('filters', $params) ? $params['filters'] : [];


        $rawFilter = $this->createRawFilter($columns, $search, $rawFilter);

        $params = $repo->createParams()->setTop($top)->addRawFilter($rawFilter);

        $params = $this->addFilters($filters, $params);

        $results = $repo->findAll($params);

        $data = $this->retrieveResults($results, $columns);

        return $data;
    }

    private function createRawFilter($columns, $search, $rawFilter = '')
    {
        foreach ($columns as $col => $displayName) {

            if (end($columns) !== $displayName) {
                $rawFilter .= 'substringof(\'' . strtolower($search) . '\' , ' . $col . ') or ';
                $rawFilter .= 'substringof(\'' . strtoupper($search) . '\' , ' . $col . ') or ';
            } else {
                $rawFilter .= 'substringof(\'' . strtolower($search) . '\' , ' . $col . ') or ';
                $rawFilter .= 'substringof(\'' . strtoupper($search) . '\' , ' . $col . ')';
            }
        }
        return $rawFilter.')';
    }

    /**
     * @param $results
     * @param $requestedColumns
     * @return array
     * @throws AnnotationException
     * @throws \ReflectionException
     */
    private function retrieveResults($results, $requestedColumns)
    {
        $data = [];
        /** @var AbstractEntity $result */
        foreach ($results as $result) {
            $data[] = \GuzzleHttp\json_decode($result->getEntityJson(), true);
        }


        $newData = [];
        foreach ($data as $line) {
            $newData[] = array_intersect_key($line, $requestedColumns);
        }

        return $newData;
    }

    private function addFilters(array $filters, Parameters $params)
    {
        foreach ($filters as $field => $value){
            $params = $params->addFilter($field, $value);
        }
        return $params;
    }


}