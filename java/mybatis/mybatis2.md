#### mybatis数据查询二

* 关于mybatis的配置文件，通过添加配置文件配置数据连接信息

```properties
driver=com.mysql.cj.jdbc.Driver
url=jdbc:mysql://localhost:3306/test_blog?useSSL=true&sueUnicode=true&characterEncode=UTF-8
username=root
password=root
```

```xml
<properties resource="db.properties">
    <property name="password" value="123456"/>
</properties>

<dataSource type="POOLED">
    <property name="driver" value="${driver}"/>
    <property name="url" value="${url}"/>
    <property name="username" value="${username}"/>
    <property name="password" value="${password}"/>
</dataSource>
```

> `.properties`中的配置信息会覆盖掉 `<property name="password" value="123456"/>`这里配置的信息。`${driver}`这里取的信息是`.properties`的配置信息

* 配置日志文件开启，可以使用默认的控制台输出日志`STDOUT_LOGGING`

```xml
<settings>
    <setting name="logImpl" value="STDOUT_LOGGING"/>
</settings>
```

* 配置`log4j`日志

> 在 `pom.xml`中添加依赖包，之后在配置文件中修改配置

```xml
<dependency>
    <groupId>log4j</groupId>
    <artifactId>log4j</artifactId>
    <version>1.2.17</version>
</dependency>
```

把日志输出修改为**LOG4J**,之后去添加`log4j`的配置文件

```xml
<settings>
    <setting name="logImpl" value="LOG4J"/>
</settings>
```

```properties
log4j.rootLogger=DEBUG,console,file
log4j.appender.console=org.apache.log4j.ConsoleAppender
log4j.appender.console.Target=System.out
log4j.appender.console.Threshold=DEBUG
log4j.appender.console.layout=org.apache.log4j.PatternLayout
log4j.appender.console.layout.ConversionPattern=[%c]-%m%n

log4j.appender.file=org.apache.log4j.RollingFileAppender
log4j.appender.file.File=./log/jay.log
log4j.appender.file.MaxFileSize=10mb
log4j.appender.file.Threshold=DEBUG
log4j.appender.file.layout=org.apache.log4j.PatternLayout
log4j.appender.file.layout.ConversionPattern=[%p][%d{yy-mm-dd}][%c]%m%n

log4j.logger.org.mybatis=DEBUG
log4j.logger.java.sql=DEBUG
log4j.logger.java.sql.Statement=DEBUG
log4j.logger.java.sql.ResultSet=DEBUG
log4j.logger.java.sql.PreparedStatement=DEBUG
```

**************

#### 关于数据擦查询

* 简单操作可以通过使用注解，就不需要xml文件映射

```java
interface Test {

    // @Select查询
    @Select("select * from users where id = #{id} or title = #{title}")
    // @Param 绑定参数
    public List<User> selectUser(@Param("id") _int id, @Param("title") String title);
    
    // @Insert 插入
    @Insert("insert into users (title, mobile, score, sex, created_at, updated_at) values (#{title}, #{mobile}, #{score}, #{sex}, #{created_at}, #{updated_at})")
    public int addUser(Map map);
    
    // @Update 更新
    @Update("update users set title = #{title} where id = #{id}")
    public int updateUser(@Param("id") _int id, @Param("title") String title);
    
    // @Delete 删除
    @Delete("delete from users where id = #{id}")
    public int deleteUser(@Param("id") _int id);
}
```

* 动态`where`字句的实现

```xml
<!--查询某条语句需要根据传入不同的值，添加不同的where条件，在mybatis中可以这样实现-->
<select id="userList">
    select * from users where id > 0 
    <if test="name != null">
        and name = #{name}
    </if>
    <if test="title != null">
        and title = #{title}
    </if>
    <if test="if age != null">
        and age = #{age}
    </if>
</select>
```

* 有时候，我们不想使用所有的条件，而只是想从多个条件中选择一个使用。针对这种情况，MyBatis 提供了 choose 元素，它有点像 Java 中的 switch 语句。

```xml
<!--如果 name != null 就是and name = #{name} 或者是  test="title != null" 就是 and title = #{title}，两者皆无就是  and status = 1-->
<select id="userList">
    select * from users where id > 0 
    <choose>
        <when test="name != null">
            and name = #{name}
        </when>
        <when test="title != null">
            and title = #{title}
        </when>
        <otherwise>
            and status = 1
        </otherwise>
    </choose>
</select>
```

* 当所有的where字句都是动态判断的时，就会出现一个问题：如下当什么都没有的时候，sql:`select * from users where`是这个样子，语法就会出现问题，或者是
出现`select * from users where and name = #{name}`。。。这个时候，mybatis提供了`where`标签

```xml
<select id="userList">
    select * from users where 
    <if test="name != null">
        and name = #{name}
    </if>
    <if test="title != null">
        and title = #{title}
    </if>
    <if test="if age != null">
        and age = #{age}
    </if>
</select>

<!--改造如下-->
<!--where 元素只会在子元素返回任何内容的情况下才插入 “WHERE” 子句。而且，若子句的开头为 “AND” 或 “OR”，where 元素也会将它们去除。-->

<select id="userList">
select * from users
    <where>
        <if test="name != null">
            and name = #{name}
        </if>
        <if test="title != null">
            and title = #{title}
        </if>
        <if test="if age != null">
            and age = #{age}
        </if>
    </where>
</select>
```

