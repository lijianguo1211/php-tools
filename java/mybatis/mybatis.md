### java 数据库操作扩展包之 **myBatis**一

>MyBatis 是一款优秀的持久层框架，它支持自定义 SQL、存储过程以及高级映射。MyBatis 免除了几乎所有的 JDBC 代码以及设置参数和获取结果集的工作。
> MyBatis 可以通过简单的 XML 或注解来配置和映射原始类型、接口和 Java POJO（Plain Old Java Objects，普通老式 Java 对象）为数据库中的记录。

**[中文版官方文档](https://mybatis.net.cn/index.html)**

##### 安装

* 通过``maven``安装

```xml
<dependencies>
    <dependency>
        <groupId>org.mybatis</groupId>
        <artifactId>mybatis</artifactId>
        <version>3.5.10</version>
    </dependency>
</dependencies>
```

* 直接下载`mybatis.jar`包，将 mybatis-x.x.x.jar 文件置于类路径（classpath）中。


##### `myBatis`的基本配置文件

```xml
<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE configuration
        PUBLIC "-//mybatis.org//DTD Config 3.0//EN"
        "http://mybatis.org/dtd/mybatis-3-config.dtd">
<!--配置文件-->
<configuration>
<!--  多个环境，默认的是development  -->
    <environments default="development">
<!--   development环境的配置     -->
        <environment id="development">
            <transactionManager type="JDBC"/>
<!--   数据源       -->
<!--   POOLED 连接池       -->
            <dataSource type="POOLED">
<!--   数据库举动             -->
                <property name="driver" value="com.mysql.jdbc.Driver"/>
<!--   数据库连接             -->
                <property name="url" value="jdbc:mysql://localhost:3306/wp?useSSL=true&amp;sueUnicode=true&amp;characterEncode=UTF-8"/>
<!--   数据库用户             -->
                <property name="username" value="root"/>
<!--   数据库密码             -->
                <property name="password" value="root"/>
            </dataSource>
        </environment>
    </environments>

<!-- 映射器 - 映射器的 XML 映射文件包含了 SQL 代码和映射定义信息   -->
    <mappers>
<!--  使用相对于类路径的资源引用    -->
        <mapper resource="org/jay/dao/UserMapper.xml"/>
    </mappers>
</configuration>
```

* 启用默认值配置

```xml
<property name="org.apache.ibatis.parsing.PropertyParser.enable-default-value" value="true"/>
```

* 修改默认值的分隔符

```xml
<property name="org.apache.ibatis.parsing.PropertyParser.default-value-separator" value="?:"/> 
<property name="username" value="${db:username?:ut_user}"/>
```

* settings配置：极为重要的调整设置

|               设置名	               |                                                                                     描述	                                                                                      |                                   有效值	                                    |                          默认值                          |
|:--------------------------------:|:----------------------------------------------------------------------------------------------------------------------------------------------------------------------------:|:-------------------------------------------------------------------------:|:-----------------------------------------------------:|
|           cacheEnabled           |                                                                        	全局性地开启或关闭所有映射器配置文件中已配置的任何缓存。                                                                         |                              	true / false	                               |                         true                          |
|        lazyLoadingEnabled        |                                                      	延迟加载的全局开关。当开启时，所有关联对象都会延迟加载。 特定关联关系中可通过设置 fetchType 属性来覆盖该项的开关状态                                                       |                              	true / false	                               |                         false                         |
|      aggressiveLazyLoading       |                                                  	开启时，任一方法的调用都会加载该对象的所有延迟加载属性。 否则，每个延迟加载属性会按需加载（参考 lazyLoadTriggerMethods)。                                                  |                              	true / false	                               |            false （在 3.4.1 及之前的版本中默认为 true）            |
|    multipleResultSetsEnabled     |                                                                         	是否允许单个语句返回多结果集（需要数据库驱动支持）。                                                                          |                               	true /false                                |                         	true                         |
|         useColumnLabel	          |                                                              使用列标签代替列名。实际表现依赖于数据库驱动，具体可参考数据库驱动的相关文档，或通过对比测试来观察。                                                              |                               	true / false                               |                         true                          |
|         useGeneratedKeys         |                                            	允许 JDBC 支持自动生成主键，需要数据库驱动支持。如果设置为 true，将强制使用自动生成主键。尽管一些数据库驱动不支持此特性，但仍可正常工作（如 Derby）。	                                             |                               true /false	                                |                         False                         |
|       autoMappingBehavior        |                                      	指定 MyBatis 应如何自动映射列到字段或属性。 NONE 表示关闭自动映射；PARTIAL 只会自动映射没有定义嵌套结果映射的字段。 FULL 会自动映射任何复杂的结果集（无论是否嵌套）。                                      |                           	NONE, PARTIAL, FULL	                           |                        PARTIAL                        |
| autoMappingUnknownColumnBehavior | 	指定发现自动映射目标未知列（或未知属性类型）的行为。NONE: 不做任何反应 WARNING: 输出警告日志（'org.apache.ibatis.session.AutoMappingUnknownColumnBehavior' 的日志等级必须设置为 WARN） FAILING: 映射失败 (抛出 SqlSessionException) |                          NONE, WARNING, FAILING                           |                         NONE                          |
|       defaultExecutorType        |                                           	配置默认的执行器。SIMPLE 就是普通的执行器；REUSE 执行器会重用预处理语句（PreparedStatement）； BATCH 执行器不仅重用语句还会执行批量更新。                                           |                           	SIMPLE REUSE BATCH	                            |                        SIMPLE                         |
|     defaultStatementTimeout      |                                                                         	设置超时时间，它决定数据库驱动等待数据库响应的秒数。	                                                                         |                                  任意正整数	                                   |                      未设置 (null)                       |
|         defaultFetchSize         |                                                               	为驱动的结果集获取数量（fetchSize）设置一个建议值。此参数只可以在查询设置中被覆盖。	                                                               |                                  任意正整数	                                   |                      未设置 (null)                       |
|       defaultResultSetType       |                                                                           	指定语句默认的滚动策略。（新增于 3.5.2）                                                                           | 	FORWARD_ONLY / SCROLL_SENSITIVE / SCROLL_INSENSITIVE / DEFAULT（等同于未设置）	  |                      未设置 (null)                       |
|       safeRowBoundsEnabled       |                                                                	是否允许在嵌套语句中使用分页（RowBounds）。如果允许使用则设置为 false。	                                                                 |                               true / false	                               |                         False                         |
|     safeResultHandlerEnabled     |                                                             	是否允许在嵌套语句中使用结果处理器（ResultHandler）。如果允许使用则设置为 false。                                                              |                              	true / false	                               |                         True                          |
|     mapUnderscoreToCamelCase     |                                                           	是否开启驼峰命名自动映射，即从经典数据库列名 A_COLUMN 映射到经典 Java 属性名 aColumn。                                                           |                              	true  / false	                              |                         False                         |
|         localCacheScope	         |                    MyBatis 利用本地缓存机制（Local Cache）防止循环引用和加速重复的嵌套查询。 默认值为 SESSION，会缓存一个会话中执行的所有查询。 若设置值为 STATEMENT，本地缓存将仅用于执行语句，对相同 SqlSession 的不同查询将不会进行缓存。                    |                           	SESSION / STATEMENT	                           |                        SESSION                        |
|         jdbcTypeForNull	         |                                      当没有为参数指定特定的 JDBC 类型时，空值的默认 JDBC 类型。 某些数据库驱动需要指定列的 JDBC 类型，多数情况直接用一般类型即可，比如 NULL、VARCHAR 或 OTHER。	                                       |                   JdbcType 常量，常用值：NULL、VARCHAR 或 OTHER。                   |                        	OTHER                         |
|      lazyLoadTriggerMethods      |                                                                             	指定对象的哪些方法触发一次延迟加载。	                                                                             |                               用逗号分隔的方法列表。	                                |            equals,clone,hashCode,toString             |
|     defaultScriptingLanguage     |                                                                           	指定动态 SQL 生成使用的默认脚本语言。	                                                                            |                              一个类型别名或全限定类名。	                               | org.apache.ibatis.scripting.xmltags.XMLLanguageDriver |
|      defaultEnumTypeHandler      |                                                                   	指定 Enum 使用的默认 TypeHandler 。（新增于 3.4.5）	                                                                   |                              一个类型别名或全限定类名。	                               |        org.apache.ibatis.type.EnumTypeHandler         |
|       callSettersOnNulls	        |                         指定当结果集中值为 null 的时候是否调用映射对象的 setter（map 对象时为 put）方法，这在依赖于 Map.keySet() 或 null 值进行初始化时比较有用。注意基本类型（int、boolean 等）是不能设置成 null 的。                         |                              	true / false	                               |                         false                         |
|    returnInstanceForEmptyRow	    |                                        当返回行的所有列都是空时，MyBatis默认返回 null。 当开启这个设置时，MyBatis会返回一个空实例。 请注意，它也适用于嵌套的结果集（如集合或关联）。（新增于 3.4.2）	                                         |                               true / false	                               |                         false                         |                                                       |
|            logPrefix	            |                                                                            指定 MyBatis 增加到日志名称的前缀。                                                                            |                                  	任何字符串	                                  |                          未设置                          |
|             logImpl	             |                                                                       指定 MyBatis 所用日志的具体实现，未指定时将自动查找。	                                                                       | SLF4J/LOG4J /LOG4J2/JDK_LOGGING/COMMONS_LOGGING/STDOUT_LOGGING/NO_LOGGING |                         	未设置                          |
|           proxyFactory           |                                                                        	指定 Mybatis 创建可延迟加载对象所用到的代理工具。                                                                        |                           	CGLIB  / JAVASSIST	                            |              JAVASSIST （MyBatis 3.3 以上）               |
|             vfsImpl              |                                                                                 	指定 VFS 的实现                                                                                  |                         	自定义 VFS 的实现的类全限定名，以逗号分隔。                         |                         	未设置                          |
|        useActualParamName        |                                              	允许使用方法签名中的名称作为语句参数名称。 为了使用该特性，你的项目必须采用 Java 8 编译，并且加上 -parameters 选项。（新增于 3.4.1）	                                              |                               true / false                                |                         	true                         |
|      configurationFactory	       |                 指定一个提供 Configuration 实例的类。 这个被返回的 Configuration 实例用来加载被反序列化对象的延迟加载属性值。 这个类必须包含一个签名为static Configuration getConfiguration() 的方法。（新增于 3.2.3）	                  |                              一个类型别名或完全限定类名。                               |                         	未设置                          |

