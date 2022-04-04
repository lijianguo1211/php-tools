### 关于PHP多进程编程七

> 思考一个问题，在PHP的多进程中，通过函数`pcntl_fork()`出一个子进程，返回的子进程id，通过判断返回的pid是否等于0，代表是进入子进程，小于0代表fork
> 失败，大于0代表是父进程。多个进程之间数据是隔离的，就是各自进程之间的数据是不直接共享的，想访问其它进程的数据[进程之间通讯]，只能是通过别的方式
> （共享内存，管道等等）。但是子进程和父进程之间又是，fork出的子进程会继承父进程的数据，但是修改又是隔离的，比如下面这样：

```php
$arr = [];

$pid    = pcntl_fork();
// 子进程...
if ( 0 == $pid ) {
    $arr[] = time();
}
// 父进程
else if ( $pid > 0 ) {

    $arr[] = date('Y-m-d H:i:s');
}
else {
    throw new Exception( "Exception:pcntl_fork err" );
}

pcntl_wait($status);

print_r($arr);

### output
//Array
//(
//    [0] => 1648994822
//)
//Array
//(
//    [0] => 2022-04-03 14:07:02
//)

```

* 同时还有一个问题，fork出的子进程和父进程到底是谁先执行，谁后执行，这在PHP里面是不确定的吧[个人觉得没意义]。不过测试时是先父后子

```php
$pid = pcntl_fork();

if ($pid === -1) {
    exit("fork process fail~\n");
} elseif ($pid === 0) {
    echo "child process pid = " . posix_getpid() . PHP_EOL;
} else {
    echo "parent process pid = " . posix_getpid() . PHP_EOL;
}

### output
//parent process pid = 465
//child process pid = 466
```

* 再有子进程如果不退出，也就是不在子进程逻辑后执行`exit`,那么在之后的代码逻辑里，所有的代码都会执行“两次”

```php
$pid = pcntl_fork();

if ($pid === -1) {
    exit("fork process fail~\n");
} elseif ($pid === 0) {
    echo "child process pid = " . posix_getpid() . PHP_EOL;
} else {
    echo "parent process pid = " . posix_getpid() . PHP_EOL;
}

pcntl_wait($status);
for ($i = 0; $i < 3; $i++) {
    echo "$i . \t";
}

### output
//parent process pid = 513
//child process pid = 514
//0 .     1 .     2 .     0 .     1 .     2 .
```

* 如果在子进程逻辑最后执行 `exit`，主程序中，如果调用`pcntl_wait()`函数，等待子进程退出之后，再次执行之后的逻辑，子进程是已经退出了，就不会再向后走了

```php
$pid = pcntl_fork();

if ($pid === -1) {
    exit("fork process fail~\n");
} elseif ($pid === 0) {
    echo "child process pid = " . posix_getpid() . PHP_EOL;
    exit();
} else {
    echo "parent process pid = " . posix_getpid() . PHP_EOL;
}

pcntl_wait($status);
for ($i = 0; $i < 3; $i++) {
    echo "$i . \t";
}
### output
//parent process pid = 515
//child process pid = 516
//0 .     1 .     2 .
```

* 再接上一条，如果在一个for循环中，操作程序的人员本意中，是fork出多个子进程，然后分别去执行逻辑，但是他写为这样的,本意是想要三个子进程去处理逻辑，
结果却是fork出7个子进程

```php
for ($i = 0; $i < 3; $i++) {
    $pid = pcntl_fork();

    if ($pid === 0) {
        echo "child process pid = " . posix_getpid() . PHP_EOL;
    }
}

while (1) {
    $pid = pcntl_wait($status);

    if ($pid > 0) {
        echo "exit child process pid = $pid\n";
    }

    sleep(2);
}

### output
//child process pid = 537
//child process pid = 538
//child process pid = 540
//child process pid = 539
//child process pid = 541
//child process pid = 543
//child process pid = 542

### 看进程树 pstree, 可以看到最终fork出了多少进程，明明想要的是三个，结果却是在主进程下，fork了三个，然后又分别在前两个子进程上继续fork出了子进程
//init─┬─init───bash───php─┬─php─┬─php───php
//     │                   │     └─php
//     │                   ├─php───php
//     │                   └─php
//     ├─init───bash───pstree
//     └─2*[{init}]
```

* 继续上一条，如果在fork出的子进程的逻辑最后加上`exit`，最后的结果又是什么样的？

```php
for ($i = 0; $i < 3; $i++) {
    $pid = pcntl_fork();

    if ($pid === 0) {
        echo "child process pid = " . posix_getpid() . PHP_EOL;
        
        exit();
    }
}

while (1) {
    $pid = pcntl_wait($status);

    if ($pid > 0) {
        echo "exit child process pid = $pid\n";
    }

    sleep(2);
}

### output 这才是最终想要的结果
//child process pid = 546
//child process pid = 547
//exit child process pid = 546
//child process pid = 548
//exit child process pid = 547
//exit child process pid = 548
```

