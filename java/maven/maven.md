### Maven的相关记录

##### 安装

[maven下载地址](https://maven.apache.org/download.cgi)

* 解压文件到自定义文件

* 配置环境变量 `MAVEN_HOME=/path`

* 添加环境变量到`path| %MAVEN_HOME%\bin`

* 查看是否安装成功`mvn -v`

* 输出：

```shell
C:\Users\jay.li>mvn -v
Apache Maven 3.8.5 (3599d3414f046de2324203b78ddcf9b5e4388aa0)
Maven home: D:\java\apache-maven-3.8.5
Java version: 1.8.0_321, vendor: Oracle Corporation, runtime: D:\Program Files\Java\jdk1.8.0_321\jre
Default locale: zh_CN, platform encoding: GBK
OS name: "windows 11", version: "10.0", arch: "amd64", family: "windows"
```

* 修改配置文件，在`maven/conf/settings.xml`

```xml
<!--D:\java\maven\repo 本地电脑自定义的文件夹位置-->
<localRepository>D:\java\maven\repo</localRepository>
```

* 修改maven的配置源地址

```xml
<mirror>
  <id>alimaven</id>
  <mirrorOf>central</mirrorOf>
  <name>aliyun maven</name>
  <url> http://maven.aliyun.com/nexus/content/repositories/central/</url>
  <blocked>true</blocked>
</mirror>
```

* 添加jdk的配置(可不配置)

```xml
<profile>
      <id>jdk-1.8</id>

      <activation>
        <jdk>1.8</jdk>
      </activation>

      <repositories>
        <repository>
          <id>jdk18</id>
          <name>Repository for JDK 1.4 builds</name>
          <url>http://www.myhost.com/maven/jdk14</url>
          <layout>default</layout>
          <snapshotPolicy>always</snapshotPolicy>
        </repository>
      </repositories>
</profile>
```

* 简单测试是否可以下载包

```shell
mvn help:active-profiles
```

* 查看第一步配置文件下`localRepository`文件

#### Maven 相关的命令

* ``mvn install`` 安装包到本项目
* ``mvn clear`` 删除maven编译生成的目录
* ``mvn compile`` 编译项目主代码
* ``mvn test-compile`` 编译项目测试主代码
* ``mvn test`` 运行所有测试代码
* ``mvn package`` 打包，将项目打包为`jar`包或者`war`包
* ``mvn deploy`` 部署