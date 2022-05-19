### java 操作数据库(mysql)

#### 驱动类 ``Driver``

```java
class Test {
    public static void main(String[] args) {
        Driver driver = new com.mysql.jdbc.Driver();
    }
}
```

#### 驱动管理类 ``DriverManager``

```java
class Test {
    public static void main(String[] args) {
        //获取连接
        Connection connection = DriverManager.getConnection(url, user, password);
    }
}
```

#### 连接类 ``Connection``

```java
class Test {
    public static void main(String[] args) {
        Driver driver = new com.mysql.jdbc.Driver();

        Connection connect = driver.connect(url, properties);
        //创建 Statement 对象
        Statement statement = connection.createStatement();
        //生成预处理对象
        PreparedStatement preparedStatement = connection.prepareStatement(sql);
    }
}
```

#### ``Statement``接口

```java
class Test {
    public static void main(String[] args) {
        Driver driver = new com.mysql.jdbc.Driver();

        Connection connect = driver.connect(url, properties);
        //创建 Statement 对象
        Statement statement = connection.createStatement();

        //执行DML语句返回受影响的行数
        statement.executeUpdate(sql);
        //执行查询，返回ResultSet对象
        statement.executeQuery(sql);
        //任意sql执行，返回bool值
        statement.execute(sql);
    }
}
```

#### ``PreparedStatement``接口

```java
class Test {
    public static void main(String[] args) {
        Driver driver = new com.mysql.jdbc.Driver();

        Connection connect = driver.connect(url, properties);
        //预处理对象
        PreparedStatement preparedStatement = connection.prepareStatement(sql);
        //执行DML
        preparedStatement.executeUpdate();
        //执行查询，返回ResultSet对象
        preparedStatement.executeQuery();
        //任意SQL，返回bool值
        preparedStatement.execute();
        //绑定预处理数据，索引从1开始
        preparedStatement.setXXX('索引', '绑定值');
        preparedStatement.setObject('索引', '绑定值');
        
    }
}
```

#### ``ResultSet`` 查询结果集

```
ResultSet resultSet = preparedStatement.executeQuery();
//向后移动一行，如果没有，返回false
resultSet.next();
//向前移动一行
resultSet.previous();
//得到数据
resultSet.getXXX('index')//索引
resultSet.getXXX('column')//列名
resultSet.getObject('column|index')//列名
```

#### 事务 `transcantion`

```
Driver driver = new com.mysql.jdbc.Driver();

Connection connect = driver.connect(url, properties);

//开启事务
connect.setAutoCommit(false);

//提交事务
connect.commit();
//事务回滚
connect.rollback();
```

#### 批量增加数据 `batch`

````
Driver driver = new com.mysql.jdbc.Driver();

Connection connect = driver.connect(url, properties);
//预处理对象
PreparedStatement preparedStatement = connection.prepareStatement(sql);

for (int i = 1; i < 30000; i++) {
    preparedStatement.setString(1, "AA" + i)
    preparedStatement.addBatch();
    if ((i + 1) % 1000 == 0) {
        preparedStatement.executeBatch();

        preparedStatement.clearBatch();
    }
}
````

#### 连接池 `pool | DataSource`

```java
//C3P0 速度相对较慢，稳定性好
//DBCP 速度想比c3p0快，但不稳定
//Proxool 有监控连接池状态的功能，稳定性较c3p0差一点
//BoneCP 速度快
//Druid 阿里提供的，集DBCP，C3P0,Proxool优点于一起的连接池
```

##### C3P0 连接池

```java
//@link https://jaist.dl.sourceforge.net/project/c3p0/c3p0-bin/c3p0-0.9.5.5/c3p0-0.9.5.5.bin.zip
public class C3P0_ {

    @Test
    public void testM1() throws IOException, PropertyVetoException, SQLException {
        ComboPooledDataSource comboPooledDataSource = new ComboPooledDataSource();

        Properties properties = new Properties();
        properties.load(new FileReader("src\\mysql.properties"));

        String user = properties.getProperty("user");
        String password = properties.getProperty("password");
        String url = properties.getProperty("url");
        String driver = properties.getProperty("driver");

        //设置驱动
        comboPooledDataSource.setDriverClass(driver);
        //设置链接
        comboPooledDataSource.setJdbcUrl(url);
        //设置用户
        comboPooledDataSource.setUser(user);
        //设置密码
        comboPooledDataSource.setPassword(password);
        //初始化连接数
        comboPooledDataSource.setInitialPoolSize(15);
        //最大连接数
        comboPooledDataSource.setMaxPoolSize(50);
        comboPooledDataSource.setAcquireIncrement(15);
        //得到连接
        long start = System.currentTimeMillis();

        for (int i = 0; i < 5000000; i++) {
            //11981
            Connection connection = comboPooledDataSource.getConnection();

            connection.close();
        }

        long end = System.currentTimeMillis();


        System.out.println("5000 次连接耗时：" + (end - start));

    }

    @Test
    public void testM2() throws SQLException {
        //c3p0-config.xml配置文件连接
        ComboPooledDataSource comboPooledDataSource = new ComboPooledDataSource("jayPoolApp");

        Connection connection = comboPooledDataSource.getConnection();

        System.out.println("连接成功：" + connection);

        connection.close();
    }
}

```

##### Druid 连接池

```properties
driverClassName=com.mysql.jdbc.Driver
url=jdbc:mysql://localhost:3306/test_blog?useSSL=false&rewriteBatchedStatements=true
username=root
password=root
initialSize=10
minIdle=2
maxActive=50
maxWait=5000
```

```java
//@link https://repo1.maven.org/maven2/com/alibaba/druid/

public class Druid_ {

    @Test
    public void testDruid() throws Exception {
        Properties properties = new Properties();
        properties.load(new FileInputStream("src\\druid.properties"));

        DataSource dataSource = DruidDataSourceFactory.createDataSource(properties);

        long start = System.currentTimeMillis();
        //1165
        for (int i = 0; i < 5000000; i++) {
            Connection connection = dataSource.getConnection();
            connection.close();
        }
        long end = System.currentTimeMillis();

        System.out.println("druid 连接5000次耗时：" + (end - start));
    }
}
```