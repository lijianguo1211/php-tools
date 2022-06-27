### Java数据类型之String

![String-01](https://qiniu.lglg.xyz/images/lglg/posts/202206/26/string-01.png)

1. `String` 是一个`final`关键字休息的类，首先它是不允许被继承的，没有子类。
2. 实现了`Serializable`接口，（可串行化）可以在网络中传输
3. 实现了`Comparable`接口，对象之间可以比较大小
4. `String`对象用于保存字符串，也就是一组字符序列
5. 字符串字符使用`Unicode`字符编码，一个字符（不区分字母还是汉字）占两个字节
6. `String` 实现了很多的构造器，构造器重载

![string-02](https://qiniu.lglg.xyz/images/lglg/posts/202206/26/string-02.png)

7. `String`对象有一个重要的属性`private final char value[];`用来存储字符串
8. `final value` 在赋值以后，地址是不可修改的


————————————————————————————————

* `String` 赋值的两种方式
  * 直接常量赋值`String str = "hello world~"`
  * 通过构造器`String str = new String("Hello World~")`

**关于两种方式的赋值：**
第一种直接赋值，是首先去常量池中查找是否有这个地址空间，有就直接返回指向，没有就在常量池创建，最后指向，返回的是常量池的地址空间
第二种构造器,先在堆中创建空间，里面维护了`value`属性，指向常量池的地址空间，如果常量池没有，在常量池中创建，如果有，直接通过`value`指向，最终指向的是堆中的地址空间


**************

* 初始化一个`String`类型的数据后，再次修改这个变量，会在内存中再次创建一个对象

![string-03](https://qiniu.lglg.xyz/images/lglg/posts/202206/26/string-03.png)

```java
class Test {
    public static void main(String[] args) {
        String str1 = "Hello";
        String str2 = "World";
        
        // 创建一个StringBuilder sb
        // sb.append("Hello")
        // sb.append("World")     
        // new String()
        String str3 = str1 + str2;
    }
}
```

![string-04](https://qiniu.lglg.xyz/images/lglg/posts/202206/26/string-04.png)


**************

###### `StringBuffer`代表可变的字符序列（可变长度），可以对字符串内容进行增删

![string-05](https://qiniu.lglg.xyz/images/lglg/posts/202206/26/string-05.png)

1. `StringBuffer` 是一个被`final`修改的类，该类不能被继承
2. 实现了`Serializable`接口，可被串行化
3. 它继承自`AbstractStringBuilder`
4. `AbstractStringBuilder`类中有属性`char[] value;`用来存放字符串内容，它里面的内容存放在堆中
5. 字符内容存放在`char[] value;`中，每次增加或修改内容，不用每次更改地址空间（每次重新创建对象），效率高 [数组扩容]

* `String` 转为 `StringBuffer`

```java
class Test {
    public static void main(String[] args) {
        String str = "hello";
        
        // 方式一
        StringBuffer sb = new StringBuffer(str);
        
        // 方式二
        StringBuffer stringBuffer1 = new StringBuffer();
        stringBuffer1.append(str);
    }
}
```

* `StringBuffer` 转为 `String`

```java
class Test {
    public static void main(String[] args) {
        StringBuffer stringBuffer = new StringBuffer("Jay");
        
        // 方式一
        String s1 = stringBuffer.toString();
        
        String s2 = new String(stringBuffer);
    }
}
```


**********************

##### `StringBuilder`代表可变的字符序列（可变长度），可以对字符串内容进行增删

1. `StringBuilder`类关系和`StringBuffer`一样，都是`AbstractStringBuilder`子类
2. `StringBuilder` 是非线程安全的
3. `StringBuilder`速度更快，效率更高，但是多线程中非安全，单线程中效果最好


---------------------

> 关于 `String` `StringBuffer` `StringBuilder` 之间的比较

1. 字符串存在大量修改的情况下：一般使用`StringBuffer` `StringBuilder`
2. 字符串存在大量修改的情况下：单线程`StringBuilder`
3. 字符串存在大量修改的情况下：多线程`StringBuffer`
4. 字符串很少修改的，被多个对象引用，使用`String`