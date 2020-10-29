<?php

namespace W3com\HulkBundle\Util;

use DateTime;
use Doctrine\Common\Annotations\AnnotationException;
use Exception;
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
     * @param $data
     *
     * @throws Exception
     *
     * @return Display
     */
    public function addData(Display $dataTable, $data)
    {
        $dataTable->setData($this->transformData($data));
        $this->urlManager->generateLinks($dataTable, $dataTable->getData());

        return $dataTable;
    }

    public static function transformDateFormat($value)
    {
        $dateTime = DateTime::createFromFormat('Y-m-d H:i:s',
            str_replace('T', ' ', $value));

        $date = DateTime::createFromFormat('Y-m-d', $value);

        if (false === $dateTime && false === $date) {
            return $value;
        }

        return $dateTime ? $dateTime->format('d/m/Y H:i:s') : $date->format('d/m/Y');
    }

    public static function reverseDateFormat($value)
    {
        $date = DateTime::createFromFormat('d/m/Y', $value);
        if (false !== $date) {
            return $date->format('Y-m-d');
        }

        return $value;
    }

    /**
     * @throws AnnotationException
     * @throws ReflectionException
     *
     * @return array
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
}
