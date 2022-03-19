### 关于PHP中的多进程编程(三)

* 关于事件循环扩展安装 `event`

|系统|依赖名|PHP扩展|
|:---:|:---:|:---:|
ubuntu|libevent-dev|event
--- | 编译安装-apt-get安装|编译安装

* 编译安装 ``event`` 扩展

|步骤|命令|解释
|:---:|:---:|:---:|
1| `wget https://pecl.php.net/get/event-3.0.6.tgz`| 下载扩展包
2| `tar -zxvf event-3.0.6.tgz` | 解压包
3| `cd event-3.0.6` | 进入文件
4| `/usr/local/php/bin/phpize` | 生成对应系统的 `./configure` 文件
5| `./configure --with-event-core --with-event-extra --enable-event-debug`| --
6| `make && make install` | 编译安装
7| `extension=event.so` | 添加到php.ini文件
8| `php -m | grep event` | 检查是否安装成功

* `EventConfig` 可以配置的event设置，传递给`EventBase`

|方法|解释
|:---|:---:|
`avoidMethod`| 告诉 libevent 避免使用指定 event 方法
`requireFeatures`| 输入应用程序要求的必需 event 方法功能
`setFlags`| EventBase 初始化需设置的一个或者多个 flag
`setMaxDispatchInterval `| 防止优先级反转

```php
$eventConfig = new EventConfig();

//select 方法失效
$eventConfig->avoidMethod("select");

//开启FDS功能
$eventConfig->requireFeatures(EventConfig::FEATURE_FDS)
```

* `EventBase` 

|方法|解释
|:---:|:---|
`getMethod ` | 返回受支持[当前使用]的事件调度方法
`getFeatures ` | 返回受支持[当前使用]的事件掩码，`EventConfig`可设置
`getTimeOfDayCached ` | 返回当前事件基准时间
`exit` | 停止事件调度
`stop` | 告诉EventBase停止调度事件
`gotStop` | 检查事件循环是否被 EventBase::stop()告知退出 。
`gotExit` | 检查事件循环是否被 EventBase::exit()告知退出 。
`free` |  Free resources allocated for this event base
`dispatch` | 调度挂起的事件,等待事件激活，然后运行它们的回调。与未设置标志的EventBase::loop()相同 。
`loop` | 调度挂起的事件,等待事件激活，然后运行它们的回调。
`priorityInit` | 设置每个事件库的优先级数
`reInit` | 重新初始化事件库（在 fork 之后)

```php
$base = new EventBase();
echo "current lib event method: " . $base->getMethod() . PHP_EOL;
echo "current lib event flags: " . $base->getFeatures() . PHP_EOL;
echo "current lib event times: " . $base->getTimeOfDayCached() . PHP_EOL;

$event = new Event($base, STDIN, Event::READ | Event::PERSIST, function ($fd, $events, $arg) {
    static $maxNumber = 0;
    
    if (++$maxNumber > 10) {
        
        //2.43秒后停止
        $arg->exit(2.43);
   
    }
    
    echo fgetc($fd) . PHP_EOL;
}, &$base);

$event->add();

$base->dispatch(); // == $base->loop();

while (true) {
    if ($base->gotExit()) {
        echo "事件已退出！\n";
        break;
    }
}
```

* `Event` 

|方法|解释
|:---:|:---|
`add` | 挂起事件
`addSignal` | 挂起信号事件
`addTimer` | 挂起定时器事件
`del` | 从挂起事件中删除，使之成为非挂起事件
`delSignal` | 从挂起信号事件中删除，使之成为非挂起信号事件
`delTimer` | 从挂起定时器事件中删除，使之成为非挂起定时器事件
`signal` | 构造信号事件对象
`timer` | 构造定时器事件对象
`setTimer` | 重新配置定时器事件
`setPriority` | 设置一个事件的优先级
`set` | 重新配置事件
`pending` | 检测事件是挂起还是已安排
`free` | 从 libevent 监视的事件列表中删除事件，并释放为事件分配的资源。

```php
$fun2 = function () {
    $base = new EventBase();

//    $event = new Event($base, STDIN, Event::READ | Event::PERSIST, function ($fd, $events, $arg) {
//        static $maxIterations = 0;
//
//        if (++$maxIterations >= 5) {
//            echo "Stopping...\n";
//
//            $arg[0]->exit(2.33);
//        }
//
//        echo fgets($fd);
//    }, [&$base]);

//    $event->add();

    $event = Event::signal($base, \SIGTRAP, function ($sig, $arg) {

        echo "listen sig = $sig" . PHP_EOL;

        $arg[0]->exit();


    }, [&$base]);

    $event->add();

    $pid = pcntl_fork();

    if ($pid == -1) {
        exit("Create process failed~\n");
    }

    if ($pid === 0) {
        
        $ppid = posix_getpid();

        echo "Create child process success pid = $ppid\n";

        $num = 0;
        while (1) {
            echo $num . PHP_EOL;
            sleep(1);
            $num++;
            if ($num > 10) {
                posix_kill($ppid, \SIGTRAP);
                break;
            }
        }

    } else {
        echo "Create parent process success pid = " . posix_getpid() . PHP_EOL;
        $base->loop();
        while (true) {
            if ($base->gotExit()) {
                echo "信号事件已经退出~\n";
                break;
            }

            sleep(2);
        }

        pcntl_waitpid($pid, $status, WUNTRACED);

        echo "parent process wait child pid = $pid parent pid = " . posix_getpid() . PHP_EOL;

        while (true) {
            sleep(5);
            echo time() . PHP_EOL;
        }
    }
};

$fun2();
### output
//Create parent process success pid = 215
//Create child process success pid = 216
//0
//1
//2
//3
//4
//5
//6
//7
//8
//9
//10
//listen sig = 5
//信号事件已经退出~
//parent process wait child pid = 216 parent pid = 215
//1647099831
//1647099836
```