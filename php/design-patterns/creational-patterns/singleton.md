### 单例模式

> 单例模式是一种创建型设计模式， 让你能够保证一个类只有一个实例， 并提供一个访问该实例的全局节点。

* 必要条件

1. 一个类不管实例化多少次，总是返回的是同一个实例
2. 因为`oop`在`new`一个对象的时候，总是会返回一个新的对象，所以单例模式就不允许外部对象`new`单例类，这个时候就需要把对象的构造方法设为私有的，
防止外部类`new`这个对象
3. 因为禁止了外部类`new`单例对象，这里就需要一个静态变量来存储当前的实例，给外部类来访问。
4. 比如一个程序中，只允许`new`一个`DB`类，后面的程序调用`DB`都是同一个，不会造成资源的浪费
5. 生活中，一个国家只有一个领导结构，不管是谁来当家，都是这个结构来领导这个国家

* 在php中实现需要注意
  * `__construct` 需要设为私有的，防止外部`new`
  * `__clone` 需要设为私有的，防止外部`clone`
  * `__invoke` 需要设为`final`,并返回空，防止在类内部直接实例化类，外部把示例当方法调用，或者攻击者改写
  * `____wakeup` 需要设为`final`,并返回异常，防止在反序列化时被改写

* 具体代码实现

```php
class Singleton
{
    private static ?self $instance = null;

    private function __construct()
    {
    }

    private function __clone()
    {

    }


    /**
     * @throws Exception
     */
    public function __wakeup()
    {
//        throw new \Exception("Cannot unserialize singleton");
        return new static();
    }


    final public function __invoke(): void
    {
        // TODO: Implement __invoke() method.
    }


    public static function getInstance():self
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }
}

$obj = Singleton::getInstance();
$obj2 = Singleton::getInstance();

$obj4 = serialize($obj);

$obj4 = unserialize($obj4);

var_dump($obj4 === $obj);

//$obj1 = clone $obj;

$obj3 = $obj();
var_dump($obj3 === $obj);

//var_dump($obj === $obj1);
var_dump($obj === $obj2);
```
