### 关于PHP多进程编程十一

> 在进程中，给进程设置一个时钟信号，或者说是定时器。如果只使用`pcntl`扩展的情况下，需要使用到的函数

|函数|释义
|:---:|:---:|
`pcntl_signal`| 安装一个信号处理器
`pcntl_alarm`|创建一个计时器，在指定的秒数后向进程发送一个SIGALRM信号。每次对 pcntl_alarm()的调用都会取消之前设置的alarm信号。
`pcntl_signal_dispatch`|调用等待信号的处理器

* 注册一个`SIGALRM`信号闭包处理，调用`pcntl_alarm`发送信号,主线程`sleep(100)`休眠,`pcntl_signal_dispatch`调用等待信号的处理器

```php
pcntl_signal(SIGALRM, function ($signal) {
    echo "pcntl signal sigalrm ".time()."\n";
}, false);

pcntl_alarm(4);//四秒之后,会终止 sleep系统调用
echo "start " . time() . PHP_EOL;
pcntl_signal_dispatch();//调用等待信号的处理器
sleep(1000);

echo "end " . time() . PHP_EOL;

### output
//start 1649755654
//pcntl signal sigalrm 1649755658
//end 1649755658
```

* 使用 `declare(ticks = 1)`做定时器

> Tick（时钟周期）是一个在 declare 代码段中解释器每执行 N 条可计时的低级语句就会发生的事件。N 的值是在 declare 中的 directive 部分用 ticks=N 来指定的

```php
declare(ticks = 1);
pcntl_signal(SIGALRM, function ($signal) {
    echo "pcntl signal sigalrm ".time()."\n";
}, false);

echo "pcntl_alarm start ".time()."\n";
pcntl_alarm(10);//10s之后，SIGALRM收到通知，处理逻辑
echo "start " . time() . PHP_EOL;
sleep(1000);
echo "end " . time() . PHP_EOL;
```

* 使用 `pcntl_async_signals` 函数异步信号处理

```php
pcntl_async_signals(true);
pcntl_signal(SIGALRM, function ($signal) {
    echo "pcntl signal sigalrm ".time()."\n";
}, false);

echo "start " . time() . "\n";
pcntl_alarm(3);

sleep(100);
```
