<?php
/**
 * @Notes:
 *
 * @File Name: BinarySort.php
 * @Date: 2022/4/13
 * @Created By: Jay.Li
 */

$arr = [10, 23, 12, 10, 99, 10, 35, 1];

//二分插入排序，在插入排序上做升级
function binary(array $arr): array
{
    if (!is_array($arr)) {
        return [];
    }

    $len = count($arr);

    if ($len <= 1) {
        return $arr;
    }

    /**
     * $left 要排序的范围中第一个元素的索引
     * $right 要排序的范围中最后一个元素之后的索引
     * $start 尚未被排序的第一个元素
     * $left < $start < $right
     */
    for ($start = 0; $start < $len; $start++) {
        //
        $pivot = $arr[$start];

        $left = 0;

        $right = $start - 1;

        while ($left <= $right) {
            $mid = ($left + $right) >> 1;
            if ($arr[$mid] > $pivot) {
                $right = $mid - 1;
            } else {
                $left = $mid + 1;
            }
        }

       for ($j = $start - 1; $j > $left; $j--) {
           $arr[$j++] = $arr[$j];
       }

       $arr[$left] = $pivot;

    }

    return $arr;
}

function binarySort(array $arr)
{
    if (!is_array($arr)) {
        return [];
    }

    $len = count($arr);

    if ($len <= 1) {
        return $arr;
    }

    $mid = $arr[0];
    $leftArr = [];
    $rightArr = [];

    for ($i = 1; $i < $len; $i++) {
        if ($mid > $arr[$i]) {
            $leftArr[] = $arr[$i];
        } else {
            $rightArr[] = $arr[$i];
        }
    }

    $leftArr = binarySort($leftArr);

    $rightArr = binarySort($rightArr);

    return array_merge($leftArr, [$mid], $rightArr);
}

function binarySort2(array &$arr, int $left, int $right)
{
    //递归结束条件
    if ($left > $right) {
        return;
    }
    //选择一个基准数
    $base = $arr[$left];
    //左边下标
    $i = $left;
    //右边下标
    $j = $right;
    //[10, 23, 12, 10, 99, 10, 35, 1]
    //当左边下标不等于右边下标时
    while ($i !== $j) { // 0 != 7 | 1 != 7
        //判断数组右边的元素是否大于基准数，并且左边的下标要小于右边的下标
        while ($arr[$j] >= $base && $i < $j) {
            //arr[7] = 1 >= 10 false, j = 7
            //arr[7] = 23 >= 10 1 < 7
            //arr[6] = 35 >= 10 1 < 6
            //arr[5] = 10 >= 10 1 < 5
            //arr[4] = 99 >= 10 1 < 4
            //arr[3] = 10 >= 10 1 < 3
            //arr[2] = 12 >= 10 1 < 2
            //arr[1] = 1 >= 10 false
            //符合条件的，数组右边的下标向前移动一位
            $j--;//6 5 4 3 2 1
        }
        ////判断数组左边的元素是否小于基准数，并且左边的下标要小于右边的下标
        while ($arr[$i] <= $base && $i < $j) {//
            // arr[0] = 10 <= 10 | arr[1] = 23 <= 10 ? false i = 1
            //arr[1] = 1 <= 10 1 < 1 false
            //符合条件的，数组左边的下标需要向前移动一位
            $i++;//1
        }

        // tmp = arr[1] = 23
        // tmp = arr[1] = 1
        $tmp = $arr[$i];
        // arr[1] = arr[j] = 1
        //arr[1] = arr[j] = 1
        $arr[$i] = $arr[$j];
        // arr[7] = 23
        //arr[1] = 1
        $arr[$j] = $tmp;
        //arr = [10, 1, 12, 10, 99, 10, 35, 23]
    }

    //left = 0, i = 1
    //arr[0] => 10 = arr[1] => 1
    ///arr = [1, 1, 12, 10, 99, 10, 35, 23]
    $arr[$left] = $arr[$i];

    //arr[1] => 1 = $base = 10
    //arr = [1, 10, 12, 10, 99, 10, 35, 23]
    $arr[$i] = $base;

    //left = 0, right = 1 - 1 = 0
    binarySort2($arr, $left, $i - 1);

    //left 1 + 1 = 2, right = 7
    binarySort2($arr, $i + 1, $right);
}


//$arrSort = binarySort($arr);
//$arrSort = binary($arr);
binarySort2($arr, 0, count($arr) - 1);

$arrSort = $arr;

array_map(function ($val) {
    echo $val . "\t";
}, $arrSort);


