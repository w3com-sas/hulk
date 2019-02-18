<?php

namespace W3com\HulkBundle\Tests;

use PHPUnit\Framework\TestCase;
use W3com\BoomBundle\HanaEntity\AbstractEntity;
use W3com\HulkBundle\Column\ColumnManager;
use W3com\HulkBundle\Model\Column;
use W3com\HulkBundle\Model\DataTable;
use W3com\HulkBundle\Model\GlobalAction;


class ColumnManagerTest extends TestCase
{


    public function testReturnActiveColumnWhenRelatedDataExist()
    {
        $columnManager = new ColumnManager();
        $dataTable = new DataTable();
        $col = new Column();
        $data = [];
        $boomObj = new AbstractEntity();
        // Need to add 3 characters because boom obj have protected properties, and not with
        // set method..
        $boomObj->set('azefield', 'value');
        $data[] = $boomObj;
        $col->setFieldName('field');
        $dataTable->addColumn($col);
        $newDataTable = $columnManager->initColumns($dataTable, $data);
        $this->assertEquals('Y', $newDataTable->getColumns()[0]->getActive());
    }

    public function testReturnUnActiveColumnWhenRelatedDataDoesNotExist()
    {
        $columnManager = new ColumnManager();
        $dataTable = new DataTable();
        $col = new Column();
        $data = [];
        $boomObj = new AbstractEntity();
        // Need to add 3 characters because boom obj have protected properties, and not with
        // set method..
        $boomObj->set('azefield', 'value');
        $data[] = $boomObj;
        $col->setFieldName('unknowField');
        $dataTable->addColumn($col);
        $data[] = ['azefield' => 'value'];
        $newDataTable = $columnManager->initColumns($dataTable, $data);
        $this->assertEquals('N', $newDataTable->getColumns()[0]->getActive());
    }

    public function testReturnCheckBoxColWhenGlobalActionExist()
    {
        $columnManager = new ColumnManager();
        $dataTable = new DataTable();
        $data = [];
        $dataTable->addGlobalAction(new GlobalAction());
        $data[] = ['field' => 'value'];
        $newDataDatable = $columnManager->initColumns($dataTable, $data);
        $this->assertEquals(Column::COL_TYPE_CHECKBOX, $newDataDatable->getColumns()[0]->getType());
    }

}