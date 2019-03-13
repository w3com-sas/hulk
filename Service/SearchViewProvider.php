<?php

namespace W3com\HulkBundle\Service;

use W3com\BoomBundle\HanaEntity\AbstractEntity;
use W3com\BoomBundle\Service\BoomManager;

class SearchViewProvider
{
    private $boom;

    public function __construct(BoomManager $boom)
    {
        $this->boom = $boom;
    }

    /**
     * @param AbstractEntity $entity
     * @param array $columns
     * @param string $search
     * @param int $top
     * @return array
     * @throws \Exception
     */
    public function getBoomResults($entity, array $columns, string $search = null, int $top = 100)
    {
        $repo = $this->boom->getRepository($entity);
        $params = $repo->createParams()->setTop($top);

        $rawFilter = $this->createRawFilter($columns, $search);
        $params->addRawFilter($rawFilter);

        $results = $repo->findAll($params);
        $data = $this->retrieveResults($results, $columns);
        return $data;
    }

    private function createRawFilter($columns, $search, $rawFilter = '')
    {
        foreach ($columns as $col => $displayName){

            if (end($columns) !== $displayName){
                $rawFilter.='substringof(\''.strtolower($search).'\' , '.$col.') or ';
                $rawFilter.='substringof(\''.strtoupper($search).'\' , '.$col.') or ';
            } else {
                $rawFilter.='substringof(\''.strtolower($search).'\' , '.$col.') or ';
                $rawFilter.='substringof(\''.strtoupper($search).'\' , '.$col.')';
            }
        }
        return $rawFilter;
    }

    /**
     * @param $results
     * @param $requestedColumns
     * @return array
     * @throws \Doctrine\Common\Annotations\AnnotationException
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


}