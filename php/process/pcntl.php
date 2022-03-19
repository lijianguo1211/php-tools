<?php
/**
 * @Notes:
 *
 * @File Name: pcntl.php
 * @Date: 2022/3/17
 * @Created By: Jay.Li
 */

class EventLoop
{
    protected \EventBase $eventBase;

    protected int $key = \Event::PERSIST | \Event::READ;

    public array $allEvent = [];

    public function __construct()
    {
        $this->eventBase = new EventBase();
    }

    public function add($fd, $cb)
    {
        $event = new \Event($this->eventBase, $fd, $this->key, $cb);

        $res = $event->add();

        $fdKey = (int)$fd;

        if ($res) {
            $this->allEvent[$this->key][$fdKey] = $event;
        }

        return $res;
    }

    public function del($fd)
    {
        $fdKey = (int)$fd;

        if (!isset($this->allEvent[$this->key][$fdKey])) {
            goto end;
        }

        $this->allEvent[$this->key][$fdKey]->del();

        unset($this->allEvent[$this->key][$fdKey]);

        end:
    }

    public function loop()
    {
        $this->eventBase->loop();
    }


}

class Masters
{
    /**
     * 全局事件
     * @var static EventLoop|null
     */
    public static ?EventLoop $globalEvent = null;

    /**
     * 关闭操作
     * @var callable
     */
    public $onClose = null;

    /**
     * 消息发送
     * @var callable
     */
    public $onMessage = null;

    /**
     * 连接错误
     * @var  callable
     */
    public $onError = null;

    /**
     * 创建socket上下文参数
     * @var  array|int[][]
     */
    protected array $options = [
        'socket' => [
            'backlog' => 1024,
            'so_reuseport' => 0
        ]
    ];

    /**
     * 监听的所有连接
     * @var  array
     */
    public array $connections = [];

    /**
     * socket 上下文
     * @var resource
     */
    protected $context;

    /**
     * 创建监听的服务端资源
     * @var resource
     */
    protected $mainSocket;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->context = stream_context_create($this->options);

        $this->init();

        $this->fork();

        $this->wait();
    }

    /**
     * @Notes: 初始化服务端资源
     *
     * @User: Jay.Li
     * @Methods: init
     * @Date: 2022/3/19
     * @throws Exception
     */
    protected function init()
    {
        if (!$this->mainSocket) {
            $errno = 0;
            $errMsg = '';
            $flags = \STREAM_SERVER_BIND | \STREAM_SERVER_LISTEN;

            $this->mainSocket = \stream_socket_server("tcp://127.0.0.1:9999", $errno, $errMsg, $flags, $this->context);


            if (!is_resource($this->mainSocket)) {
                throw new \Exception($errMsg);
            }

            if (\function_exists('socket_import_stream')) {
                \set_error_handler(function(){});
                $socket = \socket_import_stream($this->mainSocket);
                \socket_set_option($socket, \SOL_SOCKET, \SO_KEEPALIVE, 1);
                \socket_set_option($socket, \SOL_TCP, \TCP_NODELAY, 1);
                \restore_error_handler();
            }

            \stream_set_blocking($this->mainSocket, false);
        }

        $this->addSocketAccept();
    }

    /**
     * @Notes: 创建子进程 worker
     *
     * @User: Jay.Li
     * @Methods: fork
     * @Date: 2022/3/19
     */
    protected function fork()
    {
        $pid = pcntl_fork();

        switch ($pid) {
            case -1:
                exit("created process child failed~\n");
            case 0:
                \srand();
                \mt_srand();

                if (!static::$globalEvent) {
                    static::$globalEvent = new EventLoop();

                    echo "child create Event Loop~\n";
                    $this->addSocketAccept();
                }

                $onWorkerStart = function () {
                    echo "worker start loop~\n";
                };

                $onWorkerStart();
                //child process 开启事件调度
                static::$globalEvent->loop();
                exit(0);
            default:
                echo "this is mater process, listen child worker process~\n";
        }
    }

    /**
     * @Notes: 主进程等待监控
     *
     * @User: Jay.Li
     * @Methods: wait
     * @Date: 2022/3/19
     */
    protected function wait()
    {
        //阻塞master process
        while (1) {
            \pcntl_signal_dispatch();
            //等待worker process exit
            $pid = pcntl_wait($status, WUNTRACED);
            \pcntl_signal_dispatch();
            //如果得子进程id，代表子进程退出了，
            if ($pid) {

                //睡眠500微秒，之后重新启动一个子进程
                usleep(500);

                $this->fork();
            } else {
                //10微妙休眠一下，master进程
                usleep(10);
            }
        }
    }

    /**
     * @Notes: 服务端的套接字添加到事件库
     *
     * @User: Jay.Li
     * @Methods: addSocketAccept
     * @Date: 2022/3/19
     */
    protected function addSocketAccept()
    {
        if (static::$globalEvent && $this->mainSocket) {
            static::$globalEvent->add($this->mainSocket, [$this, 'acceptConnection']);
        }
    }

    /**
     * @Notes: 接受连接
     *
     * @User: Jay.Li
     * @Methods: acceptConnection
     * @Date: 2022/3/19
     */
    public function acceptConnection()
    {
        \set_error_handler(function(){});
        $newSocket = \stream_socket_accept($this->mainSocket, 0, $remoteAddress);
        \restore_error_handler();

        if (!$newSocket) {
            return;
        }

        $onConnect = function ($remoteAddress) {
            echo "onConnect client remote address is :" . $remoteAddress . PHP_EOL;
        };

        $onConnect($remoteAddress);

        \stream_set_blocking($newSocket, 0);
        // Compatible with hhvm
        if (\function_exists('stream_set_read_buffer')) {
            \stream_set_read_buffer($newSocket, 0);
        }
        $conn = new TcpCoon($newSocket, $remoteAddress);

        $this->connections[$conn->id] = $conn;

        echo "total Tcp Conn: " . $conn->id . PHP_EOL;

        $conn->onMessage = $this->onMessage;

        $conn->onClose = $this->onClose;

        $conn->onError = $this->onError;
    }
}

