<?php


$pid = pcntl_fork();

if ($pid === -1) {

    exit("fork fail ~ \n");
}

$fun1 = function ($pid) {

    for ($i = 0; $i < 5; $i++) {
        printf("当前的父进程id = %d， 运行第%d次\n", getmypid(), $i);
        sleep(1);
    }

    $cid = pcntl_waitpid($pid, $status, WUNTRACED);

    if ($cid === -1) {
        if ($errCode = pcntl_get_last_error()) {
            var_dump("运行时发生错误~" . $errCode . "\n");
        }
    } elseif ($cid === 0) {
        var_dump("暂时没有可用的子进程~");
    } else {
        var_dump("子进程" . $pid . "正常退出！");
    }


};

$fun2 = function () {

    for ($i = 0; $i < 5; $i++) {
        sleep(2);

        printf("当前的子进程id = %d， 运行第%d次\n", posix_getpid(), $i);
    }
};

if ($pid > 0) {
    //处理父进程
    $fun1($pid);
} else {
    //处理子进程
    $fun2();
}

