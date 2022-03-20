**生成器模式**

> 生成器模式是一种创建型设计模式， 使你能够分步骤创建复杂对象。 该模式允许你使用相同的创建代码生成不同类型和形式的对象。

1. 该模式会将对象构造过程划分为一组步骤,但是不是所有步骤都需要调用
2. 最常用的一些例子就是sql查询的查询构造器
3. 当有不同的查询的时候，只需要调用不同的步骤即可
4. 再比如，sql查询中，mysql的limit，offset 写法和 postgreSql不一样，别的都一样，这个时候就只需要改写一个方法即可

**代码示例**

* 数据查询接口

```php
interface QueryBuilder
{
    public function reset():void;
    public function select(array $fields = ["*"]):self;

    public function table(string $table):self;

    public function where(string $field, $value, $operator = '='):self;

    public function limit($start, $offset):self;

    public function getSql(): string;
}
```

* mysql 查询构造器

```php
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
```

* 别的查询构造器

```php
class OtherQueryBuilder implements QueryBuilder
{

}
```

* 测试调用

```php
$mysql = new MysqlQueryBuilder();

$sql = $mysql->table("users")->select(['id', 'name', 'email'])->where('id', 5)
    ->where('email', '123@test.com')
    ->limit(0, 10)
    ->getSql();

var_dump($sql);
```