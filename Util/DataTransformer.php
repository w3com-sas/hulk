<?php

namespace W3com\HulkBundle\Util;

use W3com\BoomBundle\Generator\Model\Property;
use W3com\BoomBundle\HanaEntity\AbstractEntity;
use W3com\HulkBundle\Finder\ModelFinder;
use W3com\HulkBundle\Model\Display;
use W3com\HulkBundle\Url\UrlManager;

class DataTransformer
{
    private $modelFinder;

    private $urlManager;

    public function __construct(ModelFinder $finder, UrlManager $manager = null)
    {
        $this->modelFinder = $finder;
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

        $formatedData = $this->adaptKeyWithProperties($data, $dataTable);
        $dataTable->setData($formatedData);

        if ($this->urlManager !== null){
            $this->urlManager->generateLink($dataTable, $dataTable->getData());
        }

        return $dataTable;
    }

    /**
     * @param $data
     * @param Display $dataTable
     * @return array
     * @throws \Exception
     */
    private function adaptKeyWithProperties($data, Display $dataTable)
    {

        // Boom return all fields of object, even if their selects

        $requiredFields = $this->modelFinder->getAvailableProperties($dataTable);

        $newData = [];
        /** @var AbstractEntity $boomObj */
        foreach ($data as $boomObj) {
            $data = [];

            // Cast entity
            foreach ((array)$boomObj as $property => $value) {

                // Cast add /00* (Because entity have protected property)
                // Need to remove it
                $realProperty = substr($property, 3);

                /**
                 * @var string $field
                 * @var Property $requiredProperty
                 */
                foreach ($requiredFields as $field => $requiredProperty) {

                    // Match with json Required property
                    if ($realProperty == $requiredProperty->getName()) {

                        $value = $this->checkDataFormat($value);
                        $data[$field] = $value;
                    }
                }
            }
            $newData[] = $data;
        }
        return $newData;
    }


    private function checkDataFormat($value)
    {
        $dateTime = \DateTime::createFromFormat('Y-m-d H:i:s',
            str_replace('T', ' ', $value));

        $date = \DateTime::createFromFormat('Y-m-d', $value);

        if (false === $dateTime && false === $date){
            return $value;
        } else {
            return $dateTime ? $dateTime->format('d/m/Y H:i:s') : $date->format('d/m/Y');
        }
    }
}