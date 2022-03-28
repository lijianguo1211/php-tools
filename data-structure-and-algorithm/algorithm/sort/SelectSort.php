<?php
/**
 * @Notes:
 *
 * @File Name: SelectSort.php
 * @Date: 2022/3/28
 * @Created By: Jay.Li
 */

$arr = [1, 99, 108, 2, 34, 4, 67, 25, 79, 100, 99];

function select($arr = [])
{
    if (!is_array($arr)) {
        return [];
    }

    $len = count($arr);

    if ($len <= 1) {
        return $arr;
    }

    //假设一个数是最小值，让这个最小值去比较，
    //加入最小的一个数据是第一个元素

    for ($i = 0; $i < $len - 1; $i++) {
        $min = $i;
        for ($j = $i + 1; $j < $len; $j++) {
            if ($arr[$j] < $arr[$min]) {
                $min = $j;
            }
        }

        if ($i != $min) {
            $temp = $arr[$i];
            $arr[$i] = $arr[$min];
            $arr[$min] = $temp;
        }
    }


    return $arr;
}

$arrSort = select($arr);

array_map(function ($val) {
    echo $val . "\t";
}, $arrSort);