### java之内部类

**java内部类**

* 成员内部类(普通内部类)
* 静态内部类
* 方法内部类(定义在外部类方法中的内部类，也称之为局部内部类)
* 匿名内部类

-------
* 成员内部类示例：

```java
public class Person {

    public int age;

    public Heart getHeart() {
        return new Heart();
    }

    public class Heart {
        public String beat() {
            return age + " 岁的心脏跳动~";
        }
    }
}
```

1. 访问内部类

```java
class Test {
    public static void main(String[] args) {
        Person person = new Person();
        // 通过外部类中方法返回
        person.getHeart();
        // 直接通过外部类new
        Person.Heart heart = new Person().new Heart();
        // 通过已经实例化后的外部类new
        Person.Heart heart1 = person.new Heart();
    }
}
```

2. 内部类中可以使用访问修饰符`default public protected private`
3. 外部类可以直接获取外部类的属性方法
4. 外部类和内部类属性方法同名时，默认是使用内部类的，想要使用外部类的，需要添加`外部类.this.属性|方法`
5. 外部类想要访问内部类信息，需要通过内部类示例
6. 内部类编译后的文件是：`外部类$内部类.class`

* 静态内部类示例：

```java
public class Person {

    public static int age;

    public void getHeart() {
        Heart.eat();
    }

    public static class Heart {
        public static void eat() {
            System.out.println("吃东西吧~");
        }
        public String beat() {
            return Person.age + " 岁的心脏跳动~";
        }
    }
}
```

1. 使用静态内部类

```java
class Test {
    public static void main(String[] args) {
        Person.Heart heart1 = new Person.Heart();
    }
}
```

2. 静态内部类是可以直接访问外部类的静态属性
3. 静态内部类需要调用外部类非静态属性方法时，需要实例化外部类之后调用
4. 外部类中直接调用静态内部类中的静态方法属性`Heart.eat()`
5. 外部类中调用静态内部类中的非静态方法属性`new Heart().beat()`
6. 在类外部中调用静态内部类静态属性方法` Person.Heart.eat()`

* 方法内部类示例：

```java
public class Person {
    public static int age;

    public Object getHeart() {
        class Heart {
            public String beat() {
                return Person.age + " 岁的心脏跳动~";
            }
        }
        return new Heart();
    }
    
}
```
1. 调用

```java
class Test {
    public static void main(String[] args) {
        Person person = new Person();

        Object heart = person.getHeart();

        System.out.println(heart.getClass());
        
        // class org.jay.enums.innerClass.Person$1Heart
    }
}
```

2. 定义在方法内部，作用范围也在方法内
3. class前面不可以添加`public | private | protected | static`
4. 类中不能包含静态成员
5. 类中可以包含`final | abstract` 修饰成员

* 匿名内部类示例：

```java
public abstract class Person {
    private String name;

    public Person() {

    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public abstract void read();
}
```

1. 使用：

```java
class Test {
    public static void main(String[] args) {
        getRead(new Person() {
            @Override
            public void read() {
                System.out.println("看科幻书~");
            }
        });


        getRead(new Person() {
            @Override
            public void read() {
                System.out.println("看文学书~");
            }
        });
    }

    public static void getRead(Person person) {
        person.read();
    }
}
```

2. 编译后的class文件是：`使用类$数字.class`
3. 匿名内部类没有类型名称，示例对象
4. 无法使用`public | private | protected | static | abstract`修改
5. 无法编写构造方法，但是可以使用构造代码块
6. 不能出现静态成员
7. 匿名内部类可以实现接口或者继承父类，二者不可同时出现

*******

* 接口中的内部类

```java
public interface PersonInterface {

    int TEMP = 10;

    void fun1();

    public default void fun2() {
        System.out.println("接口中默认方法");
    }

    public static void fun3() {
        System.out.println("接口中静态方法");
    }
    public class InnerClass1 {
        public void fun4() {
            System.out.println("接口中定义的普通成员内部类~");
        }
    }

    public abstract class InnerClass2 {
        public abstract void fun5();

        public void fun6() {
            System.out.println("接口中定义的抽象成员内部类~");
        }
    }
}
```

1. 类实现这个接口

```java
public class Person implements PersonInterface{
    @Override
    public void fun1() {
        System.out.println("实现类~");
    }

    public InnerClass1 getInnerClass1() {
        return new InnerClass1();
    }

    public class InnerClass3 extends InnerClass2 {

        @Override
        public void fun5() {
            System.out.println("重写接口中内部抽象类的抽象方法~");
        }
    }
}
```

2. 调用：

```java
import org.jay.innerClass.Person;
import org.jay.innerClass.PersonInterface;
import org.jay.innerClass.PersonInterface.InnerClass1;

class Test {
    public static void main(String[] args) {
        // 接口名.类名实例化
        PersonInterface.InnerClass1 innerClass1 = new PersonInterface.InnerClass1();

        innerClass1.fun4();

        // 通过方法
        Person person = new Person();
        person.getInnerClass1().fun4();

        // import 导入接口中的内部类，直接实例化
        InnerClass1 innerClass11 = new InnerClass1();
        innerClass11.fun4();

        // 通过匿名内部类实例化接口中的抽象内部类
        PersonInterface.InnerClass2 innerClass2 = new PersonInterface.InnerClass2() {

            @Override
            public void fun5() {
                System.out.println("重写接口中内部抽象类的抽象方法~");
            }
        };
        innerClass2.fun5();
        innerClass2.fun6();

        // 通过实现类调用
        Person.InnerClass3 innerClass3 = new Person().new InnerClass3();
        innerClass3.fun5();
        innerClass3.fun6();
    }
}
```