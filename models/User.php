<?php

class User extends BaseModel
{

    const ROLE_USER = 1;
    const ROLE_ADMIN = 2;

    public $id;
    public $login;
    public $role;
    protected $password;

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
        return 'users';
    }

    /**
     * @param $text string
     * @return string
     */
    public function getHash($text)
    {
        return md5($text . 'some_custom_salt');
    }
}