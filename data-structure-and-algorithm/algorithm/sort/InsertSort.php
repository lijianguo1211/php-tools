<?php
/**
 * @Notes:
 *
 * @File Name: InsertSort.php
 * @Date: 2022/3/28
 * @Created By: Jay.Li
 */


$arr = [23, 10, 48, 10, 99, 10];

function insert(array $arr): array
{
    if (!is_array($arr)) {
        return [];
    }

    $len = count($arr);

    if ($len <= 1) {
        return $arr;
    }

    //$arr = [23, 10, 48, 10, 99, 10];
    //$arr = [10, 23, 48, 10, 99, 10];
    //$arr = [10, 23, 48, 10, 99, 10];
    for ($i = 1; $i < $len; $i++) {
        $tmp = $arr[$i];//10 48 10
        $j = $i;//1 2 3

        // 10 < 23 -> 10 < 48 ->
        while ($j > 0 && $tmp < $arr[$j - 1]) {
            $arr[$j] = $arr[$j - 1];
            $j--;
        }

        if ($j !== $i) {
            $arr[$j] = $tmp;
        }
    }

    return $arr;
}

$arrSort = insert($arr);

array_map(function ($val) {
    echo $val . "\t";
}, $arrSort);