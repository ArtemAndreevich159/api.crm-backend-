<?php

namespace components;

/**
 * Class ActiveRecord
 * @package components
 */
class ActiveRecord extends Db
{
    /** @var  string */
    protected $tableName;
    /** @var array  */
    protected $fields = [];
    /** @var bool  */
    protected $isNew = true;
    /** @var array  */
    protected $fieldsValue = [];
    /** @var string  */
    protected $pk;

    /**
     * ActiveRecord constructor.
     */
    public function __construct() {
        parent::__construct();
        $fields = $this->queryRows('SHOW COLUMNS FROM ' . $this->tableName);
        foreach ($fields as $field) {
            $fieldName = array_shift($field);
            $this->fields[$fieldName] = $field;
            $this->fieldsValue[$fieldName] = null;
            if ('PRI' === $field['Key']) {
                $this->pk = $fieldName;
            }
        }
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name) {
        return $this->fieldsValue[$name];
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value) {
        $this->fieldsValue[$name] = $value;
    }

    /**
     * @return bool|null|string
     */
    public function save() {
        $this->beforeSave();
        if ($this->isNew) {
            $res = $this->insert($this->tableName, $this->fieldsValue);
        } else {
            $res = $this->update(
                $this->tableName,
                $this->fieldsValue,
                "{$this->pk['field']} = {$this->pk['value']}"
            );
        }

        if ($res) {
            $this->afterSave();
        }

        return $res;
    }

    public function beforeSave() {

    }

    public function afterSave() {

    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function findByPK($value) {
        $sql = "
            SELECT * FROM {$this->tableName}
            WHERE {$this->pk} = :PK
        ";
        $result = $this->queryRow($sql, [':PK' => $value]);

        if (!$result) {
            return false;
        }

        foreach ($result as $field => $value) {
            $this->fieldsValue[$field] = $value;
        }

        $this->isNew = false;

        return true;
    }

    /**
     * @param array $attrs
     * @return bool
     */
    public function findByAttrs($attrs) {
        $cond = '';
        $first = true;

        foreach (array_keys($attrs) as $attr) {
            if (!$first) {
                $cond .= ' AND ';
            }

            $first = false;
            $cond .= $attr . ' = :' . $attr;
            $attr = ':' . $attr;
        }

        $sql = "
            SELECT * FROM {$this->tableName}
            WHERE {$cond}
        ";
        $result = $this->queryRow($sql, $attrs);

        if (!$result) {
            return false;
        }

        foreach ($result as $field => $value) {
            $this->fieldsValue[$field] = $value;
        }

        $this->isNew = false;

        return true;
    }

    /**
     * @param array $attrs
     * @return null
     */
    public function findAll($attrs) {
        $cond = '';
        $first = true;

        foreach (array_keys($attrs) as $attr) {
            if (!$first) {
                $cond .= ' AND ';
            }

            $first = false;
            $cond .= $attr . ' = :' . $attr;
            $attr = ':' . $attr;
        }

        $sql = "
            SELECT * FROM {$this->tableName}
            WHERE {$cond}
        ";
        return $this->queryRows($sql, $attrs);
    }

    /**
     * @return bool
     */
    public function delete() {
        if ($this->isNew) {
            return false;
        }

        $this->beforeDelete();
        $res = $this->deleted($this->tableName, [$this->pk => $this->fieldsValue[$this->pk]]);

        if ($res) {
            $this->afterDelete();
        }

        return $res;
    }

    public function beforeDelete() {

    }

    public function afterDelete() {

    }
}