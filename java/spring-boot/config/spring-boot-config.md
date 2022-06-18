### 关于spring-boot的一些配置

###### 配置文件的格式

spring-boot 支持两种格式的配置文件：

* `.properties`
* `.yml` 官方更加支持使用`yml`格式的文件
* 配置文件的名字统一是`application`

###### 配置文件的位置

* 文件的位置和优先级

1. 项目下的根目录 | 优先级为1
2. 项目下目录`config`文件下 | 优先级为2 
3. 项目`resources/config`文件下 | 优先级为3
4. 项目`resources`文件下 | 优先级为4

> 在以上每个文件下都有配置文件的情况下，优先级最高的目录会优先生效，会从高到低的查找。

![config-01](https://qiniu.lglg.xyz/images/lglg/posts/202206/18/config-01.png)

* 关于`yml|yaml`文件数据格式

```yaml
server: tomcat
# 对象
student:
  name: jay
  age: 18

# 行内写法
students: {name: jay, age: 19}

# 数组
pets:
  - cat
  - dog
  - pig

pets1: [cat, dog, pig]


person:
  name: jay
  age: 22
  happy: false
  birth: 2019/11/02
  maps: {k1: v1, k2: v2}
  lists:
    - code
    - music
    - girl
  dog:
    name: 旺财
    age: 3
```

* 通过yml文件给类注入数据,`@ConfigurationProperties(prefix = "person")`通过这个注解，就可以把配置文件的数据注入到类中了

```java
@Component
public class Dog {
    @Value("旺财")
    private String name;

    @Value("2")
    private int age;

    public Dog(String name, int age) {
        this.name = name;
        this.age = age;
    }

    public Dog() {
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public int getAge() {
        return age;
    }

    public void setAge(int age) {
        this.age = age;
    }

    @Override
    public String
    toString() {
        return "Dog{" +
                "name='" + name + '\'' +
                ", age=" + age +
                '}';
    }
}


@Component
@ConfigurationProperties(prefix = "person")
@Validated
public class Person {
    private String name;

    private int age;

    private Boolean happy;

    private Date birth;

    private Map<String, Object> maps;

    private List<Object> lists;

    private Dog dog;

    public Person() {
    }

    public Person(String name, int age, Boolean happy, Date birth, Map<String, Object> maps, List<Object> lists, Dog dog) {
        this.name = name;
        this.age = age;
        this.happy = happy;
        this.birth = birth;
        this.maps = maps;
        this.lists = lists;
        this.dog = dog;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public int getAge() {
        return age;
    }

    public void setAge(int age) {
        this.age = age;
    }

    public Boolean getHappy() {
        return happy;
    }

    public void setHappy(Boolean happy) {
        this.happy = happy;
    }

    public Date getBirth() {
        return birth;
    }

    public void setBirth(Date birth) {
        this.birth = birth;
    }

    public Map<String, Object> getMaps() {
        return maps;
    }

    public void setMaps(Map<String, Object> maps) {
        this.maps = maps;
    }

    public List<Object> getLists() {
        return lists;
    }

    public void setLists(List<Object> lists) {
        this.lists = lists;
    }

    public Dog getDog() {
        return dog;
    }

    public void setDog(Dog dog) {
        this.dog = dog;
    }

    @Override
    public String toString() {
        return "Person{" +
                "name='" + name + '\'' +
                ", age=" + age +
                ", happy=" + happy +
                ", birth=" + birth +
                ", maps=" + maps +
                ", lists=" + lists +
                ", dog=" + dog +
                '}';
    }
}


```

**测试：**

```java
@SpringBootTest
class ApplicationTests {

    @Autowired
    private Person person;

    @Test
    void contextLoads() {

        System.out.println(person);
    }

}
```

* 在yml文件中可以多环境配置,使用`---`隔开不同的环境，使用`spring.profiles.active`指定具体是什么环境

```yaml

spring:
  profiles:
    active: local

---

server:
  port: 9999
spring:
  profiles: local
---

server:
  port: 8889
spring:
  profiles: dev

---
server:
  port: 8890
spring:
  profiles: test

```

![config-02](https://qiniu.lglg.xyz/images/lglg/posts/202206/18/config-02.png)

##### 静态资源存放位置

* 静态文件存放的位置

1. `resources/public` | 优先级 3
2. `resources/resources` | 优先级 1
3. `resources/status` | 优先级 2
4. `resources/templtes` 这个文件下是html的模板文件（只能是在这个目录下）


* 使用 `thymeleaf` 模板引擎

1. 在`pom`中添加依赖

```xml
<dependencies>
    <dependency>
        <groupId>org.thymeleaf</groupId>
        <artifactId>thymeleaf-spring5</artifactId>
    </dependency>
    <dependency>
        <groupId>org.thymeleaf.extras</groupId>
        <artifactId>thymeleaf-extras-java8time</artifactId>
    </dependency>
</dependencies>
```

2. 在`resources/templtes`下面创建模板文件,后缀必须是`.html`结尾,这些都是默认的，可以从`applcation.properties|application.yml`中配置

![config-03](https://qiniu.lglg.xyz/images/lglg/posts/202206/18/config-03.png)

3. 在`html`标签中添加约束`xmlns:th="http://www.thymeleaf.org"`

![config-04](https://qiniu.lglg.xyz/images/lglg/posts/202206/18/config-04.png)

4. **thymeleaf**官网地址 [https://www.thymeleaf.org/](https://www.thymeleaf.org/)
5. GitHub上样例依赖[https://github.com/spring-projects/spring-boot/blob/v2.0.0.M3/spring-boot-starters/spring-boot-starter-thymeleaf/pom.xml](https://github.com/spring-projects/spring-boot/blob/v2.0.0.M3/spring-boot-starters/spring-boot-starter-thymeleaf/pom.xml)
6. 关于`thymeleaf`的语法

![thymeleaf](https://qiniu.lglg.xyz/images/lglg/posts/202206/18/config-05.png)

```html
<!--普通字符-->
<div th:text="${msg}"></div>
<!--普通未转义字符-->
<div th:utext="${msg}"></div>
<!--循环-->
<h3 th:each="user:${users}" th:text="${user}"></h3>
<!--链接-->
<a href="@{https://www.baidu.com}">百度</a>
...
```

![config-06](https://qiniu.lglg.xyz/images/lglg/posts/202206/18/config-06.png)
