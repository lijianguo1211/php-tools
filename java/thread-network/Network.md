### 关于Java网络相关的知识

#### 线程（Thread）[线程相关介绍](./Thread.md)

#### 文件流（IO）[文件流相关介绍](./../io/io.md)

#### 网络（TCP|UDP）

##### IPV4地址分类

| 类型  |            范围             |        表示        |
|:----|:-------------------------:|:----------------:|
| A   |  0.0.0.0到127.255.255.255  |   0~7网络号~24主机号   |
| B   | 128.0.0.0到191.255.255.255 | 1~0~14网络号~16主机号  |
| C   | 192.0.0.0到223.255.255.255 | 1~1~0~21网络号~8主机号 |
| D   | 224.0.0.0到239.255.255.255 |  1~1~1~0~28多播组号  |
| E   | 240.0.0.0到247.255.255.255 | 1~1~1~1~0~27留待后用 |

![ipv4-type](https://qiniu.lglg.xyz/images/lglg/posts/202206/06/ipv4-type.png)

##### 端口号

> 计算机种端口号是定位特定运行程序的一个标识，范围是0~65535，其中0-1024是计算机内部占用，22[ssh] 21[ftp] 25[smtp] 80[http]剩下的端口号才是不同程序的分配（人为）比如3306[mysql] 6379[redis] 1521[oracle]
> sqlserver[1433]

##### `java.net.*`下面相关的网络编程包

* `java.net.InetAddress` IP地址相关的封装操作

> 基本是封装在静态方法中，作为静态方法调用

* 返回本地主机地址 ``InetAddress.getLocalHost()`` 返回的是`InetAddress`对象.
* 返回指定主机的信息``InetAddress.getByName("www.lglg.xyz")`` 传入一个主机名或者是域名,返回的是`InetAddress`对象
* 返回IP信息``InetAddress.getHostAddress()``
* 返回主机名``InetAddress.getHostName()``
* 返回字节数组组成的IP地址`InetAddress.getAddress()`

##### java当中的tcp编程

##### 服务端

> 服务端需要四个最重要的类

* ``ServerSocket`` 可以监听端口
* ``Socket``通过 `ServerSocket.accept()`方法阻塞，等待连接
* ``InputStream`` 拿到输入流
* ``outputStream`` 拿到输出流

###### 测试只监听处理一次请求的服务端

```java
public class Server_ {

    public static void main(String[] args) throws IOException {
        //监听端口
        ServerSocket serverSocket = new ServerSocket(9999);

        System.out.println("服务端在端口9999监听~");

        //当连接到9999端口，程序会阻塞，等待连接
        Socket accept = serverSocket.accept();

        InputStream inputStream = accept.getInputStream();

        byte[] buf = new byte[1024];

        int readLen = 0;

        while ((readLen = inputStream.read(buf)) != -1) {
            System.out.println(new String(buf, 0, readLen));
        }

        inputStream.close();

        accept.close();

        serverSocket.close();
    }
}

```

##### 客户端

> 客户端一般都比较简单只需要三个类就可以简单的编写一个客户端

* ``Socket``创建一个socket【ip, port】
* ``InputStream``拿到输入流
* ``outputStream``拿到输出流


###### 测试发送一次请求的客户端

```java
public class Client_ {

    public static void main(String[] args) throws IOException {
        Socket socket = new Socket(InetAddress.getLocalHost(), 9999);

        OutputStream outputStream = socket.getOutputStream();

        outputStream.write("Hello Server~".getBytes());

        outputStream.close();

        socket.close();

        System.out.println("client exit~");
    }
}

```

首先运行服务端，服务端在本机的9999端口开始监听

![netstat-9999-1](https://qiniu.lglg.xyz/images/lglg/posts/202206/06/netstat-9999-1.jpg)

服务端开启监听之后，可以调用客户端的程序，就可以看到，客户端发送给服务端的**Hello Server~**数据，服务端在收到数据之后，打印退出~，客户端发送数据之后退出~

![server-01](https://qiniu.lglg.xyz/images/lglg/posts/202206/06/server-01.png)

同时，如果不想编写客户端的代码，还可以在使用`telnet| telnet 127.0.0.1 9999`命令，向服务端发送数据，服务端同时也会收到客户端的数据，打印在控制台

![server-02](https://qiniu.lglg.xyz/images/lglg/posts/202206/06/server-02.png)

********

#### 进阶版，客户端可以给服务端发送消息，服务端在收到消息之后，再给客户端回送一条消息，之后再各自关闭服务

```java
// 服务端
public class Server2 {

    public void server() throws IOException {

        System.out.println("服务端监听 9999 端口~");
        // 服务端监听9999端口
        ServerSocket serverSocket = new ServerSocket(9999);

        //阻塞监听
        Socket socket = serverSocket.accept();

        //拿到输入流
        InputStream inputStream = socket.getInputStream();

        byte[] buf = new byte[1024];

        int readLen = 0;

        //读取客户端发送的数据
        while ((readLen = inputStream.read(buf)) != -1) {
            System.out.println(new String(buf, 0, readLen));
        }

        //拿到输出流
        OutputStream outputStream = socket.getOutputStream();
        //回应给客户端的数据
        outputStream.write("Hello Client".getBytes());

        //写入内容结束标记.(很重要，如果没有结束标记，会出大问题，不知道什么时候结束)
        socket.shutdownOutput();
        //关闭输出
        outputStream.close();

        inputStream.close();

        socket.close();

        serverSocket.close();

        System.out.println("服务端退出~");
    }
}

```

![server-03](https://qiniu.lglg.xyz/images/lglg/posts/202206/06/server-03.png)

````java
// 客户端

public class Client2 {

    public void client() throws IOException {
        System.out.println("客户端开始连接~");
        Socket socket = new Socket(InetAddress.getLocalHost(), 9999);

        //拿到输出流
        OutputStream outputStream = socket.getOutputStream();
        //写数据给服务端
        outputStream.write("Hello Server".getBytes());
        //写入结束标记
        socket.shutdownOutput();

        //拿到输入流
        InputStream inputStream = socket.getInputStream();

        byte[] buf = new byte[1024];

        int readLen = 0;
        //服务端回应给客户端的数据
        while ((readLen = inputStream.read(buf)) != -1) {
            System.out.println(new String(buf, 0, readLen));
        }

        //关闭资源
        inputStream.close();

        outputStream.close();

        socket.close();

        System.out.println("客户端退出~");
    }
}
````

![client-01](https://qiniu.lglg.xyz/images/lglg/posts/202206/06/client-01.png)

******* 

> 通过上面两个简单的测试可以看出`Socket`类得到的输入输出流都是以字节形式的，刚好可以结合前面一张io流的知识，可以通过转换流的形式，把字节转化为字符

```java
//得到字节输出流
OutputStream outputStream = socket.getOutputStream();
//转换为字符输出流
BufferedWriter bufferedWriter = new BufferedWriter(new OutputStreamWriter(outputStream));

//同理字节输入流
InputStream inputStream = socket.getInputStream();

BufferedReader bufferedReader = new BufferedReader(new InputStreamReader(inputStream));
```

##### 服务端读取文件，转发给客户端

* 读取二进制文件数据 `new FileInputStream(dFile)`
* 字节缓冲输入流 ``BufferedInputStream bis = new BufferedInputStream(new FileInputStream(dFile));``
* 字节数组输出流：把数据转换为字节数据

```java
ByteArrayOutputStream byteArrayOutputStream = new ByteArrayOutputStream();

byte[] b = new byte[1024];

int len = 0;

while ((len = inputStream.read(b)) != -1) {
    byteArrayOutputStream.write(b, 0 , len);
}

byte[] array = byteArrayOutputStream.toByteArray();

byteArrayOutputStream.close();
```

* 字节缓冲输出流 `BufferedOutputStream bos = new BufferedOutputStream(socket.getOutputStream());`
* 写入数据

```java
bos.write(bytes);
bos.flush();
socket.shutdownOutput();
```

##### java当中的UDP编程

##### 接收端（也可以变为发送端）**既要发送数据还要接收数据**

* ``DatagramSocket.receive(new DatagramPacket(buf, buf.length))``

##### 发送端（也可以变为接收端）**既要发送数据还要接收数据**

* ``DatagramSocket.send(new DatagramPacket(bytes, bytes.length, InetAddress.getLocalHost(), port))``

##### 重要的类

* `DatagramSocket` 端口监听
* `DatagramPacket` 数据打包


##### 测试用例

```java
public class Udp_ {

    /**
     *
     */
    public void sendData() throws IOException {

        System.out.println("我目前是发送端~");

        DatagramSocket socket = new DatagramSocket(9998);

        sen(socket, "hello 明天吃火锅呀", 9999);

        res(socket);

        socket.close();

        System.out.println("发送端退出~");
    }

    public void sen(DatagramSocket socket, String s, int port) throws IOException {
        byte[] bytes = s.getBytes();

        DatagramPacket packet = new DatagramPacket(bytes, bytes.length, InetAddress.getLocalHost(), port);

        socket.send(packet);
    }

    public void res(DatagramSocket socket) throws IOException {
        byte[] buf = new byte[1024];

        DatagramPacket datagramPacket = new DatagramPacket(buf, buf.length);

        System.out.println("接收端： 等待接收数据~");

        socket.receive(datagramPacket);

        int len = datagramPacket.getLength();

        byte[] data = datagramPacket.getData();

        String s = new String(data, 0, len);

        System.out.println(s);
    }

    public void receiveData() throws IOException {
        System.out.println("我目前是接收端~");
        DatagramSocket datagramSocket = new DatagramSocket(9999);

        res(datagramSocket);

        sen(datagramSocket, "好的，明天见~", 9998);

        datagramSocket.close();

        System.out.println("接收端退出~");
    }
}

```




























