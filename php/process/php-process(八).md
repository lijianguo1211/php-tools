### 关于PHP多进程编程八

* 关于进程（孤儿进程，僵尸进程，守护进程）

> 孤儿进程，通过`fork`出的子进程还在“运行”，父进程却异常退出了，这个时候子进程还未退出，子进程被init进程接管

```php
$pid = pcntl_fork();

if ($pid === 0) {
    echo "child process pid = " . posix_getpid() . "\n";
    while (1) {
        sleep(5);
    }
}

    throw new \Exception("主动异常结束父进程~\n");
### output
//jayli@DESKTOP-OOHU3CT:/d/php/event$ php pcntl.php
//PHP Fatal error:  Uncaught Exception: 主动异常结束父进程~
// in /d/php/event/pcntl.php:347
//Stack trace:
//#0 /d/php/event/pcntl.php(350): test14()
//#1 {main}
//  thrown in /d/php/event/pcntl.php on line 347
//child process pid = 67
//jayli@DESKTOP-OOHU3CT:/d/php/event$

### pstree
//init─┬─init───bash
//     ├─init───bash───pstree
//     ├─php
//     └─2*[{init}]
```

> 僵尸进程，通过`fork`出的子进程退出了，但是在父进程中并没有调用`wait`方法监控子进程退出，这个时候子进程的进程描述符还保存在系统中

