**抽象工厂模式**

> 抽象工厂模式是一种创建型设计模式， 它能创建一系列相关的对象， 而无需指定其具体类。

**实现**

1. 以不同的产品类型和产品变体来抽象
2. 为所有的产品类型申明接口
3. 具体的产品类来实现接口
4. 申明抽象工厂接口，并且在接口中为所有抽象产品提供一组构建方法。
5. 为每种产品变体实现一个具体的工厂类
6. 在应用程序中开发初始化代码。 该代码根据应用程序配置或当前环境， 对特定具体工厂类进行初始化。 然后将该工厂对象传递给所有需要创建产品的类

**举例**

1. 声明一个数据连接，数据查询的接口
2. 不同的数据库有不同的连接方式
3. 在程序中，不同的连接方式又又不同的参数传递和方法

**代码实现**

* 声明一个数据接口

```php
interface Db
{
    public function connection(array $config):DbConnection;

    public function select(string $sql):DbSelect;
}
```

* 不同数据库的实现

```php
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
```

* 不同数据库的连接又是不同的,不同的查询也是不同的，单独设立接口，各自实现

```php
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
```

* 最后在客户端调用

````php
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
````

