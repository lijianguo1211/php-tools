### 关于PHP中的多进程编程(五)

> 上一篇当中，可以算PHP的单进程的socket监听，监听事件也是使用PHP自带的Select调度模式。本篇就简单使用多进程加epoll事件调度模式编写

##### 关于当前的多进程需要知道几点

* `pcntl_fork`出子进程做`socket`监听, 父进程或者说是主进程做子进程监听控制，比如停掉，重启子进程等等

* 使用`event`扩展，做事件调度，第一次添加`stream_socket_server`创建的服务器套接字，在第一次的回调中，`stream_socket_accept`接收由服务端的
套接字，第二次添加，`stream_socket_accept`返回的套接字添加到事件调度中

* 是否开启端口复用，如果开启端口复用，监听socket就是在子进程中，不开启端口复用，监听socket就在外

* **子进程之间操作的数据是隔离的**

* 子进程可以读父进程的变量数据

* 父进程没法读取子进程的数据

```php
static $arr = [];

for ($i = 0; $i < 3; $i++) {
    $id = posix_getpid();

    $arr[$id][] = mt_rand(1, 10) . '-' . microtime(true);
}

for ($i = 0; $i < 2; $i++) {
    $pid = pcntl_fork();

    if ($pid === -1) {
        exit("fork process failed~\n");
    } elseif ($pid === 0) {
        sleep(5);
        $id = posix_getpid();
        for ($i = 0; $i < 5; $i++) {
            $key = $id . '-' . $i;
            $arr[$key] = mt_rand(1, 10) . '-' . microtime();
        }
        while (true) {
            print_r($arr);
            sleep(10);
        }
    } else {

    }
}

while (true) {
    print_r($arr);
    sleep(10);
}

### output
//Array
//(
//    [328] => Array
//        (
//            [0] => 1-1647491341.4108
//            [1] => 7-1647491341.4108
//            [2] => 9-1647491341.4108
//        )
//
//)
//Array
//(
//    [328] => Array
//        (
//            [0] => 1-1647491341.4108
//            [1] => 7-1647491341.4108
//            [2] => 9-1647491341.4108
//        )
//
//    [329-0] => 2-0.50702900 1647491346
//    [329-1] => 4-0.50726400 1647491346
//    [329-2] => 7-0.50726800 1647491346
//    [329-3] => 4-0.50727000 1647491346
//    [329-4] => 9-0.50727200 1647491346
//)
//Array
//(
//    [328] => Array
//        (
//            [0] => 1-1647491341.4108
//            [1] => 7-1647491341.4108
//            [2] => 9-1647491341.4108
//        )
//
//    [330-0] => 2-0.51156300 1647491346
//    [330-1] => 4-0.51177200 1647491346
//    [330-2] => 7-0.51177500 1647491346
//    [330-3] => 4-0.51177700 1647491346
//    [330-4] => 9-0.51177900 1647491346
//)
//Array
//(
//    [328] => Array
//        (
//            [0] => 1-1647491341.4108
//            [1] => 7-1647491341.4108
//            [2] => 9-1647491341.4108
//        )
//
//)
```

* 全局的event事件注册，是在子进程中注册。也就是说每个子进程中都注册了一个属于自己的event,只对自己进程负责

* 监听服务程序的大概结构[参考workerman](https://www.workerman.net/)

![监听流程](./worker.jpg)

![程序主要的程序](./worker-2.jpg)

* 第一步创建一个`Worker`类，属于`master`进程[人为设置]，在`Worker`类中创建的子进程属于`worker`进程

* 初始化全局的参数，子进程`worker`是可以继承父进程`master`

|回调函数|何时调用|
|:---:|:---:|
`onWorkerStart`| 子进程`worker`启动，调度未决事件之前 `event-loop()`
`onConnect`| 成功建立了套接字`stream_socket_accept`
`onMessage`| 发送数据给客户端
`onClose`| 关闭客户端连接
`onError`| 客户端连接发生错误
`onBufferFull`| 发送缓冲区不够溢出
`onBufferDrain`| 发送缓冲区为空
`onWorkerStop`| 工作进程停止
`onWorkerReload`| 工作进程重启
`onMasterReload`| `master`进程重启
`onMasterStop`|  `master`进程停止

* 用户可以自定义设置的参数

|参数|释义
|:---:|:---:|
name|用户设置的进程名
user|程序运行用户
group|程序运行用户组
count|设置子进程数量
reusePort|端口复用
transport|传输层协议
protocol|应用层协议
*** | ***
pidFile|`master`进程id保存文件
statusFile|`master`进程状态保存文件
logFile|程序不设计用户逻辑日志文件
globalEvent| 事件类
masterPid| `master` 进程id
mainSocket| 监听的套接字 `stream_socket_server`
_context| 创建套接字 `stream_socket_server`的上下文`stream_context_create`
_workers| 保存程序的`master`进程
_status| 保存程序运行的状态，只有`master`进程操作

* 整个程序的结构

```
├──Connection
│   ├── ConnectionInterface 定义传输层接口
│   ├── TcpConnection
├──Enums
│   ├── RunningStatus 定义运行状态
├──Events
│   ├── EventInterface 定义事件接口
│   ├── Event
├──Exceptions 定义自定义异常
│   ├── ProcessException
│   ├── SocketCreateServerException
├──Protocol 定义应用层
│   ├── ProtocolInterface 应用层接口
├──Traits
│   ├── MessageCallback 定义的事件回调
│   ├── SocketTrait 定义主要socket相关
├──RunConfig 定义各个公用的数据属性方法
└──worker 对外提供程序
```

* 关于[socket]操作

> 监听套接字，把套接字放入event,并添加相关的闭包处理

* 关于[Event]事件的监听

> 事件`Event`定义一个接口，根据程序安装的扩展，选择对应的事件处理，接口中主要有添加事件`add`,删除事件`del`,调度事件`loop`

* 关于应用层和传输层

> 从应用层得到包，传输层解析具体的数据

* 关于多进程的处理

> 子进程的停止，重启，主进程停止，重启，








