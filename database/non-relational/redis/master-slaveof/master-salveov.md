### 关于redis主从相关资料

* 在同一台电脑上启动多个redis实例

1. 首先复制一份redis的配置文件，修改监听的端口号`port`
2. 修改本地的数据库文件名`dbfilename `
3. 修改日志文件名`logfile`
4. 启动redis服务`redis-server.exe --service-install redis.windows-service-6380.conf --service-name redis6380 --port 6380 `
5. `redis-server.exe --service-start --service-name redis6380`
6. 登录`redis-cli -p 6380`

* 把一台redis服务变为`从节点`,使用命令行,`6379`为主节点，`6380`为从节点，可以是一主一从，也可以是一主多从

```shell
slaveof 127.0.0.1 6379
```

* 通过修改从节点的配置文件使其本身变为从节点,在从节点的配置文件中添加

```shell
slaveof 127.0.0.1 6379
```

* 如果主节点`master`设置了密码，从节点`slave`可以使用`redis-cli`或者直接配置文件

```shell
# redis-cli、
config set masterauth 123456

#config file
masterauth 123456
```

* 从节点支持只读模式,`conf`，默认从节点是没有写入权限的，强制写入会：

```shell
127.0.0.1:6380> set mykey2 246810
(error) READONLY You can't write against a read only replica.
```

```shell
salve-read-only yes
```

#### 配置哨兵模式

[中文资料](http://redis.cn/topics/sentinel.html)

* 一台`master`节点，两台`slave`节点

* 配置三个哨兵，配置文件

```shell
port 16379
# 监视一个名为 mymaster 的主服务器， 这个主服务器的 IP 地址为 127.0.0.1 ， 端口号为 6379 ， 而将这个主服务器判断为失效至少需要 2 个 Sentinel 同意
sentinel monitor mymaster 127.0.0.1 6379 2  
#指定了 Sentinel 认为服务器已经断线所需的毫秒数
#如果服务器在给定的毫秒数之内， 没有返回 Sentinel 发送的 PING 命令的回复， 或者返回一个错误， 那么 Sentinel 将这个服务器标记为主观下线
sentinel down-after-milliseconds mymaster 5000 
#选项指定了在执行故障转移时， 最多可以有多少个从服务器同时对新的主服务器进行同步， 这个数字越小， 完成故障转移所需的时间就越长。
sentinel parallel-syncs mymaster 1 
#如果在该时间（ms）内未能完成failover操作，则认为该failover失败 
sentinel failover-timeout mymaster 15000 
```

* 首先启动三个`redis`节点服务，再启动`sentinel`服务

```shell
redis-server.exe sentinel-16379.conf
redis-server.exe sentinel-16380.conf
redis-server.exe sentinel-16381.conf
```

* 查看哪一个节点是`master`节点

```shell
redis-cli -p 16379

127.0.0.1:16380> info sentinel
# Sentinel
sentinel_masters:1
sentinel_tilt:0
sentinel_running_scripts:0
sentinel_scripts_queue_length:0
sentinel_simulate_failure_flags:0
master0:name=mymaster,status=ok,address=127.0.0.1:6380,slaves=2,sentinels=3
```

* Redis 使用异步复制，slave 和 master 之间异步地确认处理的数据量