#### 得到一个`SqlSessionFactory`示例（通过xml文件构建）

* SqlSessionFactoryBuilder
> 这个类可以被实例化、使用和丢弃，一旦创建了 SqlSessionFactory，就不再需要它了。 因此 SqlSessionFactoryBuilder 实例的最佳作用域是方法作用域
> （也就是局部方法变量）

* SqlSessionFactory
>SqlSessionFactory 一旦被创建就应该在应用的运行期间一直存在，没有任何理由丢弃它或重新创建另一个实例。 使用 SqlSessionFactory 的最佳实践是在应
> 用运行期间不要重复创建多次，多次重建 SqlSessionFactory 被视为一种代码“坏习惯”。因此 SqlSessionFactory 的最佳作用域是应用作用域。 有很多方法可以做到，最简单的就是使用单例模式或者静态单例模式。

* SqlSession
>每个线程都应该有它自己的 SqlSession 实例。SqlSession 的实例不是线程安全的，因此是不能被共享的，所以它的最佳的作用域是请求或方法作用域.而且使用之后
> 就马上关闭它

```java
public class MybatisUtils {
    private static SqlSessionFactory sqlSessionFactory;

    static {
        String resource = "mybatis-config.xml";

        InputStream inputStream = null;
        try {
            inputStream = Resources.getResourceAsStream(resource);
            
            sqlSessionFactory = new SqlSessionFactoryBuilder().build(inputStream);
        } catch (IOException e) {
            throw new RuntimeException(e);
        }
    }

    //　获取SqlSession实例
    public static SqlSession getSqlSession() {
        return sqlSessionFactory.openSession();
    }

}
```

