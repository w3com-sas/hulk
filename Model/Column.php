<?php

namespace W3com\HulkBundle\Model;

class Column
{
    const TPL_TEXT = 'Text';

    const TPL_ICON = 'Icon';

    const TPL_COMBO = 'Combo';

    public function __construct($column)
    {
        foreach ($column as $field => $value) {

            switch ($field) {

                case 'Template':
                    $this->setTemplate($value);
                    break;

                case 'ColumnName':
                    $this->setViewName($value);
                    break;

                case 'FieldName':
                    $this->setFieldName($value);
                    break;

                case 'Action':
                    $this->setAction($value);
                    break;
            }
        }
    }

    private $order;

    private $template;

    private $viewName;

    private $fieldName;

    /**
     * @var boolean
     */
    private $hidden;

    private $index;

    private $action;

    private $icon;

    /**
     * @var string
     * Determine if corresponding field exist in SAP ODATA and prevent error
     */
    private $active;

    public function setOrder($order)
    {
        $this->order = $order;
    }

    public function setViewName($viewName)
    {
        return $this->viewName = $viewName;
    }

    public function setTemplate($template)
    {
        return $this->template = $template;
    }

    public function setFieldName($fieldName)
    {
        $this->fieldName = $fieldName;
    }

    public function setAction($action)
    {
        return $this->action = $action;
    }

    public function setIcon($icon)
    {
        return $this->icon = $icon;
    }

    /**
     * @return mixed
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    public function setActive(string $active)
    {
        $this->active = $active;
    }

    public function getActive()
    {
        return $this->active;
    }

    /**
     * @return mixed
     */
    public function getViewName()
    {
        return $this->viewName;
    }

    /**
     * @return bool
     */
    public function isHidden(): ?bool
    {
        return $this->hidden;
    }

    /**
     * @param bool $hidden
     */
    public function setHidden(bool $hidden): void
    {
        $this->hidden = $hidden;
    }

    /**
     * @return mixed
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @param mixed $index
     */
    public function setIndex($index): void
    {
        $this->index = $index;
    }

}