<?php

use JetBrains\PhpStorm\Pure;

interface Db
{
    public function connection(array $config):DbConnection;

    public function select(string $sql):DbSelect;
}

class MysqlDb implements Db
{
    #[Pure]
    public function connection(array $config):DbConnection
    {
        // TODO: Implement connection() method.
        return new MysqlDbConnection($config);
    }

    #[Pure]
    public function select(string $sql):DbSelect
    {
        // TODO: Implement select() method.
        return new MysqlDbSelect();
    }
}

class OracleDb implements Db
{
    #[Pure]
    public function connection(array $config):DbConnection
    {
        // TODO: Implement connection() method.
        return new OracleDbConnection($config);
    }

    #[Pure]
    public function select(string $sql):DbSelect
    {
        // TODO: Implement select() method.
        return new OracleDbSelect();
    }
}

interface DbConnection
{
    public function connect();
}

class MysqlDbConnection implements DbConnection
{
    public function __construct(array $config)
    {
    }

    public function connect()
    {
        // TODO: Implement connect() method.
        echo "mysql 连接数据库~\n";
    }
}

class OracleDbConnection implements DbConnection
{
    public function __construct(array $config)
    {
    }

    public function connect()
    {
        // TODO: Implement connect() method.
        echo "Oracle 连接数据库~\n";
    }
}

interface DbSelect
{
    public function get();
}

class MysqlDbSelect implements DbSelect
{
    public function get()
    {
        // TODO: Implement get() method.
        echo "mysql 查询数据~\n";
    }
}

class OracleDbSelect implements DbSelect
{
    public function get()
    {
        // TODO: Implement get() method.
        echo "Oracle 查询数据~\n";
    }
}

class App
{
    protected Db $factory;

    /**
     * @throws Exception
     */
    public function __construct(string $type)
    {
        $this->factory = match ($type) {
            "mysql" => new MysqlDb(),
            "oracle" => new OracleDb(),
            default => throw new \Exception("目前不支持 $type 数据库的连接~\n"),
        };
    }

    public function connect()
    {
        $this->factory->connection([])->connect();
    }

    public function get()
    {
        $this->factory->select("select * from users")->get();
    }
}

$obj = new App("mysql");
$obj->connect();
$obj->get();
$obj1= new App("oracle");
$obj1->connect();
$obj1->get();