<?php

namespace W3com\HulkBundle\Util;

use W3com\BoomBundle\Generator\Model\Property;
use W3com\BoomBundle\HanaEntity\AbstractEntity;
use W3com\HulkBundle\Finder\ModelFinder;
use W3com\HulkBundle\Model\DataTable;

class DataTransformer
{
    private $modelFinder;

    public function __construct(ModelFinder $finder)
    {
        $this->modelFinder = $finder;
    }

    /**
     * @param DataTable $dataTable
     * @param $data
     * @return DataTable
     * @throws \Exception
     */
    public function addData(DataTable $dataTable, $data)
    {
        $formatedData = $this->retrieveData($data, $dataTable);
        $dataTable->setData($formatedData);
        return $dataTable;
    }

    /**
     * @param $data
     * @param DataTable $dataTable
     * @return array
     * @throws \Exception
     */
    private function retrieveData($data, DataTable $dataTable)
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

    public function checkDataFormat($value)
    {
        $dateTime = \DateTime::createFromFormat('Y-m-d H:i:s',
            str_replace('T', ' ', $value));

        if (false === $dateTime){
            return $value;
        }
        $date = $dateTime->format('H:m d/m/Y');
        return str_replace(':', 'h', $date);
    }
}