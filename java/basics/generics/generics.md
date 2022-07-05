### java基础泛型

**什么是泛型**

> Java 泛型（generics）是 JDK 5 中引入的一个新特性, 泛型提供了编译时类型安全检测机制，该机制允许程序员在编译时检测到非法的类型。 泛型的本质是参数化类型，也就是说所操作的数据类型被指定为一个参数。

为什么使用泛型，如果没有泛型会有什么问题？**java**是强类型的语言，定义一种数据，就必须为其指定一种类型。当然java中所有类都继承自`Object`对象，不指
定具体某个数据类型时，可以把它当作`Object`类型，比如：

```java
class Test {
    
    class A {
        void fun1() {
            System.out.println("fun1~");
        }
    }
    
    public static void main(String[] args) {
        // 向 list 集合中添加数据
        ArrayList arrayList = new ArrayList();
        arrayList.add(1);// Integer
        arrayList.add("string");// String
        arrayList.add(2f); // Float
        arrayList.add(3d); // Double
        arrayList.add(4l); // Long
        arrayList.add('a');// Character
        arrayList.add(new A());// A
        
        // 遍历时,不知道具体的类型是什么，就可以指定为 Object
        // 问题是：如果需要调用 A 对象的方法，使用 Object 是没办法直接调用的，只有向下转型之后，才可以调用A类的具体方法
        // 这样就很麻烦
        for (Object o:arrayList) {
            
            if (o instanceof A) {
                // 向下转型
                A a = (A) o;
                a.fun1();
            } else {
                System.out.println(o);
            }
            
            
        }
        
    }
}
```

再比如，没有泛型支持，`ArrayList`类就只能是添加`Object`（添加数据时向上转型-把添加的数据都转化为`Object`类型，遍历的取数据时再向下转型）造成代码
量增加，性能消耗等等;或者是为了避免转型的情况发生，就需要定义N多不同类型的`ArrayList`:

```java
class StringArrayList {
    private String[] array;
}
class IntegerArrayList {
    private Integer[] array;
}
class CharacterArrayList {
    private Character[] array;
}

class ObjectArrayList {
    private Object[] array;
}
// ....
```

这样为每种不同的类型创建不同类，确实是可以解决转型的问题，但是会造成代码成倍的剧增，而且jdk中的类是汪洋大海.但是有了泛型就可以很优雅解决这个问题

--------

**java中泛型的标记**（不做强制约定，任何字母都可以做为标记）

* `E Element` 集合中使用
* `T Type` java类
* `K Key` 键
* `V Value` 值
* `N Number` 数值类型
* `?` 可以是任意类型（不确定的类型）

**泛型接口**

* 普通示例

```java
public interface Iterable<T> {

    Iterator<T> iterator();
}
```

* 继承泛型接口,可以在`Abc1`继承接口`Abc`指定具体的类型，在创建类`Abc2`后，实现方法时就可以时具体的类型

```java
interface Abc<E> {
    E get();
}

interface Abc1<B> extends Abc<String> {
    B getB();
}

class Abc2 implements Abc1<Integer> {

    @Override
    public String get() {
        return null;
    }

    @Override
    public Integer getB() {
        return null;
    }
}
```

* 在泛型接口中,是不可以直接设置泛型常量的。

```java
interface AAA<E> {
    E a; // 这是不允许
    static E b; // 这是不允许
}
```

**泛型类**

* 声明一个普通的泛型类

```java
class TestA<T> {
    T t;

    public TestA(T t) {
        this.t = t;
    }

    public T getT() {
        return t;
    }

    public void setT(T t) {
        this.t = t;
    }
}

class Test {
    public static void main(String[] args) {
        // 真正使用时才会去根据传入的类型确定
        TestA<Integer> test1 = new TestA(1);
        TestA<String> test2 = new TestA("str");
        TestA<Character> test3 = new TestA('a');
    }
}
```


**泛型方法**

* 普通的泛型方法

```java
class TestB<E> {
    // 传递类型为T的参数
    public <T> void test(T t) {

    }

    public <T> void test1(T t, E e) {

    }

    public <T, E> void test3(T t) {

    }

    // 泛型方法
    public <T> T getClass(Class<T> t) throws InstantiationException, IllegalAccessException {

        return t.newInstance();
    }
}

class Test {

    class A {

    }

    public static void main(String[] args) {
        TestB<String> stringTestB = new TestB<String>();

        stringTestB.test(1); // 根据传入的值推导类型
        A a = new A();
        stringTestB.test(a); // 传入的就是A
        stringTestB.test1("hello", "world");// 第一个参数是传入时确定，第二个参数是初始化对象时确定
        stringTestB.test1("java");

        Object aClass = stringTestB.getClass(Class.forName("A.class"));
    }
}
```

* 使用泛型的方法

```java
class TestC<E> {
    public void test(T t) {

    }
}
```


**泛型上下限**

* 上限 ``<? extends Number>``,支持Number及Number的子类
* 下限 ``<? supper Integer>``,支持Integer类及Integer父类
* 无限 ``<?>``

```java
class A1 {

}

class A2 extends A1 {

}

class A3 extends A2 {

}

class Jay {
    public static void main(String[] args) {

        // 可以是任意类型的
        test1(new ArrayList<A1>());
        test1(new ArrayList<String>());
        test1(new ArrayList());

        // ----
        test2(new ArrayList<A1>());
        test2(new ArrayList<A2>());
        test2(new ArrayList<A3>());
// Object Number 都不是 A1的子类
//        test2(new ArrayList<Object>());
//        test2(new ArrayList<String>());

        // -----
        test3(new ArrayList<A3>());
        test3(new ArrayList<A2>());
        test3(new ArrayList<A1>());
        // Object是所有类的父类
        test3(new ArrayList<Object>());
        // String Number 都是不是 A3的父类
//        test3(new ArrayList<String>());
//        test3(new ArrayList<Number>());

    }

    public static void test1(List<?> param) {

    }

    // 上限,A1及A1的子类
    public static void test2(List<? extends A1> a) {

    }

    // 下线 A3及A3的父类
    public static void test3(List<? super A3> a) {

    }
}
```




