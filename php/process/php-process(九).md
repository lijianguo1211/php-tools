### 关于PHP多进程编程九

* 关于事件模型中经常讲的阻塞和非阻塞

|名字|释义
|:---:|:---:|
|阻塞|去超市买菜，买完菜排队去结账，前面的没有结账完，后面的人就排队等着，这个阶段是什么都做不了，只能排队等待着
|非阻塞|同样去超时买菜，买完菜排队去结账，到了结账的地方，一看嗨，人这么多，超时的收银人员说了，你可以先做点别的事儿，直到到你了才开始结账
|同步|这个阶段就是通知的主动性，比如排队结账，同步就是需要客户不停地问，前面的人是否已经结账了，到我了没没有呀
|异步|异步就是在这个阶段，前面的人已经结账了，收银人员主动告知你，嗨，到你了，快来结账吧
|同步阻塞|排队结账的时候，需要等待着前面的收银人员，这个阶段什么都不做，就是等着前面的完成，然后主动的询问是否到自己了
|同步非阻塞|排队结账的时候，需要等待着前面的收银人员，这个时候可以看看手机电影，听音乐，打电话等等，最后再询问是否到自己了
|异步阻塞|排队结账的时候，需要等待着前面的收银人员，这个阶段什么都不做，就是等着前面的完成，然后收营员通知到你结账了
|异步非阻塞|排队结账的时候，需要等待着前面的收银人员，这个时候可以看看手机电影，听音乐，打电话等等，最后收营员通知到你结账了

* 关于I/O多路复用机制（select,poll,epoll）

> PHP中关于I/O多路复用的操作函数并不是很多，其中`stream_select`函数是PHP不依赖外部扩展的`select`类型的，而`poll和epoll`类型的就需要依赖外部
扩展了，`libevent`扩展，但是它对高版本的PHP就不支持了，最好的还算`event`扩展了，它提供了从低到高各个版本的支持

|名字|释义|
|:---:|:---:|
|`select`|同步多路IO复用，基于轮训机制，单进程可以打开fd有限制；对socket进行扫描时是线性扫描，即采用轮询的方法，效率较低；用户空间和内核空间的复制非常消耗资源；
|`poll`|同步多路IO复用，算是`select`的升级版，采用链表的方式替换原有fd_set数据结构,而使其没有连接数的限制
|`epoll`|同步多路IO复用，基于操作系统支持的I/O通知机制 epoll支持水平触发和边沿触发两种模式，连接数没有限制，性能好

* 关于PHP使用`event`扩展的相关函数

|类|功能|
|:---:|:---:|
|[Event](https://php.net/manual/en/class.event.php)|具体的事件|
|[EventBase](https://php.net/manual/en/class.eventbase.php)|事件基础结构|
|[EventConfig](https://www.php.net/manual/en/class.eventconfig.php)|事件的配置类|
|[EventBuffer](https://php.net/manual/en/class.eventbuffer.php)|一种用于缓冲 I/O 的实用程序功能|
|[EventBufferEvent](https://php.net/manual/en/class.eventbufferevent.php)|表示 Libevent 的缓冲事件|
|[EventDnsBase](https://www.php.net/manual/en/class.eventdnsbase.php)|用于异步解析 DNS|
|[EventHttp](https://www.php.net/manual/en/class.eventhttp.php)|代表 HTTP 服务器|
|[EventHttpConnection](https://www.php.net/manual/en/class.eventhttpconnection.php)|表示 HTTP 连接|
|[EventHttpRequest](https://www.php.net/manual/en/class.eventhttprequest.php)|表示一个 HTTP 请求|
|[EventListener](https://www.php.net/manual/en/class.eventlistener.php)|表示连接侦听器|
|[EventSslContext](https://www.php.net/manual/en/class.eventsslcontext.php)|代表 SSL_CTX 结构。提供方法和属性来配置 SSL 上下文|
|[EventUtil](https://www.php.net/manual/en/class.eventutil.php)|EventUtil 是一个带有补充方法和常量的单例|


* 基于`event`实现一个PHP版本的定时器

```php
$config = new EventConfig();

$base = new EventBase($config);

//Event::TIMEOUT 代表定时器
//Event::PERSIST  代表是持久的
$event = new \Event($base, -1, Event::TIMEOUT|Event::PERSIST, function () {
   echo microtime(true) . PHP_EOL;
});

//0.5秒间隔输出一次
$event->add(0.5);

$base->loop();

### output
//jayli@DESKTOP-OOHU3CT:/d/php/event$ php pcntl.php
//1649066901.8597
//1649066902.3604
//1649066902.8599
//1649066903.3602
//1649066903.8597
//1649066904.3605
//1649066904.8598
//1649066905.3602
//1649066905.8601
```

* 关于定时器之`Event::timer`实现[一次性]

```php
$config = new EventConfig();

$base = new EventBase($config);

$i = 3;

$event = Event::timer($base, function ($arr) use (&$event) {
    [$num] = $arr;
    echo $num . "\t" .  microtime(true). PHP_EOL;

    $event->delTimer();
}, [&$i]);

$event->add($i);

$base->loop();
```

* 关于信号设置

```php
$config = new EventConfig();

$base = new EventBase($config);

$pid = pcntl_fork();

if ($pid === 0) {
    echo "child process pid = " . posix_getpid() . "\n";
    sleep(5);

    // 子进程退出发送型号
    exit();
}

//SIGCHLD 注册SIGCHLD信号处理
$event = new Event($base, SIGCHLD, \Event::SIGNAL|\Event::PERSIST, function ($fd) {
    echo "调用了信号 $fd \n";
});

$event->add();
$base->loop();

pcntl_async_signals(true);

$pid = pcntl_wait($status, WUNTRACED);

echo "child pid = " . $pid . "\n";

while (1) {
    sleep(5);
}
```

