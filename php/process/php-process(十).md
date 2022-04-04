### 关于PHP多进程编程十

* 一个简单的使用`event`扩展编写的http服务

```php
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
```

* 封装升级版本

```php
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
```

* 有一个问题还需要再次突出一下

> 在多进程编程的时候，父进程声明一个变量，不管是类中的静态属性还是变量，在子进程中访问的时候，是继承父进程的信息，但是在多个子进程中分别操作这些变量的
> 时候，它们是互相隔离的，子进程A和子进程B操作同一个变量，是隔离的，互相不影响的。而且父进程是访问不到的

```php
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

### output
//global M::arr empty~
//global M::arr empty~
//global M::arr empty~
//global M::arr empty~
//global M::arr empty~
//child process exit~ pid = 267
//Array
//(
//)
//child process exit~ pid = 270
//Array
//(
//)
//child process exit~ pid = 268
//Array
//(
//)
//child process exit~ pid = 269
//Array
//(
//)
//child process exit~ pid = 271
//Array
//(
//)
```