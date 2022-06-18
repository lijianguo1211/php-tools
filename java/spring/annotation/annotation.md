### spring框架学习二

* 在配置文件中开启注解

```xml
<?xml version="1.0" encoding="UTF-8"?>
<beans xmlns="http://www.springframework.org/schema/beans"
       xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
       xmlns:context="http://www.springframework.org/schema/context"
       xmlns:aop="http://www.springframework.org/schema/aop"
       xsi:schemaLocation="http://www.springframework.org/schema/beans
       http://www.springframework.org/schema/beans/spring-beans.xsd
       http://www.springframework.org/schema/context
       http://www.springframework.org/schema/context/spring-context.xsd
       http://www.springframework.org/schema/aop
       http://www.springframework.org/schema/aop/spring-aop.xsd">
    <!-- 开启注解支持   -->
    <context:annotation-config />
</beans>
```

* 在配置文件中指定扫描那些包，使之这些包中的注解生效

```xml
<?xml version="1.0" encoding="UTF-8"?>
<beans xmlns="http://www.springframework.org/schema/beans"
       xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
       xmlns:context="http://www.springframework.org/schema/context"
       xmlns:aop="http://www.springframework.org/schema/aop"
       xsi:schemaLocation="http://www.springframework.org/schema/beans
       http://www.springframework.org/schema/beans/spring-beans.xsd
       http://www.springframework.org/schema/context
       http://www.springframework.org/schema/context/spring-context.xsd
       http://www.springframework.org/schema/aop
       http://www.springframework.org/schema/aop/spring-aop.xsd">

<!--  指定需要扫描的包，这个包下的注解就会生效  -->
    <context:component-scan base-package="org.jay.pojo"></context:component-scan>
    <context:component-scan base-package="org.jay.dao"></context:component-scan>
    <context:component-scan base-package="org.jay.service"></context:component-scan>
    <context:component-scan base-package="org.jay.controller"></context:component-scan>
</beans>
```

* `@Component` 注册一个类，等价于 `<bean id="people" class="org.jay.pojo.People" scope="prototype"></bean>`

```java
@Component
@Scope("prototype")
public class User {

    @Value("Jay")
    private String name;

    @Value("18")
    private int age;

    @Override
    public String toString() {
        return "User{" +
                "name='" + name + '\'' +
                ", age=" + age +
                '}';
    }
}
```

> 和`Component`作用相同的注解 `org.springframework.stereotype.Repository` `org.springframework.stereotype.Controller` `org.springframework.stereotype.Service`

* 给初始化的属性赋值 ``org.springframework.beans.factory.annotation.Value`` 等价于 `<property name="name" value="jay"></property>`

* 设置一个类的作用域 ``org.springframework.context.annotation.Scope`` 等价于 ` <bean id="people" class="org.jay.pojo.People" scope="prototype"></bean>`

##### 使用 ``javaConfig``代替xml配置`spring`

* 创建一个配置文件，`Configuration`标记这个java类,注册到spring容器中，代表这是一个配置类

* 在方法中使用 ``Bean``标记,相等于之前xml文件中bean标签

```java
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;

@Configuration
public class AppConfig {

    // user 方法名 相当于 id 属性
    // User 方法返回值 相当于 class 属性
    @Bean
    public User user() {
        return new User(); // 返回要注入到bean的对象
    }
}
```

* 加载配置文件

```java
public class Main {
    public static void main(String[] args) {
        ApplicationContext context = new AnnotationConfigApplicationContext(AppConfig.class);

        User user = context.getBean("user", User.class);

        System.out.println(user.getName());
    }
}
```

****************

* ``org.springframework.context.annotation.ComponentScan`` 扫描包

```java
import org.jay.pojo.User;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.ComponentScan;
import org.springframework.context.annotation.Configuration;

@Configuration
@ComponentScan("org.jay.pojo")
public class PojoConfig {
    @Bean
    public User user() {
        return new User();
    }
}
```

* ``org.springframework.context.annotation.Import`` 引入配置文件

```java
import org.springframework.context.annotation.Configuration;
import org.springframework.context.annotation.Import;

@Configuration
@Import(PojoConfig.class)
public class AppConfig {


}
```