#### 简单的数据操作

* 定义一个数据表对象的实体类，比如`users`表

```java
public class User {
    private int ID;

    private String user_login;

    private String user_pass;

    private String user_nicename;

    private String user_email;

    private String user_url;
    private String user_registered;

    private short user_status;
    private String display_name;
    private String user_activation_key;

    public int getID() {
        return ID;
    }

    public void setID(int ID) {
        this.ID = ID;
    }

    public String getUser_login() {
        return user_login;
    }

    public void setUser_login(String user_login) {
        this.user_login = user_login;
    }

    public String getUser_pass() {
        return user_pass;
    }

    public void setUser_pass(String user_pass) {
        this.user_pass = user_pass;
    }

    public String getUser_nicename() {
        return user_nicename;
    }

    public void setUser_nicename(String user_nicename) {
        this.user_nicename = user_nicename;
    }

    public String getUser_email() {
        return user_email;
    }

    public void setUser_email(String user_email) {
        this.user_email = user_email;
    }

    public String getUser_url() {
        return user_url;
    }

    public void setUser_url(String user_url) {
        this.user_url = user_url;
    }

    public String getUser_registered() {
        return user_registered;
    }

    public void setUser_registered(String user_registered) {
        this.user_registered = user_registered;
    }

    public short getUser_status() {
        return user_status;
    }

    public void setUser_status(short user_status) {
        this.user_status = user_status;
    }

    public String getDisplay_name() {
        return display_name;
    }

    public void setDisplay_name(String display_name) {
        this.display_name = display_name;
    }

    public String getUser_activation_key() {
        return user_activation_key;
    }

    public void setUser_activation_key(String user_activation_key) {
        this.user_activation_key = user_activation_key;
    }

    @Override
    public String toString() {
        return "User{" +
                "ID=" + ID +
                ", user_login='" + user_login + '\'' +
                ", user_pass='" + user_pass + '\'' +
                ", user_nicename='" + user_nicename + '\'' +
                ", user_email='" + user_email + '\'' +
                ", user_url='" + user_url + '\'' +
                ", user_registered='" + user_registered + '\'' +
                ", user_status=" + user_status +
                ", display_name='" + display_name + '\'' +
                ", user_activation_key='" + user_activation_key + '\'' +
                '}';
    }
    
    public User() {
    }

    public User(int ID, String user_login, String user_pass, String user_nicename, String user_email, String user_url, String user_registered, short user_status, String display_name, String user_activation_key) {
        this.ID = ID;
        this.user_login = user_login;
        this.user_pass = user_pass;
        this.user_nicename = user_nicename;
        this.user_email = user_email;
        this.user_url = user_url;
        this.user_registered = user_registered;
        this.user_status = user_status;
        this.display_name = display_name;
        this.user_activation_key = user_activation_key;
    }

    public User(String user_login, String user_pass, String user_nicename, String user_email, String user_url, String user_registered) {
        this.user_login = user_login;
        this.user_pass = user_pass;
        this.user_nicename = user_nicename;
        this.user_email = user_email;
        this.user_url = user_url;
        this.user_registered = user_registered;
    }
}
```

