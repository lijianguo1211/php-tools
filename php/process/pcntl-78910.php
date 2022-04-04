<?php
/**
 * @Notes:
 *
 * @File Name: pcntl.php
 * @Date: 2022/4/3
 * @Created By: Jay.Li
 */

function test1()
{
    for ($i = 0; $i < 20; $i ++) {
        $pid = pcntl_fork();

        if ($pid === 0) {
            file_put_contents('./test1.log', $i . PHP_EOL, FILE_APPEND);

            while (1) {
                sleep(2);
            }
        }
    }

    while (1) {
        sleep(5);
    }
}

//test1();

function test2()
{
    for ($i = 0; $i < 20; $i ++) {
        $pid = pcntl_fork();

        if ($pid === 0) {
            $handle = fopen('./test2.log', 'a+');

            fwrite($handle, $i . PHP_EOL);

            fclose($handle);

            while (1) {
                sleep(2);
            }
        }
    }

    while (1) {
        sleep(5);
    }
}

//test2();

function test3()
{
    $handle = fopen('./test3.log', 'a+');
    for ($i = 0; $i < 20; $i ++) {
        $pid = pcntl_fork();

        if ($pid === 0) {


            fwrite($handle, $i . PHP_EOL);

            while (1) {
                sleep(2);
            }
        }
    }

    while (1) {
        sleep(5);
    }
}

//test3();


function test4()
{
    $arr = [1, 2, 3, 4];

    for ($i = 5; $i <= 7; $i++) {
        $pid = pcntl_fork();
        if ($pid === 0) {
            sleep(1);
//            $arr[] = mt_rand(5, 10);
            print_r($arr);

//            while (1) {
//                sleep(2);
//            }
            exit();
        } elseif ($pid >= 0) {
            $arr[] = $num =  mt_rand(10, 15);

            print_r($num);

//            sleep(2);

//            print_r($arr);
        } else {
            exit("fork process fails~\n");
        }
    }

//    $pid = pcntl_wait($status);

//    print_r($arr);

    while (1) {
       sleep(10);
    }
}

//test4();

/**
 * @throws Exception
 */
function test5()
{
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
}

//test5();

function test6()
{
    $pid = pcntl_fork();

    if ($pid === -1) {
        exit("fork process fail~\n");
    } elseif ($pid === 0) {
        echo "child process pid = " . posix_getpid() . PHP_EOL;
    } else {
        echo "parent process pid = " . posix_getpid() . PHP_EOL;
    }
}

//test6();

function test7()
{
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
}

//test7();

function test8()
{
    pcntl_signal(SIGUSR1, function () {
        echo "child exit~\n";
    }, false);
    $pid = pcntl_fork();

    if ($pid === -1) {
        exit("fork process fail~\n");
    } elseif ($pid === 0) {
        echo "child process pid = " . posix_getpid() . PHP_EOL;

        posix_kill(posix_getpid(), SIGUSR1);
        exit();
    } else {
        echo "parent process pid = " . posix_getpid() . PHP_EOL;
    }





    pcntl_wait($status);
    for ($i = 0; $i < 3; $i++) {
        echo "$i . \t";
    }
    echo "\n";
}

//test8();

function test9()
{

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
}

//test9();


function test10()
{

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
}

//test10();

function test11()
{
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
}

//test11();

function test12()
{
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
}

//test12();

function test13()
{
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
}

//test13();

function test14()
{
    $pid = pcntl_fork();

    if ($pid === 0) {
        echo "child process pid = " . posix_getpid() . "\n";
        while (1) {
            sleep(5);
        }
    }

    throw new \Exception("主动异常结束父进程~\n");
}

//test14();

function test15()
{
    $pid = pcntl_fork();

    if ($pid === 0) {
        echo "child process pid = " . posix_getpid() . "\n";
        exit();
    }

    echo "main process pid = " . posix_getpid() . "\n";

    while (1) {
        sleep(5);
    }
}

//test15();

function test16()
{
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
}
//test16();

function test17()
{
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
//    $res = pcntl_signal_dispatch();
//
//    echo "pcntl_signal_dispatch return = " . $res . PHP_EOL;

    while (1) {


        sleep(3);
    }
}

//test17();

function test18()
{
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
}

//test18();

function test19()
{
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
}

//test19();


function test20()
{
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
}

//test20();

function test21()
{
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
}

//test21();

function test22()
{
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
}

//test22();

function test23()
{
    $config = new EventConfig();

    $base = new EventBase($config);

    $event = new Event($base, -1, \Event::TIMEOUT, function () {
        echo "timer 1 " . microtime(true);
    });

    $event->add(1.5);

    $event->delTimer();

    $event->setTimer($base, function ($n) {
        echo "timer 2 " . microtime(true);
    }, 1);
    $event->add(2);

    $base->loop();
}

//test23();

function test24()
{
    $config = new EventConfig();

    $base = new EventBase($config);

    $pid = pcntl_fork();

    if ($pid === 0) {
        echo "child process pid = " . posix_getpid() . "\n";
        sleep(5);

        exit();
    }

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
}

//test24();


