<?php

class Image extends BaseModel
{

    public $id;
    public $image_name;

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return User the static model class
     */
    public static function model($forceNew = false, $className = __CLASS__)
    {
        return parent::model($forceNew, $className);
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return 'images';
    }
}