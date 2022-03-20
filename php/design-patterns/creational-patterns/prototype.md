**原型模式**

> 原型模式是一种创建型设计模式， 使你能够复制已有对象， 而又无需使代码依赖它们所属的类。
> 所有的原型类都必须有一个通用的接口， 使得即使在对象所属的具体类未知的情况下也能复制对象。 原型对象可以生成自身的完整副本， 因为相同类的对象可以相互访问对方的私有成员变量。

1. 原型模式提供了一种复制已有对象的简便方式， 可代替直接复制对象的所有成员变量来对对象进行重构的方法
2. 原型模式让你能够在被克隆类的内部进行克隆工作， 因此可以不受限制地访问类的私有成员变量。
3. 创建原型接口， 并在其中声明 克隆方法。 如果你已有类层次结构， 则只需在其所有类中添加该方法即可
4. 在PHP中有魔术方法`__clone`,使用原型模式时，只需要重写这个方法就可以了，在这个方法中做需要做的事儿

**代码实现**

* 原型类

```php
class Page
{
    /**
     * 全是私有变量
     * @var  string
     */
    private string $title;

    private string $body;

    /**
     * @var  Author
     */
    private Author $auther;

    private array $comments = [];

    /**
     * @var DateTime
     */
    private DateTime $data;

    public function __construct(string $title, string $body, Author $author)
    {
        $this->title = $title;

        $this->body = $body;

        $this->auther = $author;

        $this->auther->addToPage($this);
        
        $this->data = new \DateTime();
    }

    public function addComments(string $commend):void
    {
        $this->comments[] = $commend;
    }

    /**
     * @Notes: 需要克隆的数据
     *
     * @User: Jay.Li
     * @Methods: __clone
     * @Date: 2022/3/20
     */
    public function __clone()
    {
        $this->title = "Copy of " . $this->title;

        $this->auther->addToPage($this);

        $this->comments = [];

        $this->data = new \DateTime();
    }


}

class Author
{
    /**
     * @var  string
     */
    private string $name;

    /**
     * @var  array
     */
    private array $pages = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }


    public function addToPage(Page $page)
    {
        $this->pages[] = $page;
    }
}
```

* 测试

```php
$author = new Author("李四");

$page = new Page("大家好", "展示问候语呀~", $author);

$page->addComments("start | stop | reload");

$cloneObj = clone $page;

print_r($cloneObj);
```

* 输出

```php
//Page Object
//(
//    [title:Page:private] => Copy of 大家好
//    [body:Page:private] => 展示问候语呀~
//    [auther:Page:private] => Author Object
//        (
//            [name:Author:private] => 李四
//            [pages:Author:private] => Array
//                (
//                    [0] => Page Object
//                        (
//                            [title:Page:private] => 大家好
//                            [body:Page:private] => 展示问候语呀~
//                            [auther:Page:private] => Author Object
// *RECURSION*
//                            [comments:Page:private] => Array
//                                (
//                                    [0] => start | stop | reload
//                                )
//
//                            [data:Page:private] => DateTime Object
//                                (
//                                    [date] => 2022-03-20 19:50:09.899059
//                                    [timezone_type] => 3
//                                    [timezone] => Asia/Shanghai
//                                )
//
//                        )
//
//                    [1] => Page Object
// *RECURSION*
//                )
//
//        )
//
//    [comments:Page:private] => Array
//        (
//        )
//
//    [data:Page:private] => DateTime Object
//        (
//            [date] => 2022-03-20 19:50:09.899077
//            [timezone_type] => 3
//            [timezone] => Asia/Shanghai
//        )
//
//)
```