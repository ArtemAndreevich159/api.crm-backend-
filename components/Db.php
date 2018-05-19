<?php

namespace components;

use core\Component;

/**
 * Class Db
 * @package components
 */
class Db extends Component
{
    /** @var  \PDO */
    protected $_db;

    /**
     * Db constructor.
     */
    public function __construct() {
        $params = $this->config->get('db');

        $dsn = "mysql:host={$params['host']};dbname={$params['dbname']}";
        $db = new \PDO($dsn, $params['user'], $params['password']);
        $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $this->_db = $db;
    }

    /**
     * @param string $table
     * @param array $fields
     * @param null|array $insertParams
     * @return bool|null|string
     */
    public function insert($table, $fields, $insertParams = null) {
        try {
            $result = null;
            $names = '';
            $values = '';

            foreach (array_keys($fields) as $name) {
                if ('' !== $names) {
                    $names .= ', ';
                    $values .= ', ';
                }

                $names .= $name;
                $values .= (':' . $name);
            }

            $ignore = isset($insertParams['ignore']) && $insertParams['ignore'] ? 'IGNORE' : '';

            $sql = "INSERT {$ignore} INTO {$table} ({$names}) VALUES ({$values})";
            $rs = $this->_db->prepare($sql);

            foreach ($fields as $name => $val) {
                $rs->bindValue(':' . $name, $val);
            }

            $result = false;

            if ($rs->execute()) {
                $result = $this->_db->lastInsertId();
            }

            return $result;
        } catch (\Exception $e) {
            $this->report($e);
        }
    }

    /**
     * @param $table
     * @param $fields
     * @param $cond
     * @param null $params
     * @return bool
     */
    public function update($table, $fields, $cond, $params = null) {
        try {
            $sql = "UPDATE {$table} SET ";

            $first = true;

            foreach (array_keys($fields) as $name) {
                if (!$first) {
                    $sql .= ', ';
                }

                $first = false;
                $sql .= ($name . ' = :' . $name);
            }

            if (!is_array($params)) {
                $params = [];
            }

            $sql .= (' WHERE ' . $cond);

            $rs = $this->_db->prepare($sql);

            foreach ($fields as $name => $val) {
                $params[':' . $name] = $name;
            }

            $result = $rs->execute($params);

            return $result;
        } catch (\Exception $e) {
            $this->report($e);
        }
    }

    /**
     * @param $query
     * @param null $params
     * @return null|string
     */
    public function queryValue($query, $params = null) {
        try {
            $result = null;
            $stmt = $this->_db->prepare($query);

            if ($stmt->execute($params)) {
                $result = $stmt->fetchColumn();
                $stmt->closeCursor();
            }

            return $result;
        } catch (\Exception $e) {
            $this->report($e);
        }
    }

    /**
     * @param $query
     * @param null $params
     * @return array|null
     */
    public function queryValues($query, $params = null) {
        try {
            $result = null;
            $stmt = $this->_db->prepare($query);

            if ($stmt->execute($params)) {
                $result = [];

                while ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
                    $result[] = $row[0];
                }
            }

            return $result;
        } catch (\Exception $e) {
            $this->report($e);
        }
    }

    /**
     * @param $query
     * @param null $params
     * @param int $fetchStyle
     * @param null $classname
     * @return null
     */
    public function queryRow($query, $params = null, $fetchStyle = \PDO::FETCH_ASSOC, $classname = null) {
        $row = $this->queryRowOrRows(true, $query, $params, $fetchStyle, $classname);
        return $row;
    }

    /**
     * @param $query
     * @param null $params
     * @param int $fetchStyle
     * @param null $classname
     * @return null
     */
    public function queryRows($query, $params = null, $fetchStyle = \PDO::FETCH_ASSOC, $classname = null) {
        $rows = $this->queryRowOrRows(false, $query, $params, $fetchStyle, $classname);
        return $rows;
    }

    /**
     * @param $singleRow
     * @param $query
     * @param null $params
     * @param int $fetchStyle
     * @param null $classname
     * @return array|mixed|null
     */
    private function queryRowOrRows($singleRow, $query, $params = null, $fetchStyle = \PDO::FETCH_ASSOC, $classname = null) {
        try {
            $result = null;
            $stmt = $this->_db->prepare($query);

            if ($classname) {
                $stmt->setFetchMode($fetchStyle, $classname);
            } else {
                $stmt->setFetchMode($fetchStyle);
            }

            if ($stmt->execute($params)) {
                $result = $singleRow ? $stmt->fetch() : $stmt->fetchAll();
                $stmt->closeCursor();
            }

            return $result;
        } catch (\Exception $e) {
            $this->report($e);
        }
    }

    /**
     * @param $table
     * @param $cond
     * @return bool
     */
    public function deleted($table, $cond) {
        try {
            $sql = "DELETE FROM {$table} WHERE ";

            $first = true;

            foreach (array_keys($cond) as $item) {
                if (!$first) {
                    $first = false;
                    $sql .= ', ';
                }

                $first = false;
                $sql .= $item . ' = :_' . $item;
            }

            $rs = $this->_db->prepare($sql);

            $params = [];

            foreach ($cond as $name => $val) {
                $params[':_' . $name] = $val;
            }

            return $rs->execute($params);
        } catch (\Exception $e) {
            $this->report($e);
        }
    }

    /**
     * @param $e
     */
    private function report($e) {
        throw $e;
    }
}