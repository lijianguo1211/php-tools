#### java 包装类相关的基础

* java 中有八大基础类型,这些基础类型又分别对应各自的包装类

| Foundation type | name |                          范围                          | Packaging |
|:---------------:|:----:|:----------------------------------------------------:|:----------|
|      byte       |  整型  |                       -128~127                       | Byte      |
|      short      |  整型  |                     -32768~32767                     | Short     |
|       int       |  整型  |             -2,147,483,648~2,147,483,647             | Integer   |
|      long       |  整型  | -9,223,372,036,854,775,808~9,223,372,036,854,775,807 | Long      |
|      float      | 浮点型  |                       单精度 32位                        | Float     |
|     double      | 浮点型  |                       双精度 64位                        | Double    |
|      char       | 字符型  |   \u0000[0] ~ \uffff[65535] (单一的 16 位 Unicode 字符)    | Character |
|     boolean     | 布尔型  |                      true~false                      | Boolean   |

* ``Character`` 类图

![Character](https://qiniu.lglg.xyz/images/lglg/posts/202206/23/char-01.png)

* ``Boolean`` 类图

![Boolean](https://qiniu.lglg.xyz/images/lglg/posts/202206/23/bool-01.png)

* ``Byte|Short|Integer|Long|Float|Double`` 类图

![number-01](https://qiniu.lglg.xyz/images/lglg/posts/202206/23/number-01.png)

> 在JDK1.5之前，装箱和拆箱都是需要手动进行，而在之后的版本中，都是程序自动转换了。

**装箱** 把基础类型转换为包装类型。比如: `Integer num = 1`
**拆箱** 把包装类型转换为基础类型。比如: `int num = new Integer(1)`

* 手动装箱

```java
class Test {
    public static void main(String[] args) {
        int num1 = 1;

        Integer integer = Integer.valueOf(num1);

        Integer integer1 = new Integer(num1);
    }
}
```

* 手动拆箱

```java
class Test {
    public static void main(String[] args) {
        Integer num2 = 1;

        int i = num2.intValue();
    }
}
```

* 自动装箱，底层调用的是`valueOf()`

```java
class Test {
    public static void main(String[] args) {
        Integer num3 = 3;
    }
}
```

* 自动拆箱，底层调用的是`intValue()`

```java
class Test {
    public static void main(String[] args) {
        Integer num4 = 3;
        
        int num5 = num4;
        
        int num6 = new Integer(6);
    }
}
```

-----------

* 关于一些概念测试

```java
class Test {
    public static void main(String[] args) {
        Integer num1 = new Integer(1);
        Integer num2 = new Integer(1);

        System.out.println(num1 == num2);
        // output false 这是new 两个不同的类
    }
}
```

```java
class Test {
    public static void main(String[] args) {
        Integer num1 = 1;
        Integer num2 = 1;

        System.out.println(num2 == num1);
        // output true 自动装箱，底层是  public static Integer valueOf(int i) ，从缓存中拿的数据
    }
}
```


```java
class Test {
    public static void main(String[] args) {
        Integer num1 = 128;
        Integer num2 = 128;

        System.out.println(num2 == num1);
        // output false 自动装箱，底层是  public static Integer valueOf(int i) ，从缓存中拿的数据,但是缓存中的数据有范围限制，刚好128超出范围，属于new 对象
    }
}
```