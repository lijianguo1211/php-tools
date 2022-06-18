### 创建一个spring-boot项目

* 环境
  * java version "1.8.0_331"
  * Apache Maven 3.8.5
  * IntelliJ IDEA 2022.1

* 使用 IntelliJ 创建spring-boot项目

1. 点击`File`
2. 选择`New`
3. 点击`Project`

![nit-project-01](https://qiniu.lglg.xyz/images/lglg/posts/202206/17/init-project-01.png)

4. 选择 `Spring Initializr`
5. 添加项目信息

![init-spring-boot-01](https://qiniu.lglg.xyz/images/lglg/posts/202206/17/init-spring-boot-01.png)

6. 选择下一步`next`,选择添加依赖，这里可以先选择一个`spring web | lombok`，这里会有选择`spring-boot`版本，可以直接选择默认的就可以

![init-spring-boot-02](https://qiniu.lglg.xyz/images/lglg/posts/202206/17/init-spring-boot-02.png)

7. 点击 `create`就完成了创建,(依赖包下载)

8. 创建完成的项目会看到有一个`com.jay.Application`启动文件

![init-spring-boot-03](https://qiniu.lglg.xyz/images/lglg/posts/202206/17/init-spring-boot-03.png)

9. 这个时候，任何代码不都写的情况下，运行这个文件，在浏览器访问`http://localhost:8080`,默认什么都不改动的情况下,控制台可以看到：

![init-spring-boot-04](https://qiniu.lglg.xyz/images/lglg/posts/202206/17/init-spring-boot-04.png)

10. 浏览器访问：

![init-spring-boot-05](https://qiniu.lglg.xyz/images/lglg/posts/202206/17/init-spring-boot-05.png)

11. 添加一个`HelloController`，添加地址

```java
package com.jay.controller;

import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RestController;

@RestController
public class HelloController {

    @RequestMapping("/hello")
    public String hello() {

        return "hello";
    }
}
```

12. 浏览器访问`http://localhost:8080/hello`，就可以看到输出字符串`hello`

![init-spring-boot-06](https://qiniu.lglg.xyz/images/lglg/posts/202206/17/init-spring-boot-06.png)
