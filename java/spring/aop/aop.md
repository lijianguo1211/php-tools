### spring框架学习三

###### 静态代理

> java 规定了数据查询的接口，各个数据库厂商根据接口规定实现自己的数据操作，用户在调用这些数据操作时，想要给各个方法添加一些前置或者后置处理

* 接口 ``Db``

```java
public interface Db {
    public Object execQuery(String sql);
    
    public int execUpdate(String sql);
    
    public Object exec(String sql);
}
```

* Mysql 数据库厂商的实现

```java
public class MysqlDb implements Db{
    @Override
    public Object execQuery(String sql) {
        return new Object() {
            @Override
            public String toString() {
                return "select: " + sql;
            }
        };
    }

    @Override
    public int execUpdate(String sql) {
        System.out.println("execUpdate: " + sql);
        return 0;
    }

    @Override
    public Object exec(String sql) {
        return new Object() {
            @Override
            public String toString() {
                return "exec: " + sql;
            }
        };
    }
}

```

* 用户现在想要添加前置或者是后置的操作,不改变原来代码的情况下，使用代理类

```java
public class MysqlProxy implements Db {

    private MysqlDb mysqlDb;

    public void setMysqlDb(MysqlDb mysqlDb) {
        this.mysqlDb = mysqlDb;
    }

    @Override
    public Object execQuery(String sql) {

        before("execQuery");
        final Object o = mysqlDb.execQuery(sql);

        return o;
    }

    @Override
    public int execUpdate(String sql) {
        before("execQuery");
        final int i = mysqlDb.execUpdate(sql);
        return i;
    }

    @Override
    public Object exec(String sql) {
        before("execQuery");
        final Object exec = mysqlDb.exec(sql);
        return exec;
    }

    public void before(String msg) {
        System.out.println(msg +": 添加前置操作");
    }

    public void after(String msg) {
        System.out.println(msg + ": 添加后置操作");
    }
}
```

* 最后调用测试

```java
public class Client {
    public static void main(String[] args) {
        MysqlDb mysqlDb = new MysqlDb();

        MysqlProxy mysqlProxy = new MysqlProxy();

        mysqlProxy.setMysqlDb(mysqlDb);


        System.out.println(mysqlProxy.execQuery("select * from users"));
    }
}
```

> 这里的代理是基于接口实现，代理类和被代理类都实现了共同的接口，用户调用的时候，只需要调用代理类，添加代码也是修改代理类，不会去改动被代理的类。

###### 动态代理

* 基于jdk（接口）实现的动态代理，需要使用到java反射下面的两个包`java.lang.reflect.InvocationHandler | java.lang.reflect.Proxy`

> `InvocationHandler` 这是一个接口,需要客户端用实现`invoke`方法，处理代理实例，并返回结果

```java
import java.lang.reflect.InvocationHandler;
import java.lang.reflect.Method;
import java.lang.reflect.Proxy;

public class ProxyInvocationHandler implements InvocationHandler {

    /**
     * 被代理的接口
     */
    private Object target;

    public void setTarget(Object target) {
        this.target = target;
    }

    // 处理代理实例，并返回结果
    @Override
    public Object invoke(Object proxy, Method method, Object[] args) throws Throwable {

        log(method.getName());

        Object invoke = method.invoke(target, args);

        return invoke;
    }

    // 生成得到代理类
    public Object getProxy() {
        return Proxy.newProxyInstance(this.getClass().getClassLoader(), target.getClass().getInterfaces(), this);
    }

    // 添加的前置后者是后置操作
    public void log(String msg) {
        System.out.println("[INFO] 执行了" + msg + "() 方法");
    }
}
```

* 调用动态代理测试

```java
class Client {
    public static void main(String[] args) {
        MysqlDb mysqlDb = new MysqlDb();

        ProxyInvocationHandler proxyInvocationHandler = new ProxyInvocationHandler();

        proxyInvocationHandler.setTarget(mysqlDb);

        Db proxy = (Db)proxyInvocationHandler.getProxy();

        Object exec = proxy.exec("select * from users order by id asc limit 10");

        System.out.println(exec);
    }
}
```

**************


#### **AOP**-Aspect Oriented Programming 面向切面编程，通过预编译方式和运行期间动态代理实现程序功能的统一维护的一种技术

> OP是OOP的延续，是软件开发中的一个热点，也是Spring框架中的一个重要内容，是函数式编程的一种衍生范型。利用AOP可以对业务逻辑的各个部分进行隔离，
> 从而使得业务逻辑各部分之间的耦合度降低，提高程序的可重用性，同时提高了开发的效率。