class TcpCoon
{
    /**
     * @var callable
     */
    public $onClose = null;

    /**
     * @var callable
     */
    public $onMessage = null;

    /**
     * @var  callable
     */
    public $onError = null;

    public string $remoteAddress;

    public int $id;

    /**
     * @var resource
     */
    public $_socket;

    public static array $globalData = [
        'total' => 0
    ];

    public function __construct($socket, $address)
    {
        $this->id = ++static::$globalData['total'];
        \stream_set_blocking($socket, 0);
        if (\function_exists('stream_set_read_buffer')) {
            \stream_set_read_buffer($socket, 0);
        }
        Masters::$globalEvent->add($socket, [$this, 'baseRead']);

        $this->remoteAddress = $address;

        $this->_socket = $socket;
    }

    /**
     * @Notes: 关闭连接
     *
     * @User: Jay.Li
     * @Methods: close
     * @Date: 2022/3/19
     */
    public function close()
    {
        if ($this->onClose) {
            call_user_func($this->onClose, $this);
        } else {
            $onClose = function () {
                echo "close " . $this->remoteAddress . "~\n";
            };

            $onClose();
        }

        Masters::$globalEvent->del($this->_socket);
        fclose($this->_socket);
        $this->_socket = null;
        static::$globalData['total']--;
    }

    /**
     * @Notes: 读取消息并发送消息
     *
     * @User: Jay.Li
     * @Methods: baseRead
     * @Date: 2022/3/19
     * @param $socket
     */
    public function baseRead($socket)
    {
        $buffer = '';
        try {
            $buffer = @\fread($socket, 65535);

        } catch (\Exception | \Error $e) {
            if ($this->onError) {
                call_user_func($this->onError, $this, $e);
            } else {
                $onError = function ($e) {
                    $msg = $e->getMessage();
                    echo "send client " . $this->remoteAddress . " error $msg: ~\n";
                };

                $onError($e);
            }

            $this->close();
        }

        if (empty($buffer)) {

            $this->close();

            return;
        }

        if ($this->onMessage) {
            call_user_func($this->onMessage, $this, $buffer);
        } else {
            $onMessage = function ($buffer, $socket) {
                $responseHeader = [
                    "HTTP/1.1 200 OK\r\n",
                    "Server: nginx-jay\r\n",
                    "Connection: keep-alive\r\n",
                    "Content-Type: text/html;charset=utf-8\r\n"
                ];

                $len = strlen($buffer);

                $body = implode('', $responseHeader);

                $body .= "Content-Length: $len\r\n\r\n";

                $body .= $buffer;
                $len = fwrite($socket, $body);

                if ($len !== strlen($body)) {

                    if ($this->onError) {
                         call_user_func($this->onError, $this);
                    } else {
                        $onError = function () {
                            echo "send client " . $this->remoteAddress . " error~\n";
                        };

                        $onError();
                    }

                }
            };

            $onMessage($buffer, $socket);
        }
    }
}


new Masters();
