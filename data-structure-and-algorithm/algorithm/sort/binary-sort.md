### 二分排序

> 二分排序，思想就是先把一个待排序的数组或者集合分为两部分，分别排序，依次再把之前的两部分再各自分为两部分，依次类推，最后拍好序之后再合并。

* 使用PHP简单实现：

```php
function binarySort(array $arr)
{
    if (!is_array($arr)) {
        return [];
    }

    $len = count($arr);

    if ($len <= 1) {
        return $arr;
    }

    //以排序元素集合的第一个元素做基准
    $mid = $arr[0];
    //比基准元素小的元素
    $leftArr = [];
    //比基准元素大的元素
    $rightArr = [];

    for ($i = 1; $i < $len; $i++) {
        if ($mid > $arr[$i]) {
            $leftArr[] = $arr[$i];
        } else {
            $rightArr[] = $arr[$i];
        }
    }

    //递归处理
    $leftArr = binarySort($leftArr);
    //递归处理
    $rightArr = binarySort($rightArr);
    //最后合并
    return array_merge($leftArr, [$mid], $rightArr);
}
```

* 二分快速排序实现

> 第一步还是需要一个基准元素，可以是第一个元素作为基准元素，依次从左边找到比基准小的元素，再从右边找到比基准大的元素，然后这两个元素交换位置，这样就会以
> 基准为中心，左边小，右边大，直到左边下标等于右边下标。此时的基准数不在中间，需要将当前的下标和基准数交换，完成第一次交换，最后再将排序后的数组根据基准
> 数拆开，再进行递归操作，直到left > right 结束。

```php
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

```