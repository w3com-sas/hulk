<?php


namespace W3com\HulkBundle\Tests;

use PHPUnit\Framework\TestCase;
use W3com\BoomBundle\HanaEntity\AbstractEntity;
use W3com\HulkBundle\Filter\FilterManager;
use W3com\HulkBundle\Model\Display;
use W3com\HulkBundle\Model\Filter;

class FilterManagerTest extends TestCase
{
    public function testReturnHiddenColWhenThereIsFilterButNoCol()
    {
        $filterManager = new FilterManager();
        $data = [];
        for ($i = 0; $i > 5; $i++){
            $boomObj = new AbstractEntity();
            $boomObj->set('azefield', 'value'.$i);
            $data[] = $boomObj;
        }
        $dataTable = new Display();
        $filter = new Filter();
        $filter->setActive('Y');
        $filter->setFieldName('field');
        $filter->setType(Filter::TYPE_SINGLE);
        $dataTable->addFilter($filter);
        $dataTable->setData($data);
        $newDt = $filterManager->initFilters($dataTable);
        $this->assertEquals(true, $newDt->getColumns()[0]->isHidden());
    }

    public function testReturnGoodNumberOfValues()
    {
        $filterManager = new FilterManager();

        $data = [];

        for ($i = 0; $i < 5; $i++){
            $boomObj = new AbstractEntity();
            $boomObj->set('azefield', 'value'.$i);
            $data[] = $boomObj;
        }

        $dataTable = new Display();
        $filter = new Filter();
        $filter->setActive('Y');
        $filter->setFieldName('field');
        $filter->setType(Filter::TYPE_SINGLE);
        $dataTable->addFilter($filter);
        $dataTable->setData($data);
        $newDt = $filterManager->initFilters($dataTable);
        $this->assertEquals(6, count($newDt->getFilters()[0]->getValues()));
    }
}