```php
$pid = pcntl_fork();

if ($pid === 0) {
    echo "child process pid = " . posix_getpid() . "\n";
    exit();
}

echo "main process pid = " . posix_getpid() . "\n";

while (1) {
    sleep(5);
}
```
> 守护进程，`fork`出一个子进程，然后把主进程退出，子进程被`init`进程接管,关于守护进程的[具体实现](https://www.lglg.xyz/article/guan-yu-php-zhong-de-duo-jin-cheng-bian-cheng-yi.html)

```php
$pid = pcntl_fork();

if ($pid === 0) {
    echo "child process pid = " . posix_getpid() . "\n";
    while (true) {
        sleep(3);
    }
} elseif ($pid > 0) {
    echo "main process exit~\n";
    exit(1);
}
```

* 这里看看PHP如何简单粗暴修改进程名`cli_set_process_title`

```php

### output
$pid = pcntl_fork();

if ($pid === 0) {
    echo "child process pid = " . posix_getpid() . "\n";
    cli_set_process_title("jay child process~");
    while (1) {
        sleep(5);
    }
} elseif ($pid > 0) {
    cli_set_process_title("jay parent process~");
    echo "main process pid = " . posix_getpid() . "\n";
}

while (1) {
    sleep(5);
}
// ps -ef | grep jay
//jayli       96     9  0 11:15 tty1     00:00:00 jay parent pro
//jayli       97    96  0 11:15 tty1     00:00:00 jay child proc
```

* 关于进程中的信号控制，主要是扩展时`posix`

* 注册信号 `pcntl_signal(信号编号， 信号处理程序|系统常量，"存在bug参数")`

```php
pcntl_signal(SIGUSR1, function ($sig) {
    echo "当前接收到的信号量是： " . $sig . PHP_EOL;
});
pcntl_signal(SIGUSR2, function ($sig) {
    echo "当前接收到的信号量是： " . $sig . PHP_EOL;
});
```

* 调用等待的信号 `pcntl_signal_dispatch`

```php
pcntl_signal(SIGUSR1, function ($sig) {
    echo "当前接收到的信号量是： " . $sig . PHP_EOL;
});
pcntl_signal(SIGUSR2, function ($sig) {
    echo "当前接收到的信号量是： " . $sig . PHP_EOL;
});

posix_kill(posix_getgid(), SIGUSR1);

sleep(2);

posix_kill(posix_getgid(), SIGUSR2);
while (1) {
    //调用等待信号的处理器
    pcntl_signal_dispatch();

    sleep(1);
}

//在另外一个窗口发送信号
//jayli@DESKTOP-OOHU3CT:/c/Users/jay.li$ kill -10 105
//jayli@DESKTOP-OOHU3CT:/c/Users/jay.li$ kill -12 105

//output 
//jayli@DESKTOP-OOHU3CT:/d/php/event$ php pcntl.php
//当前接收到的信号量是： 10
//当前接收到的信号量是： 12
```

* `pcntl_async_signals` 启用/禁用异步信号处理或返回旧设置

```php
    pcntl_async_signals(true);
    pcntl_signal(SIGUSR1, function ($sig) {
        echo "当前接收到的信号量是： " . $sig . PHP_EOL;
    });
    pcntl_signal(SIGUSR2, function ($sig) {
        echo "当前接收到的信号量是： " . $sig . PHP_EOL;
    });

    $pid = posix_getpid();

    echo "this process pid = " . $pid . PHP_EOL;

    posix_kill($pid, SIGUSR1);

    posix_kill($pid, SIGUSR2);

    while (1) {
        sleep(3);
    }
```

* `pcntl_sigprocmask`,信号阻塞处理,收到信号暂时不去处理，而要等待需要去处理的时候再去处理，比如程序中`reload`。就是master进程收到信号之后，需
要看子进程是否是繁忙状态，如果是，就等待子进程处理完当前的任务之后，再去处理重启逻辑。

```php
pcntl_async_signals(true);
pcntl_signal(SIGUSR1, function ($sig) {
    echo "当前接收到的信号量是： " . $sig . PHP_EOL;
}, false);

$oldSet = [];
pcntl_sigprocmask( SIG_BLOCK, [SIGUSR1], $oldSet);

posix_kill(posix_getpid(), SIGUSR1);
$i = 0;
while (1) {
    echo $i . "\t";
    $i++;
    sleep(2);
    if ($i === 10) {
        pcntl_sigprocmask( SIG_UNBLOCK, [SIGUSR1], $oldSet);
        print_r($oldSet);
    }
}

//SIG_BLOCK: 把信号加入到当前阻塞信号中。
//SIG_UNBLOCK: 从当前阻塞信号中移出信号。
//SIG_SETMASK: 用给定的信号列表替换当前阻塞信号列表。

### output
//0       1       2       3       4       5       6       7       8       9       当前接收到的信号量是： 10
//Array
//(
//    [0] => 10
//)
//10      11      12      13      14      ^C

```

* 接着说父子进程，如果父进程安装了一个信号处理器，子进程是会继承父进程的信号处理器的

```php
pcntl_async_signals( true );
pcntl_signal(SIGUSR1, function ($sig) {
    $pid = getmypid();
    echo "pid = $pid 当前接收到的信号量是： " . $sig . PHP_EOL;
});

$pid = pcntl_fork();

if ($pid === 0) {
    echo "child process pid = " . posix_getpid() . "\n";
    while (1) {
        sleep(2);
    }
} elseif ($pid > 0) {
    echo "parent process pid = " . posix_getpid() . "\n";
}

while (1) {
    sleep(3);
}

### output
//parent process pid = 132
//child process pid = 133
//pid = 133 当前接收到的信号量是： 10
//pid = 132 当前接收到的信号量是： 10
```

* 利用信号处理器，可以很好的检测子进程的退出或重启，如果不是使用信号处理，就需要使用`wait`函数阻塞等待

```php
$pid = pcntl_fork();

if ($pid === 0) {
    echo "child process pid = " . posix_getpid() . "\n";
    echo "child process 10s exit~\n";
    sleep(3);
    exit();
}

pcntl_async_signals(true);

echo "parent pid = " . posix_getpid() . "\n";
pcntl_signal(SIGCHLD, function ($sig) use ($pid) {
    echo "收到SIGCHLD $sig 信号，有子进程退出 pid = $pid".PHP_EOL;
    pcntl_waitpid($pid, $status, WNOHANG);
    print_r( $status );
}, false);

while (1) {
    sleep(5);
}

### output 
//parent pid = 194
//child process pid = 195
//child process 10s exit~
//收到SIGCHLD 17 信号，有子进程退出 pid = 195
//0^C
```