* 定义一个数据操作接口 `UserMapper`

```java
public interface UserMapper {

    /**
     * 获取全部用户
     * @return
     */
    public List<User> getUserList();

    public User getUserById(int id);

    public int addUser(User user);

    public int updateUser(User user);

    public int deleteUser(int id);
}
```

* 数据接口与之对应的xml文件

```xml
<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE mapper
        PUBLIC "-//mybatis.org//DTD Mapper 3.0//EN"
        "http://mybatis.org/dtd/mybatis-3-mapper.dtd">
<!--全限定命名空间-->
<mapper namespace="org.dao.UserMapper">
    <select id="getUserList" resultType="org.pojo.User">
        select * from wp_users
    </select>

    <select id="getUserById" parameterType="int" resultType="org.pojo.User">
        select * from wp_users where id = #{id}
    </select>

    <insert id="addUser" parameterType="org.pojo.User">
        insert into wp_users
            (user_login, user_pass, user_nicename, user_email, user_url, user_registered)
        values
            (#{user_login}, #{user_pass}, #{user_nicename}, #{user_email}, #{user_url}, #{user_registered})
    </insert>

    <update id="updateUser" parameterType="org.pojo.User">
        update wp_users set user_nicename = #{user_nicename} where id = #{ID}
    </update>

    <delete id="deleteUser" parameterType="int">
        delete from wp_users where ID = #{id}
    </delete>
</mapper>
```

