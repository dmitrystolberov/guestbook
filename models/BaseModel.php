<?php

abstract class BaseModel
{
    private static $models = [];

    public abstract function getTableName();

    /**
     * @param bool $forceNew
     * @param string $className
     * @return mixed
     */
    public static function model($forceNew = false, $className = __CLASS__)
    {
        if(isset(self::$models[$className]) && !$forceNew) {
            return self::$models[$className];
        } else {
            $model = self::$models[$className] = new $className(null);
            return $model;
        }
    }

    /**
     * @param $attributes
     */
    public function setAttributes($attributes)
    {
        foreach ($attributes as $attributeName => $attributeValues) {
            if (property_exists($this, $attributeName)) {
                $this->$attributeName = $attributeValues;
            }
        }
    }

    /**
     * @return int
     */
    public function save()
    {
        if (isset($this->id)) {
            return $this->_update();
        } else {
            return $this->_insert();
        }
    }

    /**
     * @return bool
     */
    private function _update()
    {
        $attributes = get_object_vars($this);
        $set = $params = [];
        foreach ($attributes as $attributeName => $attributeValue) {
            if ($attributeName == 'id') {
                continue;
            }
            $set[] = $attributeName . '=:' . $attributeName;
            $params[':' . $attributeName] = $attributeValue;
        }
        $params[':pk'] = $this->id;
        $connection = DbConnection::getInstance()->getConnection();
        $statement = $connection->prepare(
            'UPDATE ' . $this->getTableName() . ' SET ' . implode(', ', $set) . ' WHERE id=:pk'
        );
        $statement->execute($params);
        return $this->id;
    }

    public function delete()
    {
        if ($this->id) {
            $this->execute('DELETE FROM ' . $this->getTableName() . ' WHERE id=' . $this->id);
        }
    }

    /**
     * @return int
     */
    private function _insert() {
        $attributes = get_object_vars($this);
        $insertFields = $values = $params = [];
        foreach ($attributes as $attributeName => $attributeValue) {
            if ($attributeName == 'id' || empty($attributeValue)) {
                continue;
            }
            $insertFields[] = $attributeName;
            $params[':' . $attributeName] = $attributeValue;
        }
        $connection = DbConnection::getInstance()->getConnection();
        $statement = $connection->prepare(
            'INSERT INTO ' . $this->getTableName() . ' (' . implode(', ', $insertFields) . ')'
            . ' VALUES (' . implode(', ', array_keys($params)) . ')'
        );
        $statement->execute($params);
        return $connection->lastInsertId();
    }

    /**
     * @param $sql
     * @param array $params
     * @return PDOStatement
     */
    public function execute($sql, $params = [])
    {
        $connection = DbConnection::getInstance()->getConnection();
        $statement = $connection->prepare($sql);
        $statement->execute($params);
        return $statement;
    }

    /**
     * @param $attributes
     * @return mixed|null
     */
    public function findByAttributes($attributes)
    {
        $statement = $this->executeFind($attributes);
        $res =  $statement->fetch(PDO::FETCH_OBJ);
        if ($res) {
            $model = $this->model(true);
            $resAttributes = get_object_vars($res);
            $model->setAttributes($resAttributes);
            return $model;
        } else {
            return null;
        }
    }

    /**
     * @param $attributes
     * @return PDOStatement
     */
    private function executeFind($attributes)
    {
        $tableName = $this->getTableName();
        $connection = DbConnection::getInstance()->getConnection();
        $attrs = $params = [];
        foreach ($attributes as $attributeName => $attribute) {
            $attrs[] = $attributeName . '=:' . $attributeName;
            $params[':' . $attributeName] = $attribute;
        }
        $statement = $connection->prepare('SELECT * FROM ' . $tableName . ' WHERE ' . implode(' AND ', $attrs));
        $statement->execute($params);
        return $statement;
    }
}