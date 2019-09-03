<?php

namespace W3com\HulkBundle\Tests\Util;

use PHPUnit\Framework\TestCase;
use W3com\HulkBundle\Model\Column;
use W3com\HulkBundle\Model\Display;
use W3com\HulkBundle\Model\Filter;
use W3com\HulkBundle\Util\Indexor;

class IndexorTest extends TestCase
{
    public function testIndexBetweenColAndFilter()
    {
        $dataTable = new Display();
        for ($i = 0; $i < 5; $i++){
            $col = new Column();
            $col->setActive('Y');
            $col->setFieldName('field'.$i);
            $dataTable->addColumn($col);
            $filter = new Filter();
            $filter->setFieldName('field'.$i);
            $filter->setActive('Y');
            $dataTable->addFilter($filter);
        }
        $indexor = new Indexor();
        $newDt = $indexor->addIndex($dataTable);
        $this->assertEquals($newDt->getFilterByFieldName('field1')->getIndex(),
            $newDt->getColumnByFieldName('field1')->getIndex());
    }
}