* 命名空间 `namespace`

>命名空间的作用有两个，一个是利用更长的全限定名来将不同的语句隔离开来，同时也实现了你上面见到的接口绑定

* 查询 `select`
  * 属性
    * `id` 在命名空间中唯一的标识符，可以被用来引用这条语句
    * `resultType` 期望从这条语句中返回结果的类全限定名或别名
    * `resultMap` 对外部 resultMap 的命名引用
    * `parameterType` 将会传入这条语句的参数的类全限定名或别名
    * `flushCache` 将其设置为 true 后，只要语句被调用，都会导致本地缓存和二级缓存被清空
    * `useCache` 将其设置为 true 后，将会导致本条语句的结果被二级缓存缓存起来
    * `timeout` 这个设置是在抛出异常之前，驱动程序等待数据库返回请求结果的秒数
    * `fetchSize` 这是一个给驱动的建议值，尝试让驱动程序每次批量返回的结果行数等于这个设置值。 默认值为未设置（unset）（依赖驱动）
    * `statementType` 可选 STATEMENT，PREPARED 或 CALLABLE。这会让 MyBatis 分别使用 Statement，PreparedStatement 或 CallableStatement，默认值：PREPARED
    * `resultSetType` FORWARD_ONLY，SCROLL_SENSITIVE, SCROLL_INSENSITIVE 或 DEFAULT（等价于 unset） 中的一个，默认值为 unset （依赖数据库驱动）
    * `databaseId` 如果配置了数据库厂商标识（databaseIdProvider），MyBatis 会加载所有不带 databaseId 或匹配当前 databaseId 的语句；如果带和不带的语句都有，则不带的会被忽略
    * `resultOrdered` 这个设置仅针对嵌套结果 select 语句：如果为 true，将会假设包含了嵌套结果集或是分组，当返回一个主结果行时，就不会产生对前面结果集的引用。 这就使得在获取嵌套结果集的时候不至于内存不够用。默认值：false
    * `resultSets` 这个设置仅适用于多结果集的情况。它将列出语句执行后返回的结果集并赋予每个结果集一个名称，多个名称之间以逗号分隔
* 新增 `insert`
  * 属性
    * `useGeneratedKeys` 是否支持数据库自增主键
    * `keyProperty ` 自增主键column
* 更新 `update`
* 删除 `delete`
* 可重复使用的片段 `sql`

```xml
<sql id="userColumns"> ${alias}.id,${alias}.username,${alias}.password </sql>

<select id="selectUsers" resultType="map">
select
<include refid="userColumns"><property name="alias" value="t1"/></include>,
<include refid="userColumns"><property name="alias" value="t2"/></include>
from some_table t1
cross join some_table t2
</select>
```

