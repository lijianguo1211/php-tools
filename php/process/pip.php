<?php
/**
 * @Notes:
 *
 * @File Name: pip.php
 * @Date: 2022/3/10
 * @Created By: Jay.Li
 */

$fun1 = function () {
    $cmd = "cat";

    $desc = [
        0 => [
            'pipe',
            'r'
        ],
        1 => [
            'pipe', 'w'
        ],
        [
            'file', '/tmp/proc_open.err', 'a'
        ]
    ];

    for ($i = 0; $i < 5; $i++) {
        $handle = proc_open($cmd, $desc, $pipes);

        sleep(1);

        fwrite($pipes[0], date("Y-m-d H:i:s"));

        $handles[] = [
            'handle' => $handle,
            'output' => $pipes[1]
        ];

        fclose($pipes[0]);
    }

    foreach ($handles as $array) {
        echo fread($array['output'], 1024) . PHP_EOL;

        fclose($array['output']);

        proc_close($array['handle']);
    }
};

$fun1();

$fun2 = function () {
  $file = "/tmp/abc";

  if (!file_exists($file)) {
      $res = posix_mkfifo($file, 0644);

      if (!$res) {
          exit("Create fifo file failed~\n");
      }
  }
  $i = 0;
  while ($i < 15) {
      $handle = fopen($file, 'w');

      fwrite($handle, "{$i}\n");

      $i++;
      sleep(1);
  }
};

$fun2();