#### java集合体系

> `Collection`体系图

![collection-01](https://qiniu.lglg.xyz/images/lglg/posts/202206/28/collection-01.png)

> `Map` 体系图

![collection-02](https://qiniu.lglg.xyz/images/lglg/posts/202206/28/collection-02.png)


-------

* ``Collection``接口的实现子类可以存放多个元素，每个元素都是`Object`;其中部分可以存放**重复**元素，有的不可以；`List`接口子类的元素是有序的，`Set`
接口子类元素是无序的；`Collection`接口没有直接子类。

* `Collection` 中主要方法
  * `add` 添加元素
  * `contains`查找元素是否存在
  * `remove`删除元素
  * `addAll`添加多个匀速
  * `containsAll`查找多个元素
  * `removeAll`删除全部元素
  * `size`获取列表元素个数
  * `isEmpty`判断是否为空

* 实现`Collection`接口子类的遍历方法
  * 普通遍历
  * 迭代器遍历（退出while循环后，迭代器指向最后元素，想要再次遍历迭代器，需要重置迭代器`iterator = arrayList.iterator()`）
  * 增强for循环（底层还是迭代器遍历）

```java
class Test {
    public static void main(String[] args) {
        ArrayList<Object> arrayList = new ArrayList<>();

        // 普通方式
        for (int i = 0; i < arrayList.size(); i++) {
            System.out.println(arrayList.get(i));
        }

        // 迭代器
        Iterator<Object> iterator = arrayList.iterator();

        while (iterator.hasNext()) {
            System.out.println(iterator.next());
        }
        
        // 增强for循环
        for (Object o: arrayList) {
            System.out.println(o);
        }
    }
}
```

* `List`接口的实现子类中元素是有序的，（添加和取出的元素是一致的）
* `List`接口的实现子类中元素是可以重复的
* `List`接口的实现子类中每个元素都有其对应的索引
* `List`接口的实现子类中每个元素都有对应一个整型的序号记载其在容器中的位置，可以根据索引取出容器中的元素

----------------

**ArrayList**的扩容机制

> 当使用无参构造器时，首先创建一个空的`elementData`,数组容量为0，第一次添加数据时，判断数组是否为空，如果为空，就给数组`elementData`扩容为10，当添加数据
> 大于10个时，第三次（及以后）扩容就是按照当前容量1.5倍扩容，最终调用的是`Arrays.copyOf()`扩容

> 当使用有参构造器`ArrayList(int initialCapacity)`第一次容量就是指定的容量，以后的扩容就是1.5倍扩容


* `Vector`和`ArrayList`做比较

|    type     | 底层结构 |   版本   |  线程安全   |                扩容倍数                |
|:-----------:|:----:|:------:|:-------:|:----------------------------------:|
| `ArrayList` | 可变数组 | jdk1.2 | 不安全，效率高 | 有参构造，1.5倍扩容；无参构造，第一次是10，第二次开始按1.5倍 |
|  `Vector`   | 可变数组 | jdk1.0 | 安全，效率不高 |   有参构造，2倍扩容；无参构造，第一次是10，第二次开始按2倍   |



**LinkedList**底层机制

![collection-03](https://qiniu.lglg.xyz/images/lglg/posts/202206/28/collection-03.png)


|     type     | 底层结构 |   增删效率    | 改查效率 |
|:------------:|:----:|:---------:|:----:|
| `ArrayList`  | 可变数组 |  较低，数组扩容  |  较高  |
| `LinkedList` | 双向链表 | 较高，通过链表追加 |  较低  |

----------------------

**Set接口**

* 无序，添加和取出元素的顺序是不一致的
* 不允许添加重复的元素
* `Set`接口子类是不可以通过普通循环遍历元素，（迭代器和增强for循环是可以的）

> `HashSet`的底层是`HashMap`,而`HashMap`的底层是（数组+链表+红黑树）

1. 添加某个元素的时候，首先会通过算法得到一个hash值，之后再转化为一个索引值，
2. 找到存储数据的数据表，判断这个索引值是否在表中
3. 表中没有这个索引值，直接插入数据
4. 表中有这个索引值，调用算法比较存储的数据是否相同，如果相同，放弃添加，否则添加到最后（链表|树的最后）
5. 在java8中，如果一条链表的元素个数等于8，并且数据表的大小超过默认值64，链表就会转化为红黑树
6. 最外层table表的扩容机制，第一次是16，当数据到达临界值`16 * 0.75 = 12`时，就开始扩容 16 * 2  = 32；第二次是`32 * 0.75 = 24`扩容32 * 2 = 64

> 对于在外层扩容机制：是向链表中添加元素的个数来做依据扩容的,下面红框中的数据

![collection-04](https://qiniu.lglg.xyz/images/lglg/posts/202206/28/collection-04.png)


添加第一个元素时：

![collection-05](https://qiniu.lglg.xyz/images/lglg/posts/202206/28/collection-05.png)

判断当前添加元素的个数是否大于当前的扩容临界值

![collection-06](https://qiniu.lglg.xyz/images/lglg/posts/202206/28/collection-06.png)

大于（16|32）* 0.75的临界值后,判断原来容量扩大已被之后小于一个极限值并且大于初始的容量16，符合条件扩大一倍

![collection-07](https://qiniu.lglg.xyz/images/lglg/posts/202206/28/collection-07.png)

-------------------------


#### Map的接口子类

* 存放元素时`key->value`映射关系形式的参在
* 存放（取出）元素是无序的
* Map中的key和Value可以是任何引用类型的数据，会封装在`HashMap$Node`对象中
* Map中的key是不允许重复的
* Map中的value是可以重复的
* Map中的key可以为null,但是只允许有一个
* key和value之间存在单一的一对一关系，通过key可以取出value

**Map**常用方法

1. ``put`` 添加数据
2. ``remove`` 删除数据
3. ``get`` 得到数据
4. ``size`` 获取元素个数
5. ``isEmpty`` 判断是否为空
6. ``clear`` 清除
7. ``containsKey`` 查找key是否存在

**Map**的遍历方式

1. 取出所有key,再通过key得到对应的value

```java
class Test {
  public static void main(String[] args) {
    HashMap<Object, Object> objectObjectHashMap = new HashMap<>();

    objectObjectHashMap.put("jay", "1");
    objectObjectHashMap.put("english", "EN");
    objectObjectHashMap.put("china", "zh_CN");

    // 取出所有key 遍历，
    Set<Object> objects3 = objectObjectHashMap.keySet();

    // 增强for
    Set<Object> objects3 = objectObjectHashMap.keySet();

    for (Object o: objects3) {
      System.out.println("key:" + o + " value: " + objectObjectHashMap.get(o));
    }

    // 迭代器
    Iterator<Object> iterator1 = objects3.iterator();

    while (iterator1.hasNext()) {
      Object next =  iterator1.next();

      System.out.println(next + " " + objectObjectHashMap.get(next));

    }
  }
}
```

2. 取出所有的values

```java
class Test {
  public static void main(String[] args) {
    HashMap<Object, Object> objectObjectHashMap = new HashMap<>();

    objectObjectHashMap.put("jay", "1");
    objectObjectHashMap.put("english", "EN");
    objectObjectHashMap.put("china", "zh_CN");
    Collection<Object> values = objectObjectHashMap.values();

    // 增强for
    for (Object v: values) {
      System.out.println(v);
    }
    
    // 迭代器
    while (iterator2.hasNext()) {
      Object next =  iterator2.next();

      System.out.println(next);

    }
  }
}
```

3. 通过`EntrySet`

```java
class Test {
  public static void main(String[] args) {
    HashMap<Object, Object> objectObjectHashMap = new HashMap<>();

    objectObjectHashMap.put("jay", "1");
    objectObjectHashMap.put("english", "EN");
    objectObjectHashMap.put("china", "zh_CN");

    Set<Map.Entry<Object, Object>> entries = objectObjectHashMap.entrySet();

    // 增强for
    for (Object o: entries) {
      Map.Entry m = (Map.Entry) o;

      System.out.println(m.getKey() + " " + m.getValue());
    }

    Iterator<Map.Entry<Object, Object>> iterator3 = entries.iterator();

    // 迭代器
    while (iterator3.hasNext()) {
      Map.Entry<Object, Object> next = iterator3.next();

      System.out.println(next.getKey() + " " + next.getValue());
    }
  }
}
```