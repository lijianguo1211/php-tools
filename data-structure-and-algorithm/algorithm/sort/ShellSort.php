<?php
/**
 * @Notes:
 *
 * @File Name: ShellSort.php
 * @Date: 2022/4/15
 * @Created By: Jay.Li
 */

$arr = [8, 9, 1, 7, 2, 3, 5, 4, 6, 0];

function shellSort(array $arr)
{
    //希尔排序第一轮
    //将数组数据分为len/2组
    for ($i = 5; $i < count($arr); $i++) {
        for ($j = $i - 5; $j >=0; $j -= 5) {
            //如果当前的元素大于加上步长的元素，需要交换
            if ($arr[$j] > $arr[$j + 5]) {
                $tmp = $arr[$j];

                $arr[$j] = $arr[$j + 5];

                $arr[$j + 5] = $tmp;
            }
        }
    }

    echo implode('-', $arr) . PHP_EOL;


    //希尔排序第二轮
    //将数组数据分为len/2组
    for ($i = 2; $i < count($arr); $i++) {
        for ($j = $i - 2; $j >=0; $j -= 2) {
            //如果当前的元素大于加上步长的元素，需要交换
            if ($arr[$j] > $arr[$j + 2]) {
                $tmp = $arr[$j];

                $arr[$j] = $arr[$j + 2];

                $arr[$j + 2] = $tmp;
            }
        }
    }

    echo implode('-', $arr) . PHP_EOL;


    //希尔排序第三轮
    //将数组数据分为len/2组
    for ($i = 1; $i < count($arr); $i++) {
        for ($j = $i - 1; $j >=0; $j -= 1) {
            //如果当前的元素大于加上步长的元素，需要交换
            if ($arr[$j] > $arr[$j + 1]) {
                $tmp = $arr[$j];

                $arr[$j] = $arr[$j + 1];

                $arr[$j + 1] = $tmp;
            }
        }
    }

    echo implode('-', $arr) . PHP_EOL;
}

function shellSort1(array $arr)
{
    $len = count($arr);

    for ($gap = $len >> 1; $gap > 0; ) {

        for ($i = $gap; $i < $len; $i++) {
            for ($j = $i - $gap; $j >=0; $j -= $gap) {
                //如果当前的元素大于加上步长的元素，需要交换
                if ($arr[$j] > $arr[$j + $gap]) {
                    $tmp = $arr[$j];

                    $arr[$j] = $arr[$j + $gap];

                    $arr[$j + $gap] = $tmp;
                }
            }
        }
        echo implode("\t", $arr) . PHP_EOL;

        $gap = floor($gap / 2);
    }

    return $arr;
}

function shellSort2(array $arr)
{
    $len = count($arr);
    for ($gap = $len >> 1; $gap > 0; ) {
        for ($i = $gap; $i < $len; $i++) {
           $j = $i;

           $tmp = $arr[$j];

           if ($arr[$j] < $arr[$j - $gap]) {
               while ($j - $gap >= 0 && $tmp < $arr[$j - $gap]) {
                   $arr[$j] = $arr[$j - $gap];
                   $j -= $gap;
               }

               //退出循环，找到插入位置
               $arr[$j] = $tmp;
           }
        }
        echo implode("\t", $arr) . PHP_EOL;

        $gap = floor($gap / 2);
    }

    return $arr;
}

$arr = shellSort2($arr);
echo implode("\t", $arr) . PHP_EOL;