* 如果还想不明白上面的问题，也许有一个小实验可以看出更多的东西，在fork子进程之前，先创建一个redis或者是MySQL的连接，然后子进程和父进程都不退出，保持
着运行，然后去查看系统中正在运行的几个当前的进程和几个redis或者MySQL的连接,可以发现在运行的有四个PHP进程，而MySQL的连接只有一个，这个MySQL的连接
是在父进程中创建的，也就是一个长连接的，和父进程的生命周期是绑定的。也就是在这四个进程中都是使用这一个的连接

```php
$pdo = new \PDO("mysql:dbname=test_blog;host=127.0.0.1;port=3306", 'root', 'root');

for ($i = 0; $i < 3; $i++) {
    $pid = pcntl_fork();

    if ($pid === 0) {
        echo "child process pid = " . posix_getpid() . PHP_EOL;

        while (1) {
            sleep(5);
        }
    }
}

while (true) {
    sleep(10);
}
### output
### ps -ef | grep php
//jayli      551     9  0 23:10 tty1     00:00:00 php pcntl.php
//jayli      552   551  0 23:10 tty1     00:00:00 php pcntl.php
//jayli      553   551  0 23:10 tty1     00:00:00 php pcntl.php
//jayli      554   551  0 23:10 tty1     00:00:00 php pcntl.php
```

* 同时又引出了另外一个问题，如果在三个子进程中分别添加一条数据，然后返回添加数据的主键id，那么这个返回的主键id是当前这个子进程添加数据的吗？可以看到
最后的测试结果是，返回的id和插入的数据id不对应了，也就是说子进程之间抢占式的调用，导致进程A在插入数据后，返回插入id时，进程B又去插入数据,A,B共用的
一个MySQL连接，导致返回的数据错乱了，是程序的错误吗？显然不是，是写程序这个小崽的问题了，虽然把连接放在了外面，是一种长连接，但是多个进程共用这一个连
接的时候，就会出现问题

```php
$pdo = new \PDO("mysql:dbname=test_blog;host=127.0.0.1;port=3306", 'root', 'root');

for ($i = 0; $i < 3; $i++) {
    $pid = pcntl_fork();

    if ($pid === 0) {
        $id = posix_getpid();
        $date = date('Y-m-d H:i:s');
        $sql = sprintf("insert into test1_name (id, updated_at) values (%d, '%s')", $id, $date);
        $pdo->exec($sql);
        $lastId = $pdo->lastInsertId();
        echo "child process pid = " . $id . ' date: ' . $date . ' lastId = ' . $lastId . PHP_EOL;
    }
}

while (true) {
    sleep(10);
}

### output 
//child process pid = 582 date: 2022-04-03 15:26:00 lastId = 10
//child process pid = 583 date: 2022-04-03 15:26:00 lastId = 11
//child process pid = 585 date: 2022-04-03 15:26:00 lastId = 12
//child process pid = 586 date: 2022-04-03 15:26:00 lastId = 13

//582	2022-04-03 15:26:00	10
//583	2022-04-03 15:26:00	11
//585	2022-04-03 15:26:00	12
//584	2022-04-03 15:26:00	13
//586	2022-04-03 15:26:00	14
//587	2022-04-03 15:26:00	15
```

* 同时，还有一个改进，如果我把连接放在子进程中呢？

```php
for ($i = 0; $i < 3; $i++) {
    $pid = pcntl_fork();

    if ($pid === 0) {
        $pdo = new \PDO("mysql:dbname=test_blog;host=127.0.0.1;port=3306", 'root', 'root');
        $id = posix_getpid();
        $date = date('Y-m-d H:i:s');
        $sql = sprintf("insert into test1_name (id, updated_at) values (%d, '%s')", $id, $date);
        $pdo->exec($sql);
        $lastId = $pdo->lastInsertId();
        echo "child process pid = " . $id . ' date: ' . $date . ' lastId = ' . $lastId . PHP_EOL;
    }
}

while (true) {
    sleep(10);
}
### output
//child process pid = 597 date: 2022-04-03 15:34:57 lastId = 23
//child process pid = 598 date: 2022-04-03 15:34:57 lastId = 24
//child process pid = 600 date: 2022-04-03 15:34:57 lastId = 25
//child process pid = 601 date: 2022-04-03 15:34:57 lastId = 26
//child process pid = 599 date: 2022-04-03 15:34:57 lastId = 27
//child process pid = 602 date: 2022-04-03 15:34:57 lastId = 28
//child process pid = 603 date: 2022-04-03 15:34:57 lastId = 29

//597	2022-04-03 15:34:57	23
//598	2022-04-03 15:34:57	24
//600	2022-04-03 15:34:57	25
//601	2022-04-03 15:34:57	26
//599	2022-04-03 15:34:57	27
//602	2022-04-03 15:34:57	28
//603	2022-04-03 15:34:57	29
```

所以在多进程编程中，遇到这种MySQL，Redis等操作连接的时候，就需要严加注意这种问题，如果把连接放在主进程，就会导致数据错乱当然你插入的数据时没有问题，
但是在查询`last id`的时候，就需要注意了，如果把连接放入各自的子进程，就基本不会又这种问题发生了。[不会导致不同的子进程共享一个连接]