| title |                     description                      |
|:------|:----------------------------------------------------:|
| 横切关注点 | 跨越应用程序多个模块的方法或功能，（与业务逻辑无关，但是需要关注的部分，如：日志，安全，缓存，事务等）  |
| 切面    |                 横切关注点 被模块化的特殊对象 （类）                  |
| 通知    |                 切面必须要完成的工作（类中的一个方法）                  |
| 目标    |                        被通知对象                         |
| 代理    |                   向目标对象应用通知之后创建的对象                   |
| 切入点   |                   切面通知执行的**地点**的定义                   |
| 连接点   |                      与切入点匹配的执行点                      |


* 环境要求：

```xml
<!--需要安装的包-->
<dependency>
    <groupId>org.aspectj</groupId>
    <artifactId>aspectjweaver</artifactId>
    <version>1.9.9.1</version>
</dependency>
```

* 添加配置文件修改（添加约束）

```xml
<?xml version="1.0" encoding="UTF-8"?>
<!--xmlns:aop="http://www.springframework.org/schema/aop"-->
<!--http://www.springframework.org/schema/aop http://www.springframework.org/schema/aop/spring-aop.xsd-->
<beans xmlns="http://www.springframework.org/schema/beans"
       xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
       xmlns:aop="http://www.springframework.org/schema/aop"
       xsi:schemaLocation="http://www.springframework.org/schema/beans http://www.springframework.org/schema/beans/spring-beans.xsd http://www.springframework.org/schema/aop http://www.springframework.org/schema/aop/spring-aop.xsd">
</beans>
```

* 通过 `spring api` 去实现
  * `org.springframework.aop.MethodBeforeAdvice` 添加前置操作
  * `org.springframework.aop.AfterReturningAdvice` 添加后置操作

```java
public class Log implements MethodBeforeAdvice {

    /**
     *
     * @param method 要执行的目标对象的方法
     * @param args 参数
     * @param target 目标对象
     * @throws Throwable
     */
    @Override
    public void before(Method method, Object[] args, Object target) throws Throwable {
        System.out.println(target.getClass().getName() + " 的 " + method.getName() + " 方法执行了~");
    }
}
public class AfterLog implements AfterReturningAdvice {
    /**
     * 
     * @param returnValue 方法的返回值
     * @param method
     * @param args
     * @param target
     * @throws Throwable
     */
    @Override
    public void afterReturning(Object returnValue, Method method, Object[] args, Object target) throws Throwable {
        System.out.println("执行了 " + method.getName() + " 方法，返回结果为：" + returnValue);
    }
}
```

* 在配置文件中添加配置

```xml
<!--  配置aop  -->
<!-- 使用spring api 接口   -->
    <aop:config>
<!--   切入点  execution:表达式，execution(要执行的位置 * * * * *)   -->
<!--  execution:表达式分为五个部分 返回值 包 类 方法 参数   -->
<!--  * 任意返回值   -->
<!--  org.jay.service.UserServiceImpl 具体的那个类   -->
<!--  * 任意方法   -->
<!--  (..) 任意参数   -->
        <aop:pointcut id="pointcut" expression="execution(* org.jay.service.UserServiceImpl.*(..))"/>
<!--   执行环绕增加     -->
        <aop:advisor advice-ref="log" pointcut-ref="pointcut"></aop:advisor>
        <aop:advisor advice-ref="afterLog" pointcut-ref="pointcut"></aop:advisor>
    </aop:config>
```

* 通过自定义类去实现
  * 定义一个需要切入方法
  * 修改配置文件

```java
public class DiyLog {

    public void before() {
        System.out.println("######方法执行之前######");
    }

    public void after() {
        System.out.println("######方法执行之后######");
    }
}
```

```xml
<bean id="diyLog" class="org.jay.diy.DiyLog"></bean>
<aop:config>
    <aop:aspect ref="diyLog">
        <aop:pointcut id="pointcut" expression="execution(* org.jay.service.UserServiceImpl.*(..))"/>

        <aop:before method="before" pointcut-ref="pointcut"></aop:before>
        <aop:after method="after" pointcut-ref="pointcut"></aop:after>
    </aop:aspect>
</aop:config>
```

* 使用注解实现切面
  * 开启注解支持
  * 定义一个切面类 `org.aspectj.lang.annotation.Aspect`
  * 定义具体的切入点

```xml
<aop:aspectj-autoproxy></aop:aspectj-autoproxy>
```

```java
@Aspect
public class AnnotationPointCut {
  @Before("execution(* org.jay.service.UserServiceImpl.*(..))")
  public void before() {
    System.out.println("######方法执行之前######");
  }

  @After("execution(* org.jay.service.UserServiceImpl.*(..))")
  public void after() {
    System.out.println("######方法执行之后######");
  }

  @Around("execution(* org.jay.service.UserServiceImpl.*(..))")
  public void around(ProceedingJoinPoint joinPoint) throws Throwable {
    System.out.println("环绕前：");

    Object proceed = joinPoint.proceed();

    System.out.println("环绕后：");

    System.out.println(proceed);
  }
}
```

* 返回结果

```
环绕前：
######方法执行之前######
增加一个用户
######方法执行之后######
环绕后：
```