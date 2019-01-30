<?php

namespace W3com\HulkBundle\Service;

use W3com\HulkBundle\Column\ColumnManager;
use W3com\HulkBundle\Filter\FilterManager;
use W3com\HulkBundle\Finder\JsonFinder;
use W3com\HulkBundle\Finder\ModelFinder;
use W3com\HulkBundle\Model\AbstractDataTable;
use W3com\HulkBundle\Model\DataTable;
use W3com\HulkBundle\Query\QueryManager;
use W3com\BoomBundle\Generator\Model\Property;
use W3com\BoomBundle\HanaEntity\AbstractEntity;
use W3com\BoomBundle\Service\BoomManager;
use W3com\HulkBundle\Util\Indexor;

class TableProvider
{

    /**
     * @var QueryManager
     */
    private $queryManager;

    /**
     * @var FilterManager
     */
    private $filterManager;

    /**
     * @var ColumnManager
     */
    private $columnManager;

    /**
     * @var ModelFinder
     */
    private $modelFinder;

    /**
     * @var DataTable
     */
    private $dataTable;

    /**
     * @var array
     */
    private $config;

    /**
     * @var JsonFinder
     */
    private $jsonFinder;

    /**
     * @var Indexor
     */
    private $indexor;

    /**
     * TableProvider constructor.
     * @param BoomManager $boom
     * @param $config
     */
    public function __construct($config, BoomManager $boom)
    {
        $this->config = $config;
        $this->indexor = new Indexor();
        $this->filterManager = new FilterManager();
        $this->dataTable = new DataTable();
        $this->columnManager = new ColumnManager();
        $this->modelFinder = new ModelFinder($boom);
        $this->queryManager = new QueryManager($this->modelFinder, $boom);
        $this->jsonFinder = new JsonFinder($boom, $config);
    }

    /**
     * @param $filename
     * @return AbstractDataTable
     * @throws \Exception
     */
    public function getDataTable($filename)
    {
        $json = $this->jsonFinder->getOnlineJson($filename, $this->dataTable);

        $this->hydrateDataTable($json);

        // Find concerned data
        $data = $this->queryManager->createDataTableQuery($this->dataTable);


        // Retrieve data
        // Adapt Columns/Orders with Data (Need data)
        // If dataTable can't find project entity
        if ($this->dataTable->isClassExist()) {


            $this->dataTable->setData($this->retrieveData($data));

            $this->columnManager->initColumns($this->dataTable, $data);

            $this->filterManager->initFilters($this->dataTable, $data);

            $this->indexor->addIndex($this->dataTable);
            //$this->dataTable->setNonexistentProperties($this->dataTable);

        }
        dump($this->dataTable);
        return $this->dataTable;
    }

    private function hydrateDataTable($file)
    {

        if ($this->dataTable->isFileExist()){
            foreach (json_decode($file) as $key => $value) {

                switch ($key) {
                    case 'DisplayName':
                        $this->dataTable->setDisplayName($value);
                        break;
                    case 'CalculationView':
                        $this->dataTable->setCalcView($value);
                        break;
                    case 'Columns':
                        $this->dataTable->setColumns($value);
                        break;
                    case 'filters':
                        $this->dataTable->setFilters($value);
                        break;
                }
            }
        }
        return $this->dataTable;
    }

    /**
     * @param $data
     * @return array
     */
    private function retrieveData($data)
    {
        // Boom return all fields of object, even if their selects

        $requiredFields = $this->modelFinder->getAvailableProperties($this->dataTable);

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
                        $data[$field] = $value;
                    }
                }
            }
            $newData[] = $data;
        }
        return $newData;
    }
}