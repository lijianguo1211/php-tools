<?php
/**
 * @Notes:
 *
 * @File Name: timer.php
 * @Date: 2022/4/12
 * @Created By: Jay.Li
 */
//declare(ticks = 1);
//pcntl_async_signals(true);
//$interval = 1;
//pcntl_signal(SIGALRM, function ($signal) use ($interval) {
//    echo "this signal id = $signal \n";
//    pcntl_alarm($interval);
//}, false);
//
//$pid = pcntl_fork();
//
//if ($pid === 0) {
//    pcntl_alarm($interval);
//    echo 'Sleep (will be interrupted) started' . PHP_EOL;
//    sleep(1000);
//    echo 'Sleep ended soon due to interrupt' . PHP_EOL;
//    $i = 0;
//    while ($i < 10) {
//        $i++;
//        sleep(10);
//    }
//
//    exit();
//}
//
//while (true) {
//    pcntl_signal_dispatch();
//    pcntl_wait($status);
//    pcntl_signal_dispatch();
//    sleep(1);
//}



$fun1 = function () {
//    declare(ticks = 1);

    pcntl_signal(SIGALRM, function ($signal) {
           echo "pcntl signal sigalrm ".time()."\n";

           pcntl_alarm(5);
    }, false);

    pcntl_alarm(4);

    while (1) {
        pcntl_signal_dispatch();
       sleep(1);
        pcntl_signal_dispatch();
    }
};

//$fun1();

$fun2 = function () {


    pcntl_signal(SIGALRM, function ($signal) {
        echo "pcntl signal sigalrm ".time()."\n";

        pcntl_alarm(5);
    }, false);

    pcntl_alarm(4);

    while (1) {
        pcntl_signal_dispatch();
    }
};

//$fun2();


$fun3 = function () {
    pcntl_signal(SIGALRM, function ($signal) {
        echo "pcntl signal sigalrm ".time()."\n";
    }, false);

    echo "pcntl_alarm start ".time()."\n";
    pcntl_alarm(4);
    pcntl_signal_dispatch();
    echo "start " . time() . PHP_EOL;
    sleep(1000);

    echo "end " . time() . PHP_EOL;
};

//$fun3();

$fun4 = function () {
    declare(ticks = 1);
    pcntl_signal(SIGALRM, function ($signal) {
        echo "pcntl signal sigalrm ".time()."\n";
    }, false);

    echo "pcntl_alarm start ".time()."\n";
    pcntl_alarm(10);
    echo "start " . time() . PHP_EOL;
//    sleep(1000);
//
//    echo "end " . time() . PHP_EOL;

//    while (true) {
//        sleep(10);
//        echo "end " . time() . PHP_EOL;
//    }

};

//$fun4();


$fun5 = function () {
    declare(ticks=1);

// 每次 tick 事件都会调用该函数
    function tick_handler()
    {
        echo "tick_handler() called\n";
    }

    register_tick_function('tick_handler'); // 引起 tick 事件

    $a = 1; // 引起 tick 事件

    if ($a > 0) {
        $a += 2; // 引起 tick 事件
        print($a); // 引起 tick 事件
    }
};

//$fun5();

$fun6 = function () {
    pcntl_signal(SIGALRM, function ($signal) {
        echo "pcntl signal sigalrm ".time()."\n";

        pcntl_alarm(5);
    }, false);

    pcntl_alarm(3);

    while (true) {
        pcntl_signal_dispatch();
    }
};

//$fun6();

$fun7 = function () {
    pcntl_async_signals(true);
    pcntl_signal(SIGALRM, function ($signal) {
        echo "pcntl signal sigalrm ".time()."\n";
    }, false);

    echo "start " . time() . "\n";
    pcntl_alarm(3);
    echo "走\n";

    sleep(100);
};
//$fun7();

$fun8 = function () {
    pcntl_async_signals(true);
    pcntl_signal(SIGALRM, function ($signal) {
        echo "pcntl signal sigalrm ".time()."\n";
        pcntl_alarm(2);
    }, false);

    echo "start " . time() . "\n";

    echo pcntl_alarm(1) . "\n";

    while (true) {
        sleep(100);
    }

};
//$fun8();

$fun9 = function () {

    $base = new EventBase();

    $event = Event::timer($base, function ($arg) {
        echo "timer :" .time() . PHP_EOL;
    }, ['num' => 2]);
    $event->addTimer(1);

    $base->loop();
};

//$fun9();

$fun10 = function () {

    $base = new EventBase();

    $event = new Event($base, -1, Event::TIMEOUT, function ($fd, $what, $e) {
        echo "timer 2s end time: " . time() . PHP_EOL;

        $e->delTimer();
    });

    echo "timer start time：" . time() . PHP_EOL;
    $event->data = $event;
    $event->add(2);

    $base->loop();
};
//$fun10();

$fun11 = function () {

    $base = new EventBase();

    $event = new Event($base, -1, Event::TIMEOUT|Event::PERSIST, function ($fd, $what) {
        echo "timer 2s end time: " . time() . PHP_EOL;
    });

    echo "timer start time：" . time() . PHP_EOL;
    $event->add(2);

    $base->loop();
};
//$fun11();

$fun12 = function () {
    function stopAll($sig){
        echo "master has a sig $sig\n" ;
    }

    $master_id = getmypid();

    $pid = pcntl_fork();
    if($pid > 0)
    {
        pcntl_signal(SIGINT,'stopAll') ;
        $epid = pcntl_wait($status,WUNTRACED);
//        pcntl_signal_dispatch();
        echo "parent process {$master_id}, child process {$pid}\n";
        if($epid){
            echo "child $epid exit \n" ;
        }
    }
    else
    {
        $id = getmypid();
        echo "child process,pid {$id}\n";
        sleep(6);
        echo "send signal to master\n";
        posix_kill($master_id, SIGINT);

        exit(12);
    }
};

$fun12();