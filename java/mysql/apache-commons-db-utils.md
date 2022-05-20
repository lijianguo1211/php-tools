#### Apache-commons-DbUtils操作数据

[手动下载连接](https://commons.apache.org/proper/commons-dbutils/download_dbutils.cgi)

> commons-dbutils 是 Apache 组织提供的一个开源 JDBC 工具类库，它是对 JDBC 的简单封装，学习成本极低，并且使用 commons-dbutils 能极大简化 JDBC 编码的工作量，同时也不会影响程序的性能

**主要类**

* ``QueryRunner`` 数据库的操作`query|update`
* ``ResultSetHandler`` 数据结果集的封装
* ``DbUtils`` 数据连接的关闭，装载 JDBC 驱动程序等常规工作的工具类

********

* 从连接池或者是手动加载连接

```
Connection connection = JdbcDruidUtils.getConnection();
```

* `new` 一个查询类 

```
QueryRunner queryRunner = new QueryRunner();
```

* 查询多条语句 ``BeanListHandler``,返回一个集合|null

```
String sql = "select * from test_admin where id < ?"
TestAdmin query = queryRunner.query(connection, sql, new BeanHandler<>(TestAdmin.class), 1);
```

* 查询单条语句 ``BeanHandler``返回一个数据查询对象|null

```
String sql = "select * from test_admin where id = ?";

TestAdmin query = queryRunner.query(connection, sql, new BeanHandler<>(TestAdmin.class), 5231344);
```

* 查询单个字段 ``ScalarHandler`` 返回字段对象|null

```
String sql = "select username from test_admin where id = ?;

Object query = queryRunner.query(connection, sql, new ScalarHandler(), 4);
```

* 新增

```
String sql = "insert into test_admin (username, password) values (?, ?)";
int affectedRaw1 = queryRunner.update(connection, sql1, "张三丰", "123456");

System.out.println("insert 受影响的行数：" + affectedRaw1);
```

* 修改

```
String sql = "update test_admin set username = ? where id = ?";
int affectedRaw2 = queryRunner.update(connection, sql2, "刘德华", 1);

System.out.println("update 受影响的行数：" + affectedRaw2);
```

* 删除

```
String sql = "delete from test_admin where id = ?";
int affectedRaw3 = queryRunner.update(connection, sql3, 2);

System.out.println("delete 受影响的行数：" + affectedRaw3);
```