function test25()
{
    $context = [
        'socket' => [
            'backlog' => 102400,
            'so_reuseport' => 1
        ]
    ];

    $allEvent = [];

    $connection = [];

    $content = stream_context_create($context);
    $mainSocket = stream_socket_server('tcp://127.0.0.1:9999', $errCode, $errMsg, STREAM_SERVER_BIND|STREAM_SERVER_LISTEN, $content);

    $base = new EventBase();

    $event = new Event($base, $mainSocket, Event::READ|Event::PERSIST, function ($socket, $flag, $baseEvent) {

        global $allEvent;
        global $connection;

        $clientSocket = stream_socket_accept($socket, 0, $remoteAddress);

        $connection[(int)$clientSocket] = $clientSocket;

        $event = new Event($baseEvent, $clientSocket, Event::READ|Event::PERSIST, function ($socket) {
            $buffer = fread($socket, 65535);

            if ($buffer == '') {
                return;
            }

            echo $buffer;

            global $allEvent;

            $con = json_encode($allEvent);

            $len = strlen($con);

            if (is_resource($socket)) {
                fwrite($socket, "HTTP/1.1 200 OK\r\nAccept: application/json, text/plain, */*\r\nConnection: keep-alive\r\nContent-Length:$len\r\n\r\n$con");
            }
        });

        $event->add();

        $allEvent[(int)$clientSocket] = $event;


    }, $base);

    $event->add();

    $allEvent[(int)$mainSocket] = $event;

    $base->loop();
}

//test25();


function test26()
{
    $context = [
        'socket' => [
            'backlog' => 102400,
            'so_reuseport' => 1
        ]
    ];

    $allEvent = [];

    $connection = [];

    $content = stream_context_create($context);
    $mainSocket = stream_socket_server('tcp://127.0.0.1:9999', $errCode, $errMsg, STREAM_SERVER_BIND|STREAM_SERVER_LISTEN, $content);

    $base = new EventBase();

    function socketAccept($socket, $flag, $baseEvent) {
        global $allEvent;
        global $connection;

        $clientSocket = stream_socket_accept($socket, 0, $remoteAddress);

        $connection[(int)$clientSocket] = $clientSocket;

        $event = new Event($baseEvent, $clientSocket, Event::READ|Event::PERSIST, 'socketRead', $baseEvent);

        $allEvent[(int)$clientSocket]['read'] = $event;

        $event->add();
    }

    function socketRead($socket, $flag, $baseEvent)
    {
        global $allEvent;

        $content = fread($socket, 652400);

        $event = new Event($baseEvent, $socket, Event::WRITE|Event::PERSIST, 'socketWrite', [
            'content' => $content
        ]);

        $allEvent[(int)$socket]['write'] = $event;

        $event->add();
    }

    function socketWrite($socket, $flag, $arg)
    {
        global $allEvent;
        global $connection;
        $content = $arg['content'];
        $len = strlen($content);

        if ($content === '') {
            return;
        }

        foreach ($connection as $fd => $sock) {
            if ($fd !== (int)$socket) {
                fwrite($socket, "HTTP/1.1 200 OK\r\nAccept: application/json, text/plain, */*\r\nConnection: keep-alive\r\nContent-Length:$len\r\n\r\n$content");
            }
        }

        $event = $allEvent[ intval($socket) ]['write'];
        $event->del();
        unset( $allEvent[intval( $socket )]['write'] );
    }

    $event = new Event($base, $mainSocket, Event::READ|Event::PERSIST,'socketAccept', $base);

    $event->add();

    $allEvent[(int)$mainSocket]['read'] = $event;

    $base->loop();
}


//test26();


function test27()
{
    class M
    {
        public static array $arr = [];
    }

    function isArr()
    {
        if (empty(M::$arr)) {
            echo "global M::arr empty~\n";
            M::$arr[] = posix_getpid();
        }
    }

    for ($i = 0; $i < 5; $i++) {
        $pid = pcntl_fork();

        if ($pid === 0) {
            isArr();
            sleep(mt_rand(1, 10));
            exit();
        }
    }

    while (1) {
        pcntl_signal_dispatch();

        $pid = pcntl_wait($status);

        pcntl_signal_dispatch();

        if ($pid > 0) {
            echo "child process exit~ pid = $pid \n";
        }

        sleep(3);

        print_r(M::$arr);
    }
}

//test27();


function test28()
{
    $context = [
        'socket' => [
            'backlog' => 102400,
            'so_reuseport' => 1
        ]
    ];

    $allEvent = [];

    $connection = [];

    $content = stream_context_create($context);
    $mainSocket = stream_socket_server('tcp://127.0.0.1:9999', $errCode, $errMsg, STREAM_SERVER_BIND|STREAM_SERVER_LISTEN, $content);

    $base = new EventBase();

    function socketAccept($socket, $flag, $baseEvent) {
        global $allEvent;
        global $connection;

        $clientSocket = stream_socket_accept($socket, 0, $remoteAddress);

        echo "client remote address: " . $remoteAddress . "\n";

        $connection[(int)$clientSocket] = $clientSocket;

        $event = new EventBufferEvent($baseEvent, $socket, 0);

        $allEvent[(int)$clientSocket]['read'] = $event;

        $event->setCallbacks('socketRead', null, null, $socket);

        $event->setTimeouts(30, 30);

        $event->setWatermark(Event::READ, 2, 1024);

        $event->setPriority(10);

        $event->enable(Event::READ);
    }

    function socketRead(EventBufferEvent $event, $socket)
    {
        global $allEvent;

        $buff = $event->read(65535);

        echo "client buffer ." . $buff;

//        $event->readBuffer($socket);
    }

    $event = new Event($base, $mainSocket, Event::READ|Event::PERSIST,'socketAccept', $base);

    $event->add();

    $allEvent[(int)$mainSocket]['read'] = $event;

    $base->loop();
}

test28();