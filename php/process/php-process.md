### 关于PHP中的多进程编程(一)

* 需要使用的到的扩展

|扩展|作用|安装
|:---:|:---:|:---:|
pcntl| 操作进程|编译安装 `--enable=pcntl`
poisx| 操作进程（辅助）|编译安装 `--enable=poisx`

* 创建一个子进程 `pcntl_fork()`, 返回值有其三，等于`-1`失败，等于`0`进入子进程处理逻辑，`>0`进入父进程逻辑，这个`>0`的数值就是子进程的`pid`

```php
$pid = pcntl_fork();

switch ($pid) {
    case -1:
        exit("fork process failed~\n");
        break;
    case 0:
        for ($i = 0; $i < 5; $i++) {
            echo "create child $i process success~\n";
        }
       
        break;
    default:
        
        for ($i = 0; $i < 5; $i++) {
             echo "this process $i is main, child process pid = $pid~\n";
        }
        break;
}

### output
//this process 0 is main, child process pid = 483~
//create child 0 process success~
//this process 1 is main, child process pid = 483~
//create child 1 process success~
//this process 2 is main, child process pid = 483~
//create child 2 process success~
//this process 3 is main, child process pid = 483~
//create child 3 process success~
//this process 4 is main, child process pid = 483~
//create child 4 process success~
```

* `pcntl_wait | pcntl_waitpid` 等待或返回 fork 的子进程状态

```php
$fun2 = function () {
    $pid = pcntl_fork();

    switch ($pid) {
        case -1:
            exit("fork process failed~\n");
            break;
        case 0:
            for ($i = 0; $i < 5; $i++) {
                echo "create child $i process success~\n";
                sleep(mt_rand(1, 3));
            }

            break;
        default:
//         pcntl_wait($status, WNOHANG);//如果没有子进程退出立刻返回
         pcntl_wait($status, WUNTRACED);//阻塞等待
         //$pid = -1 的时候，是等待所有子进程
         pcntl_waitpid($pid, $status, WUNTRACED);
            for ($i = 0; $i < 5; $i++) {
                echo "this process $i is main, child process pid = $pid~\n";
            }
            break;
    }

   

   echo "main \n";
};

$fun2();

# output
//create child 0 process success~
//create child 1 process success~
//create child 2 process success~
//create child 3 process success~
//create child 4 process success~
//main
//this process 0 is main, child process pid = 502~
//this process 1 is main, child process pid = 502~
//this process 2 is main, child process pid = 502~
//this process 3 is main, child process pid = 502~
//this process 4 is main, child process pid = 502~
//main
```

* 创建多个子进程

> 查询进程之间的关系 `ps axjf | grep php` 
> 查看父进程下有多少个子进程 `ps --ppid 555`

```php
class Pid
{
    public static array $pidArr = [];
}

$fun3 = function () {
    $pid = pcntl_fork();

    switch ($pid) {
        case -1:
            exit("Created process failed~\n");
            break;

        case 0:
            $num = mt_rand(30, 50);
            for ($i = 0; $i < $num; $i++) {
                sleep(1);
            }
            break;

        default:
            Pid::$pidArr[$pid] = $pid;
            break;
    }
};

$fun4 = function ($num, $fun3) {
    for ($i = 0; $i < $num; $i++) {
        $fun3();
    }

    foreach (Pid::$pidArr as $pid) {
        echo "Child process pid = $pid ~ \n";
        pcntl_waitpid($pid, $status, WUNTRACED);
    }
};

$fun4(3, $fun3);
```

* 得到当前的进程id `posix_getpid | getmypid`

```php
$fun5 = function () {
    $pid = pcntl_fork();

    switch ($pid) {
        case -1:
            exit("Created process failed~\n");
            break;
        case 0:
           echo "Created child process pid = " . getmypid() . PHP_EOL;
           echo "Created child process pid = " . posix_getpid() . PHP_EOL;
            break;

        default:
            echo "Created parent process pid = " . getmypid() . PHP_EOL;
            echo "Created parent process pid = " . posix_getpid() . PHP_EOL;
            break;
    }
};

# output
//Created parent process pid = 562
//Created child process pid = 563
//Created parent process pid = 562
//Created child process pid = 563
```

* 安装一个信号处理 `pcntl_signal`

```php
$fun6 = function () {
    set_error_handler(function ($errno, $es, $ef, $el) {
        var_dump($es);
    });
    $pid = pcntl_fork();

    switch ($pid) {
        case -1:
            exit("Created process failed~\n");
            break;
        case 0:


            $pid = 0;

            pcntl_signal(SIGALRM, function () use (&$pid) {

                $pid = posix_getpid();
            }, false);

            $seconds = pcntl_alarm(2);


            for ($i = 0; $i < 3; $i++) {
                pcntl_async_signals(true);
                sleep(3);

                echo "kill pid $pid ~ \n";

            }
            break;

        default:

            echo "parent process pid = " . posix_getpid() . PHP_EOL;
            echo "child process pid = " . $pid . PHP_EOL;

            pcntl_waitpid($pid, $status, WUNTRACED);

            $i = 0;
            while ($i < 5) {
                echo $i++ . "\n";
                sleep(1);
            }
            break;
    }



    restore_error_handler();
};

$fun6();

## output

//parent process pid = 643
//child process pid = 644
//kill pid 644 ~
//kill pid 644 ~
//kill pid 644 ~
//0
//1
//2
//3
//4
```

* 设置运行的程序为守护进程，后台运行

|序号|操作
|:---:|:---:|
1|`pcntl_fork`
2|`exit`父进程
3|`posix_setsid ` 使子进程成为主会话
4|`umask` 改变当前的 umask
5|`chdir` 改变目录
6|`fopen` 关闭一些输入输出文件句柄

```php
function daemonize()
{
    $pid = pcntl_fork();

    switch ($pid) {
        case -1:
            exit("fork fail ~ \n");
            break;
        case 0:
            // Make the current process a session leader
            if (($sid = posix_setsid()) <= 0) {
                die("set sid = $sid failed \n");
            }

            if (chdir("/") === false) {
                die("change dir failed ~\n");
            }

            umask(0);

            fclose(STDIN);
            fclose(STDOUT);
            fclose(STDERR);
            break;
        default:
            file_put_contents("./d.pid", $pid);
            exit();
            break;
    }
}
```