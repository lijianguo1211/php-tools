<?php
/**
 * @Notes:
 *
 * @File Name: BubbleSort.php
 * @Date: 2022/3/28
 * @Created By: Jay.Li
 */

$arr = [99, 12, 100, 34, 35, 56, 19, 73, 100, 2];

function bubble($arr = []): array
{
    if (!is_array($arr)) {
        return [];
    }

    $len = count($arr);

    if ($len <= 1) {
        return $arr;
    }

    for ($i = 0; $i < $len; $i++) {
        for ($j = 0; $j < $len - 1; $j++) {
            if ($arr[$j] > $arr[$j + 1]) {
               $tmp = $arr[$j];
               $arr[$j] = $arr[$j + 1];
               $arr[$j + 1] = $tmp;
            }
        }
    }

    return $arr;
}



//优化后

function bubble1($arr = []): array
{
    if (!is_array($arr)) {
        return [];
    }

    $len = count($arr);

    if ($len <= 1) {
        return $arr;
    }

    for ($i = 0; $i < $len; $i++) {
        for ($j = 0; $j < $len - 1 - $i; $j++) {
            if ($arr[$j] > $arr[$j + 1]) {
                $tmp = $arr[$j];
                $arr[$j] = $arr[$j + 1];
                $arr[$j + 1] = $tmp;
            }
        }
    }

    return $arr;
}


function bubble2($arr = []): array
{
    if (!is_array($arr)) {
        return [];
    }

    $len = count($arr);

    if ($len <= 1) {
        return $arr;
    }


    for ($i = 0; $i < $len; $i++) {
        $flag = false;
        for ($j = 0; $j < $len - 1 - $i; $j++) {
            if ($arr[$j] > $arr[$j + 1]) {
                $flag = true;
                $tmp = $arr[$j];
                $arr[$j] = $arr[$j + 1];
                $arr[$j + 1] = $tmp;
            }
        }

        if (!$flag) {
            break;
        }
    }

    return $arr;
}

$arr1 = [1, 2, 3, 4, 5, 6, 7];
$arrSort = bubble2($arr);

array_map(function ($val) {
    echo $val . "\t";
}, $arrSort);