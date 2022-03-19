### 关于PHP中的多进程编程(四)

> 之前的一二三记录都是PHP中多进程编程需要使用的浅显知识，本篇算是把多进程编程的题外话，网络编程，之后通过了解网络编程之后，再把多进程编程和网络编程
> 做融合

##### 网络知识

* php需要开启`socket`扩展，在编译时` --enable-sockets        Enable sockets support`

* 网络的七层模型

|序号|title|释义|代表
|:---:|:---:|:---:|:---:|
7|应用层|网络服务与最终用户的一个接口|HTTP FTP TFTP SMTP SNMP DNS TELNET HTTPS POP3 DHCP
6|表示层|数据的表示、安全、压缩|JPEG、ASCll、EBCDIC、加密格式等
5|会话层|建立、管理、终止会话|对应主机进程，指本地主机与远程主机正在进行的会话
4|传输层|定义传输数据的协议端口号，以及流控和差错校验|TCP UDP，数据包一旦离开网卡即进入网络传输层
3|网络层|进行逻辑地址寻址，实现不同网络之间的路径选择。|ICMP IGMP IP（IPV4 IPV6）
2|数据链路层|建立逻辑连接、进行硬件地址寻址、差错校验|将比特组合成字节进而组合成帧，用MAC地址访问介质，错误发现但不能纠正。
1|物理层|建立、维护、断开物理连接|--

* php中使用自带的`select`事件机制编写一个简易demo

|函数|释义
|:---:|:---:|
`stream_socket_server` | 创建一个服务端的套接字
`stream_set_blocking` | 为资源流设置阻塞或非阻塞
`stream_context_create` | 创建资源流的上下文
`stream_socket_accept` | 接受由 `stream_socket_server()` 创建的套接字连接
`stream_socket_get_name` | 获取本地或者远程的套接字名称
`stream_context_set_option` |  对资源流、数据包或者上下文设置参数
`socket_import_stream`|将封装套接字的流导入套接字扩展资源。
`socket_set_option`|设置套接字的套接字选项
`stream_set_read_buffer` |在给定流上设置读取文件缓冲
`stream_set_write_buffer`|在给定流上设置写入文件缓冲
`stream_select`|在给定的流数组上运行相当于 select() 系统调用

**********************

```php
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
```