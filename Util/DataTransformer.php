<?php

namespace W3com\HulkBundle\Util;

use Doctrine\Common\Annotations\AnnotationException;
use ReflectionException;
use W3com\BoomBundle\HanaEntity\AbstractEntity;
use W3com\HulkBundle\Model\Display;
use W3com\HulkBundle\Url\UrlManager;

class DataTransformer
{
    /** @var UrlManager */
    private $urlManager;

    public function __construct(UrlManager $manager)
    {
        $this->urlManager = $manager;
    }

    /**
     * @param Display $dataTable
     * @param $data
     * @return Display
     * @throws \Exception
     */
    public function addData(Display $dataTable, $data)
    {
        $dataTable->setData($this->transformData($data));
        $this->urlManager->generateLinks($dataTable, $dataTable->getData());
        return $dataTable;
    }

    /**
     * @param array $hanaEntities
     * @return array
     * @throws AnnotationException
     * @throws ReflectionException
     */
    private function transformData(array $hanaEntities)
    {
        $data = [];
        /** @var AbstractEntity $boomObj */
        foreach ($hanaEntities as $boomObj) {
            // Cast entity
            $entityArray = json_decode($boomObj->getEntityJson(), true);
            foreach ($entityArray as $property => $value) {
                $value = $this->transformDateFormat($value);
                $entityArray[$property] = $value;
            }
            $data[] = $entityArray;
        }
        return $data;
    }

    public static function transformDateFormat($value)
    {
        $dateTime = \DateTime::createFromFormat('Y-m-d H:i:s',
            str_replace('T', ' ', $value));

        $date = \DateTime::createFromFormat('Y-m-d', $value);

        if (false === $dateTime && false === $date) {
            return $value;
        } else {
            return $dateTime ? $dateTime->format('d/m/Y H:i:s') : $date->format('d/m/Y');
        }
    }

    public static function reverseDateFormat($value)
    {
        $date = \DateTime::createFromFormat('d/m/Y', $value);
        if ($date !== false) {
            return $date->format('Y-m-d');
        }
        return $value;
    }
}