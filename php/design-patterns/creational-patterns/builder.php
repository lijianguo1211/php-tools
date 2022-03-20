<?php

use JetBrains\PhpStorm\Pure;

/**
 * @Notes:
 *
 * @File Name: builder.php
 * @Date: 2022/3/20
 * @Created By: Jay.Li
 */

interface QueryBuilder
{
    public function reset():void;
    public function select(array $fields = ["*"]):self;

    public function table(string $table):self;

    public function where(string $field, $value, $operator = '='):self;

    public function limit($start, $offset):self;

    public function getSql(): string;
}

class MysqlQueryBuilder implements QueryBuilder
{
    /**
     * @var stdClass|null
     */
    protected ?stdClass $query = null;

    #[Pure]
    public function __construct()
    {
        $this->query = new \stdClass();
    }


    public function select(array $fields = ["*"]): QueryBuilder
    {
        // TODO: Implement select() method.
        if (empty($this->query)) {
            $this->reset();
        }

        $this->query->field = '';

        foreach ($fields as $item) {
            $this->query->field .= "`" . $item . "`,";
        }
        $this->query->field = rtrim($this->query->field, ',');

        $this->query->type = 'select';

        return $this;
    }

    public function table(string $table): QueryBuilder
    {
        // TODO: Implement table() method.
        $this->reset();

        $this->query->table = "`" . $table . "`";

        return $this;
    }

    /**
     * @throws Exception
     */
    public function where(string $field, $value, $operator = '='): QueryBuilder
    {
        if (empty($this->query)) {
            $this->reset();
        }
        // TODO: Implement where() method.
        if (!in_array($this->query->type, ['select', 'update', 'delete'])) {
            throw new \Exception("WHERE can only be added to SELECT, UPDATE OR DELETE");
        }

        $this->query->where[] = "`$field` $operator '$value'";

        return $this;
    }

    /**
     * @throws Exception
     */
    public function limit($start, $offset): QueryBuilder
    {
        if (empty($this->query)) {
            $this->reset();
        }
        // TODO: Implement limit() method.
        if ($this->query->type != 'select') {
            throw new \Exception("LIMIT can only be added to SELECT");
        }
        $this->query->limit = " LIMIT " . $start . ", " . $offset;

        return $this;
    }

    public function getSql(): string
    {
        // TODO: Implement getSql() method.
        $query = $this->query;

        $sql = 'select ';

        if (!empty($query->field)) {
            $sql .= $query->field;
        }
        if (!empty($query->table)) {
            $sql .= " from " . $query->table;
        }

        if (!empty($query->where)) {
            $sql .= " WHERE " . implode(' AND ', $query->where);
        }

        if (isset($query->limit)) {
            $sql .= $query->limit;
        }

        return $sql . ";";
    }

    public function reset(): void
    {
        // TODO: Implement reset() method.
        $this->query = new \stdClass();
    }
}

$mysql = new MysqlQueryBuilder();

$sql = $mysql->table("users")->select(['id', 'name', 'email'])->where('id', 5)
    ->where('email', '123@test.com')
    ->limit(0, 10)
    ->getSql();

var_dump($sql);