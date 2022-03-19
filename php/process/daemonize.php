<?php
/**
 * @Notes:
 *
 * @File Name: daemonize.php
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
//            echo "child process id = " . posix_getpid() . PHP_EOL;

            while (true) {
                sleep(5);
            }
            break;
        default:
            $childs[$pid] = $pid;

            break;
    }
}

function daemonize()
{
    $pid = pcntl_fork();

    switch ($pid) {
        case -1:
            exit("fork fail ~ \n");
            break;
        case 0:
            // Make the current process a session leader
            if (($sid = posix_setsid()) <= 0) {
                die("set sid = $sid failed \n");
            }

            if (chdir("/") === false) {
                die("change dir failed ~\n");
            }

            umask(0);

            fclose(STDIN);
            fclose(STDOUT);
            fclose(STDERR);
            break;
        default:
            file_put_contents("./d.pid", $pid);
            exit();
            break;
    }
}

daemonize();

for ($i = 0; $i < 3; $i++) {
    fork();
}

while (count($childs)) {
    $exitId = pcntl_wait($status);

    if ($exitId > 0) {
//        echo "child process $exitId exit \n";
//
//        echo "中断子进程的信号值是：" . pcntl_wtermsig($status) . PHP_EOL;

        unset($childs[$exitId]);
    }

    if (count($childs) < 1) {
        fork();
    }
}

//echo "master process pid = " . getmypid() . " exit \n";