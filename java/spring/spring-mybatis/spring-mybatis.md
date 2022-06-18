### Spring整合mybatis

* 需要整合（安装）的扩展包

```xml
<dependencies>
    <dependency>
        <groupId>mysql</groupId>
        <artifactId>mysql-connector-java</artifactId>
        <version>8.0.29</version>
    </dependency>

    <dependency>
        <groupId>org.mybatis</groupId>
        <artifactId>mybatis</artifactId>
        <version>3.5.10</version>
    </dependency>
    <!-- https://mvnrepository.com/artifact/org.mybatis/mybatis-spring -->
    <dependency>
        <groupId>org.mybatis</groupId>
        <artifactId>mybatis-spring</artifactId>
        <version>2.0.7</version>
    </dependency>
    <dependency>
        <groupId>org.springframework</groupId>
        <artifactId>spring-webmvc</artifactId>
        <version>5.3.20</version>
    </dependency>

    <dependency>
        <groupId>org.springframework</groupId>
        <artifactId>spring-jdbc</artifactId>
        <version>5.3.20</version>
    </dependency>

    <dependency>
        <groupId>org.aspectj</groupId>
        <artifactId>aspectjweaver</artifactId>
        <version>1.9.9.1</version>
    </dependency>
</dependencies>
```

**mybatis-spring文档**[mybatis-spring](http://mybatis.org/spring/zh/index.html)

* 通过XML文件配置(把mybatis配置整合到spring配置中)

```xml
<?xml version="1.0" encoding="UTF-8"?>
<beans xmlns="http://www.springframework.org/schema/beans"
       xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
       xsi:schemaLocation="http://www.springframework.org/schema/beans
        http://www.springframework.org/schema/beans/spring-beans.xsd">
<!--配置一个数据源-->
    <bean id="dataSource" class="org.springframework.jdbc.datasource.DriverManagerDataSource">
        <property name="driverClassName" value="com.mysql.cj.jdbc.Driver"></property>
        <property name="url" value="jdbc:mysql://localhost:3306/test_blog?useSSL=true&amp;sueUnicode=true&amp;characterEncode=UTF-8"></property>
        <property name="username" value="root"></property>
        <property name="password" value="root"></property>
    </bean>
    <!--初始化sqlSessionFactory类-->
    <bean id="sqlSessionFactory" class="org.mybatis.spring.SqlSessionFactoryBean">
        <property name="dataSource" ref="dataSource" />
<!--   mybatis 配置文件     -->
        <property name="configLocation" value="classpath:mybatis-config.xml"></property>
<!--   mybatis Mapper配置文件     -->
        <property name="mapperLocations" value="classpath:mapper/*Mapper.xml"></property>
    </bean>
<!--初始化sqlSession-->
    <bean id="sqlSession" class="org.mybatis.spring.SqlSessionTemplate">
        <constructor-arg index="0" ref="sqlSessionFactory"></constructor-arg>
    </bean>
</beans>
```

* 创建接口，实体类，`mapper.xml`文件

```java
public interface UserMapper {
    public List<User> queryUser();

    public int addUser(User user);

    public int delUser(int id);

    public int updateUser(User user);
}
```

* 通过继承接口去实现数据操作
  * 通过继承`org.mybatis.spring.support.SqlSessionDaoSupport`得到`sqlSession`
  * 通过自定义去`new`一个`org.mybatis.spring.SqlSessionTemplate`

```java
//方式一：

public class UserMapperImpl2 extends SqlSessionDaoSupport implements UserMapper {
    @Override
    public List<User> queryUser() {
        return getSqlSession().getMapper(UserMapper.class).queryUser();
    }

    @Override
    public int addUser(User user) {
        return getSqlSession().getMapper(UserMapper.class).addUser(user);
    }

    @Override
    public int delUser(int id) {
        return getSqlSession().getMapper(UserMapper.class).delUser(id);
    }

    @Override
    public int updateUser(User user) {
        return getSqlSession().getMapper(UserMapper.class).updateUser(user);
    }
}

//方式二：
public class UserMapperImpl implements UserMapper {

    private SqlSessionTemplate sqlSessionTemplate;

    public void setSqlSessionTemplate(SqlSessionTemplate sqlSessionTemplate) {
        this.sqlSessionTemplate = sqlSessionTemplate;
    }

    @Override
    public List<User> queryUser() {
        UserMapper mapper = sqlSessionTemplate.getMapper(UserMapper.class);

        List<User> users = mapper.queryUser();

        return users;
    }
}
```

* 加载spring的xml配置文件

```java
class Client {
    public static void main(String[] args) {
        ClassPathXmlApplicationContext classPathXmlApplicationContext = new ClassPathXmlApplicationContext("beans.xml");

        UserMapperImpl userMapper = classPathXmlApplicationContext.getBean("userMapper", UserMapperImpl.class);

        List<User> users = userMapper.queryUser();

        System.out.println(users);
    }
}
```

* 关于开启事务

```xml
<!--添加事务约束-->
<!--xmlns:tx="http://www.springframework.org/schema/tx"-->
<!--http://www.springframework.org/schema/tx-->
<!--http://www.springframework.org/schema/tx/spring-tx.xsd-->
<!--添加aop约束-->
<!--xmlns:aop="http://www.springframework.org/schema/aop"-->
<!--http://www.springframework.org/schema/aop-->
<!--http://www.springframework.org/schema/aop/spring-aop.xsd-->

<!--    在 Spring 的配置文件中创建一个 DataSourceTransactionManager 对象-->
<bean id="transactionManager" class="org.springframework.jdbc.datasource.DataSourceTransactionManager">
    <constructor-arg ref="dataSource" />
</bean>
```

* 事务的声明方式
  * 编程式事务管理
  * 声明式事务

```java
// 编程式事务管理
// 方式一：
public class UserService {
    private final PlatformTransactionManager transactionManager;
    public UserService(PlatformTransactionManager transactionManager) {
        this.transactionManager = transactionManager;
    }
    public void createUser() {
        TransactionStatus txStatus =
                transactionManager.getTransaction(new DefaultTransactionDefinition());
        try {
            userMapper.insertUser(user);
        } catch (Exception e) {
            transactionManager.rollback(txStatus);
            throw e;
        }
        transactionManager.commit(txStatus);
    }
}

// 方式二：
public class UserService {
    private final PlatformTransactionManager transactionManager;
    public UserService(PlatformTransactionManager transactionManager) {
        this.transactionManager = transactionManager;
    }
    public void createUser() {
        TransactionTemplate transactionTemplate = new TransactionTemplate(transactionManager);
        transactionTemplate.execute(txStatus -> {
            userMapper.insertUser(user);
            return null;
        });
    }
}
```

* 结合aop实现事务的植入

```xml
<!--配置事务通知-->
<tx:advice id="txAdvice" transaction-manager="transactionManager">
  <!--  给那些方法配置事务 -->
  <tx:attributes>
<!-- read-only 只读   -->
    <tx:method name="queryUser" read-only="true"/>
<!-- propagation 事务的传播特性   -->
    <tx:method name="delUser" propagation="REQUIRED"/>
    <tx:method name="addUser" propagation="REQUIRED"/>
    <tx:method name="updateUser" propagation="REQUIRED"/>
<!-- 所有方法   -->
    <tx:method name="*" propagation="REQUIRED"/>
    
  </tx:attributes>
</tx:advice>

<!--  配置事务切入  -->
<aop:config>
<aop:pointcut id="txPointCut" expression="execution(* org.jay.dao.*.*(..))"/>
<aop:advisor advice-ref="txAdvice" pointcut-ref="txPointCut"></aop:advisor>
</aop:config>
```