<?php
/**
 * @Notes:
 *
 * @File Name: InsertSort.php
 * @Date: 2022/3/28
 * @Created By: Jay.Li
 */


$arr = [10, 23, 12, 10, 99, 10];

function insert(array $arr): array
{
    if (!is_array($arr)) {
        return [];
    }

    $len = count($arr);

    if ($len <= 1) {
        return $arr;
    }

    //从1开始，把0看作是一个有序的列表，1是第一个待插入的元素
    for ($i = 1; $i < $len; $i++) {
        $tmp = $arr[$i];//把待插入的元素赋值到一个零时变量 12
        $j = $i;//待插入元素的位置先记录到变量 2

        //查找待插入元素需要插入到有序列表的位置
        //终止查找的条件
        //1. 不能数组越界
        //2. 待插入的元素需要小于有序列表的最后一个元素
        while ($j > 0 && $tmp < $arr[$j - 1]) { //2 12 < 23 12 < 10
            //交换位置，把大数据向后交换
            $arr[$j] = $arr[$j - 1];//12 = 23
            //$j 向前移动一位
            $j--;
        }

        //当$j != $i 说明需要插入元素，交换
        if ($j !== $i) {//1 != 2
            $arr[$j] = $tmp;// arr[1] = 12
        }
    }

    return $arr;
}

$arrSort = insert($arr);

array_map(function ($val) {
    echo $val . "\t";
}, $arrSort);