* `foreach`，对集合进行遍历。一张学生表，一张老师表，一个老师对应n个学生，直到了所有符合条件的老师，通过老师筛选出学生：

```mysql
select * from `students` where `tid` in (select `id` from `teachers` where `age` = 30)  
```

```xml
<select id="students">
    select * from students where tid in 
    <foreach item="val" index="key" collection="teacherList" open="(" close=")" separator=",">
        #{val}
    </foreach>
</select>
<!--select * from `students` where `tid` in (1, 2, 3)  -->
```

* 动态更新`set`,set 元素可以用于动态包含需要更新的列，忽略其它不更新的列

```xml
<update id="updateUser">
  update users
    <set>
      <if test="username != null">username=#{username},</if>
      <if test="password != null">password=#{password},</if>
      <if test="email != null">email=#{email},</if>
      <if test="bio != null">bio=#{bio}</if>
    </set>
  where id=#{id}
</update>
```

* 复杂查询之一对多

```xml
<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE mapper
        PUBLIC "-//mybatis.org//DTD Mapper 3.0//EN"
        "http://mybatis.org/dtd/mybatis-3-mapper.dtd">

<mapper namespace="org.jay.dao.TeacherMapper">

<!--  通过子查询（按照结果嵌套处理）  -->
    <resultMap id="sManyT" type="Teachers">

        <result column="id" property="id"></result>
        <result column="name" property="name"></result>
        <result column="created_at" property="createdAt"></result>
        <result column="updated_at" property="updatedAt"></result>

<!--   javaType 实体类中属性的类型     -->
<!--   ofType 指定映射到集合或者List中的pojo类型（泛型的约束类型）    -->
        <collection property="students" column="id" ofType="Students" javaType="ArrayList" select="manyStudent"></collection>
    </resultMap>


    <select id="teacherList" resultMap="sManyT">
        select * from teachers
    </select>

    <select id="manyStudent" resultType="Students">
        select * from students where teacher_id = #{id}
    </select>


<!-- 通过左连接查询（按照查询嵌套处理） -->

    <resultMap id="sManyT2" type="Teachers">
        <result column="tname" property="name"></result>
        <result column="tid" property="id"></result>
        <collection property="students" ofType="Students">
            <result column="sname" property="name"></result>
            <result column="sid" property="id"></result>
        </collection>
    </resultMap>


    <select id="teacherList2" resultMap="sManyT2">
        select s.name as sname, s.id as sid, t.name as tname, t.id as tid from students as s inner join teachers as t on t.id = s.teacher_id
    </select>

</mapper>
```


* 复杂查询之多对一

1. 多条语句分开查询

```xml
<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE mapper
        PUBLIC "-//mybatis.org//DTD Mapper 3.0//EN"
        "http://mybatis.org/dtd/mybatis-3-mapper.dtd">

<mapper namespace="org.jay.dao.StudentMapper">

<!--  结果集映射  -->
<!--  id="studentOneTeacher" 对应著查询学生的resultMap  -->
<!--  type="Students" 返回的列表类型  -->
    <resultMap id="studentOneTeacher" type="Students">
<!--  数据表字段映射（字段名不一致修改）      -->
        <result property="id" column="id"></result>
        <result property="name" column="name"></result>
        <result property="grade" column="grade"></result>
        <result property="age" column="age"></result>
        <result property="teacherId" column="teacher_id"></result>
        <result property="createdAt" column="created_at"></result>
        <result property="updatedAt" column="updated_at"></result>
<!--  多对一查询老师      -->
<!--  property="teachers" 映射到实体类 Students 中的字段名      -->
<!--  column="teacher_id" 关联关系id      -->
<!--  javaType="Teachers" 返回结果的映射类型      -->
<!--  select="oneTeacher" 关联的查询语句      -->
<!--  一个复杂类型的关联；许多结果将包装成这种类型-->
        <association property="teachers" column="teacher_id" javaType="Teachers" select="oneTeacher">
            <result property="id" column="id"></result>
            <result property="name" column="name"></result>
            <result property="createdAt" column="created_at"></result>
            <result property="updatedAt" column="updated_at"></result>
        </association>
    </resultMap>

<!--先查询全部学生-->
    <select id="studentList" resultMap="studentOneTeacher">
        select * from students
    </select>
<!--根据学生表的老师id查询老师的信息-->
    <select id="oneTeacher" resultType="Teachers">
        select * from teachers where id = #{teacher_id}
    </select>
</mapper>
```

2. 连表查询

```xml
<resultMap id="studentOneTeacher2" type="Students">
    <result column="sname" property="name"></result>
    <result column="sid" property="id"></result>
    <association property="teachers" javaType="Teachers">
        <result column="tname" property="name"></result>
        <result column="tid" property="id"></result>
    </association>
</resultMap>

<select id="studentList2" resultMap="studentOneTeacher2">
    select s.name as sname, s.id as sid, t.name as tname, t.id as tid from students as s inner join teachers as t on t.id = s.teacher_id
</select>
```