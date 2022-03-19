<?php
/**
 * @Notes:
 *
 * @File Name: signal.php
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

//            pcntl_signal(SIGTERM, SIG_IGN, false);

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

$command = count($argv) < 2 ?? "";

[$file, $command] = $argv;

switch ($command) {
    case 'start':
        if (file_exists("/tmp/jay_master_pid.pid")) {
            die("already running \n");
        }
        break;
    case "stop":
        if (!file_exists("/tmp/jay_master_pid.pid")) {
            exit("place enter running start~\n");
        }

        $masterPid = file_get_contents("/tmp/jay_master_pid.pid");

        posix_kill($masterPid, SIGKILL);

        exec("ps --ppid {$masterPid} | awk '/[0-9]/{print $1}' | xargs", $output, $status);
        $pidArr = explode(" ", current($output));

        foreach ($pidArr as $child) {
            posix_kill((int)$child, SIGKILL);
        }

        while (true) {
            if (!posix_kill($masterPid, 0)) {
                unlink("/tmp/jay_master_pid.pid");
                break;
            }
        }
        exit();
        break;
    case "reload":
        if (!file_exists("/tmp/jay_master_pid.pid")) {
            exit("place enter running start~\n");
        }

        $masterPid = file_get_contents("/tmp/jay_master_pid.pid");

        exec("ps --ppid {$masterPid} | awk '/[0-9]/{print $1}' | xargs", $output, $status);

        if ($status === 0) {

            $pidArr = explode(" ", current($output));

            foreach ($pidArr as $child) {
                posix_kill((int)$child, SIGTERM);
            }
        }
        exit();
        break;
    default:
        exit("place enter command start | stop | reload \n");
        break;
}

daemonize();

$masterPid = posix_getpid();

file_put_contents('/tmp/jay_master_pid.pid', $masterPid);

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

    if (count($childs) < 3) {
        fork();
    }
}

