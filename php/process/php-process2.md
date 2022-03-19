### 关于PHP中的多进程编程(二)

* `posix_kill` 向进程发送信号

```php
posix_kill($pid, SIGHUP);//终止进程，终端线路挂断
posix_kill($pid, SIGINT);//终止进程，ctrl+c
posix_kill($pid, SIGQUIT);//建立core文件终止进程，并生成core文件，ctrl+\
posix_kill($pid, SIGKILL);//终止进程
posix_kill($pid, SIGUSR1);//终止进程,用户自定义信号
posix_kill($pid, SIGUSR2);//终止进程，用户自定义信号
posix_kill($pid, SIGPIPE);//终止进程，向一个没有读进程的管道写数据
posix_kill($pid, SIGALRM);//终止进程，计时器到时
posix_kill($pid, SIGTERM);//终止进程，软件终止信号
posix_kill($pid, SIGCHLD);//忽略信号 当子进程停止或退出时通知父进程
posix_kill($pid, SIGSTOP);//停止进程，非终端来的停止信号
posix_kill($pid, SIGTSTP);//停止进程，终端来的停止信号 ctrl+z
posix_kill($pid, SIGTTIN);//停止进程，后台进程读终端
posix_kill($pid, SIGTTOU);//停止进程，后台进程写终端
posix_kill($pid, SIGXCPU);//终止进程，cpu限时超时
posix_kill($pid, SIGXFSZ);//终止进程，文件长度过长
posix_kill($pid, SIGVTALRM);//终止进程，虚拟计时器到时


$fun7 = function () {
    $pid = pcntl_fork();

    switch ($pid) {
        case -1:
            exit("fork child process failed~\n");
        case 0:
            $childPid = posix_getpid();

            pcntl_signal(\SIGTERM, SIG_DFL, false);

            while (true) {
                echo "child process get pid = $childPid ~ \n";

                sleep(3);
            }

            break;
        default:

            sleep(5);

            pcntl_signal(\SIGCHLD, function () use ($pid) {
                echo "chile process pid = $pid is killed~\n";
            }, false);

            posix_kill($pid, \SIGKILL);

            for ($i = 0; $i < 10; $i++) {
                $parentPid = posix_getpid();
                echo "parent process get pid = $parentPid ~ \n";
                sleep(2);
            }
            break;
    }

    echo "Done ~\n";
};

$fun7();
```

* `posix_getpid` 返回当前的进程id

```
$pid = posix_getpid();
```

* `posix_getppid` 返回子进程的父进程id

```php
$pid = posix_getppid();
```

* `posix_getcwd` 返回当前进程的工作目录

```php
$dir =  posix_getcwd();
```

* `posix_getegid` 返回所属组id

```php
$groupId = posix_getegid();
```

* `posix_geteuid` 返回当前的运行用户id

```php
$uid = posix_geteuid();
```

* `posix_getgid` 当前进程的真实组id

```php
$uid = posix_getgid();
```

* `posix_getgrgid` 返回组有关的信息

```php
$info = posix_getgrgid($gid);

print_r($info);
## output

//Array
//(
//    [name] => jayli
//    [passwd] => x
//    [members] => Array
//        (
//        )
//
//    [gid] => 1000
//)
```

* `posix_getgrnam`按名称返回有关组的信息

```php
$info = posix_getgrnam("www");

print_r($info);

## output
//Array
//(
//    [name] => www
//    [passwd] => x
//    [members] => Array
//        (
//        )
//
//    [gid] => 1001
//)
```

* `posix_getgroups` 返回当前进程的组集

```php
$info = posix_getgroups();

print_r($info);

### output
//Array
//(
//    [0] => 4
//    [1] => 20
//    [2] => 24
//    [3] => 25
//    [4] => 27
//    [5] => 29
//    [6] => 30
//    [7] => 44
//    [8] => 46
//    [9] => 114
//    [10] => 1000
//)
```

* `posix_getpwnam` 返回用户相关的信息

```php
$userinfo = posix_getpwnam("jayli");

print_r($userinfo);

### output
//Array
//(
//    [name] => jayli
//    [passwd] => x
//    [uid] => 1000
//    [gid] => 1000
//    [gecos] => ,,,
//    [dir] => /home/jayli
//    [shell] => /bin/bash
//)
```

* `posix_mkfifo` 创建有名管道文件

```php
$fun2 = function () {
  $file = "/tmp/abc";

  if (!file_exists($file)) {
      $res = posix_mkfifo($file, 0644);

      if (!$res) {
          exit("Create fifo file failed~\n");
      }
  }
  $i = 0;
  while ($i < 15) {
      $handle = fopen($file, 'w');

      fwrite($handle, "{$i}\n");

      $i++;
      sleep(1);
  }
};

$fun2();
```

* `posix_uname` 获取系统名

```php
$info = posix_uname();

print_r($info);

### output
//Array
//(
//    [sysname] => Linux
//    [nodename] => DESKTOP-OOHU3CT
//    [release] => 4.4.0-22000-Microsoft
//    [version] => #1-Microsoft Fri Jun 04 16:28:00 PST 2021
//    [machine] => x86_64
//    [domainname] => localdomain
//)
```