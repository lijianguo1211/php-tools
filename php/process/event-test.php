<?php
/**
 * @Notes:
 *
 * @File Name: event-test.php
 * @Date: 2022/3/12
 * @Created By: Jay.Li
 */

$fun1 = function () {
    echo "Supported methods: \n";

    $base1 = new EventBase();

    var_dump($base1->getMethod());
//Returns bitmask of features supported
    var_dump($base1->getFeatures());
    var_dump($base1->getTimeOfDayCached());

    foreach (Event::getSupportedMethods() as $m) {
        echo $m . PHP_EOL;
    }

    $cfg = new EventConfig();

    if ($cfg->avoidMethod('select')) {
        echo "select method avoided \n";
    }

    $base = new EventBase($cfg);

    echo "Event method used: " . $base->getMethod() . PHP_EOL;

    echo "Features: \n";

    $features = $base->getFeatures();

    ($features & EventConfig::FEATURE_ET) and print("ET - edge-triggered IO\n");
    ($features & EventConfig::FEATURE_O1) and print("O1 - O(1) operation for adding/deletting events\n");
    ($features & EventConfig::FEATURE_FDS) and print("FDS - arbitrary file descriptor types, and not just sockets\n");

    if ($cfg->requireFeatures(EventConfig::FEATURE_FDS)) {
        echo "FDS feature is now required\n";

        $base = new EventBase($cfg);
        ($base->getFeatures() & EventConfig::FEATURE_FDS)
        and print("FDS - arbitrary file descriptor types, and not just sockets\n");
    }

    $base = new EventBase();

    for ($i = 0; $i < 10; $i++) {
        fwrite(STDIN, microtime());
    }


    $event = new Event($base, STDIN, Event::READ | Event::PERSIST, function ($fd, $events, $arg) {
        static $maxIterations = 0;

        if (++$maxIterations >= 5) {
            echo "Stopping...\n";

            $arg[0]->exit(2.33);
        }

        echo fgets($fd);
    }, [&$base]);

    $event->add();

    $base->loop();

    while (true) {
        if ($base->gotExit()) {
            echo "事件已退出！\n";
            break;
        }
    }
};

$fun2 = function () {
    $base = new EventBase();

//    $event = new Event($base, STDIN, Event::READ | Event::PERSIST, function ($fd, $events, $arg) {
//        static $maxIterations = 0;
//
//        if (++$maxIterations >= 5) {
//            echo "Stopping...\n";
//
//            $arg[0]->exit(2.33);
//        }
//
//        echo fgets($fd);
//    }, [&$base]);

//    $event->add();

    $event = Event::signal($base, \SIGTRAP, function ($sig, $arg) {

        echo "listen sig = $sig" . PHP_EOL;

        $arg[0]->exit();


    }, [&$base]);

    $event->add();

    $pid = pcntl_fork();

    if ($pid == -1) {
        exit("Create process failed~\n");
    }

    if ($pid === 0) {

        $ppid = posix_getpid();

        echo "Create child process success pid = $ppid\n";

        $num = 0;
        while (1) {
            echo $num . PHP_EOL;
            sleep(1);
            $num++;
            if ($num > 10) {
                posix_kill($ppid, \SIGTRAP);
                break;
            }
        }

    } else {
        echo "Create parent process success pid = " . posix_getpid() . PHP_EOL;
        $base->loop();
        while (true) {
            if ($base->gotExit()) {
                echo "信号事件已经退出~\n";
                break;
            }

            sleep(2);
        }

        pcntl_waitpid($pid, $status, WUNTRACED);

        echo "parent process wait child pid = $pid parent pid = " . posix_getpid() . PHP_EOL;

        while (true) {
            sleep(5);
            echo time() . PHP_EOL;
        }
    }
};

$fun2();

