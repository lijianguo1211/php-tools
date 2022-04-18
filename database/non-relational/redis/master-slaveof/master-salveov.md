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