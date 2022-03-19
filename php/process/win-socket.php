<?php
/**
 * @Notes:
 *
 * @File Name: win-socket.php
 * @Date: 2022/3/13
 * @Created By: Jay.Li
 */

class Worker
{
    /**
     * 连接事件回调
     * @var  callable
     */
    public $onConnect = null;

    /**
     * 发送消息事件回调
     * @var  callable
     */
    public $onMessage = null;

    /**
     * 关闭事件回调
     * @var  callable
     */
    public $onClose = null;

    /**
     * 创建服务端的套接字
     * @var resource
     */
    protected $socket = null;

    /**
     * 所有的套接字连接
     * @var  array
     */
    protected array $allSockets = [];

    public string $errMsg;

    public int $errNo;

    /**
     * 所有的连接
     * @var  array
     */
    public array $connections = [];

    public function __construct($address)
    {
        /**
         * 创建服务端的套接字
         */
        $this->socket = stream_socket_server($address, $errNo, $errMsg);

        $this->errNo = $errNo;

        $this->errMsg = $errMsg;

        /**
         * 设置非阻塞
         */
        stream_set_blocking($this->socket, 0);

        /**
         * 把服务端创建的套接字添加到全局数组
         */
        $this->allSockets[(int)$this->socket] = $this->socket;
    }

    public function run()
    {
        while (true) {
            $write = $except = null;

            $read = $this->allSockets;

            /**
             * select 调度
             */
            stream_select($read, $write, $write, 60);

            /**
             * 处理所有的读事件
             */
            foreach ($read as $key => $socket) {
                /**
                 * 如果当前的socket 和全局的socket 相等，代表监听的socket可读，有新二点连接
                 */
                if ($socket == $this->socket) {

                    /**
                     * 接收stream_socket_server创建的socket，接受新的客户连接
                     */
                    $newSocket = stream_socket_accept($this->socket);

                    if (!$newSocket) {
                        continue;
                    }

                    $conn = new TcpConnections($newSocket);

                    $this->connections[(int)$newSocket] = $conn;

                    /**
                     * 添加到全局的socket中
                     */
                    $this->allSockets[(int)$newSocket] = $newSocket;

                    /**
                     * 如果设置了onConnect回调，使用它
                     */
                    if ($this->onConnect) {
                        call_user_func($this->onConnect, $conn, $this->allSockets);
                    }
                } else { //客户端可读，有数据发送过来
                    //读数据
                    $buffer = fread($socket, 65535);

                    /**
                     * 读取客户端传递的数据
                     */
                    if ($buffer === '' || $buffer === false) {
                        if ($this->onClose) {
                            /**
                             * 没有数据，直接回调onClose事件
                             */
                            call_user_func($this->onClose, $this->connections[(int)$socket]);
                        }

                        /**
                         * 关闭当前的连接
                         */
                        fclose($socket);

                        unset($this->allSockets[(int)$socket], $this->connections[(int)$socket]);

                        continue;
                    }

                    /**
                     * 梳理onMessage事件
                     */
                    call_user_func($this->onMessage, $this->connections[(int)$socket], $buffer);
                }
            }
        }
    }
}

$obj = new Worker("tcp://127.0.0.1:9999");

$obj->onConnect = function ($conn, $all) {
    echo "Connection~\n";
};

$obj->onClose = function (TcpConnections $tcp) {

    $msg = "HTTP/1.1 200 OK \r\n Bad Request\r\n\r\n";

    fwrite($tcp->_socket, $msg);
};
$obj->onMessage = function (TcpConnections $tcp, $data) {

    [$header, $body] = explode("\r\n\r\n", $data, 2);

    $headerArray = explode("\r\n", $header);

    $msg = "hello world~";
    $len= strlen($msg);

    $m = "HTTP/1.1 200 OK\r\nConnection: keep-alive\r\nServer: jayServer\1.1.0\r\nContent-length:$len\r\n\r\n$msg";
    fwrite($tcp->_socket, $m);
};

$obj->run();

class TcpConnections
{
    /**
     * @var  resource
     */
    public $_socket = null;

    public function __construct($socket)
    {
        $this->_socket = $socket;
    }

    public function send($buffer): bool|int
    {
        var_dump($this->_socket);
        if (!feof($this->_socket)) {
            return false;
        }

        return fwrite($this->_socket, $buffer);
    }


}