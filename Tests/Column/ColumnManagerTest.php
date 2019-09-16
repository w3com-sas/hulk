<?php

namespace W3com\HulkBundle\Tests;

use PHPUnit\Framework\TestCase;
use W3com\BoomBundle\HanaEntity\AbstractEntity;
use W3com\HulkBundle\Column\ColumnManager;
use W3com\HulkBundle\Model\Column;
use W3com\HulkBundle\Model\Display;
use W3com\HulkBundle\Model\GlobalAction;


class ColumnManagerTest extends TestCase
{
    public function testReturnActiveColumnWhenRelatedDataExist()
    {
        $columnManager = new ColumnManager();
        $display = new Display();
        $col = new Column();
        $data = [];
        $boomObj = new AbstractEntity();
        // Need to add 3 characters because cast boom obj with protected properties add 3 characs,
        // but not with the set method..
        $boomObj->set('field', 'value');
        $data[] = $boomObj;
        $col->setFieldName('field');
        $display->addColumn($col);
        $display->setData($data);
        $newDisplay = $columnManager->initColumns($display);
        $this->assertEquals('Y', $newDisplay->getColumns()[0]->getActive());
    }

    public function testReturnUnActiveColumnWhenRelatedDataDoesNotExist()
    {
        $columnManager = new ColumnManager();
        $dataTable = new Display();
        $col = new Column();
        $data = [];
        $boomObj = new AbstractEntity();
        // Need to add 3 characters because cast boom obj with protected properties add 3 characs,
        // but not with the set method..
        $boomObj->set('azefield', 'value');
        $data[] = $boomObj;
        $col->setFieldName('unknowField');
        $dataTable->addColumn($col);
        $data[] = ['azefield' => 'value'];
        $dataTable->setData($data);
        $newDataTable = $columnManager->initColumns($dataTable);
        $this->assertEquals('N', $newDataTable->getColumns()[0]->getActive());
    }

    public function testReturnCheckBoxColWhenGlobalActionExist()
    {
        $columnManager = new ColumnManager();
        $dataTable = new Display();
        $data = [];
        $dataTable->addGlobalAction(new GlobalAction());
        $data[] = ['field' => 'value'];
        $dataTable->setData($data);
        $newDataDatable = $columnManager->initColumns($dataTable);
        $this->assertEquals(Column::COL_TYPE_CHECKBOX, $newDataDatable->getColumns()[0]->getType());
    }

}