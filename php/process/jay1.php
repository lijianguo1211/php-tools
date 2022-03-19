<?php
/**
 * @Notes:
 *
 * @File Name: jay1.php
 * @Date: 2022/3/10
 * @Created By: Jay.Li
 */

$childs = [];

function fork()
{
    global $childs;
    $pid = pcntl_fork();

    switch ($pid) {
        case -1:
            exit("fork fail ~ \n");
            break;
        case 0:
            echo "child process id = " . posix_getpid() . PHP_EOL;

            while (true) {
                sleep(5);
            }
            break;
        default:
            $childs[$pid] = $pid;

            break;
    }
}

for ($i = 0; $i < 3; $i++) {
    fork();
}


while (count($childs)) {
    $exitId = pcntl_wait($status);

    if ($exitId > 0) {
        echo "child process $exitId exit \n";

        echo "中断子进程的信号值是：" . pcntl_wtermsig($status) . PHP_EOL;

        unset($childs[$exitId]);
    }

    if (count($childs) < 1) {
        fork();
    }
}

echo "master process pid = " . getmypid() . " exit \n";