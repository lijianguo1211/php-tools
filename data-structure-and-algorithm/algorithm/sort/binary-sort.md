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

* 二分排序的非递归实现

> 还是需要利用插入排序的思想，外层的 `for`循环控制比较次数，从下标为1的元素开始循环。
> 下标为$i的数据()当做每次比较的基准数
> 每次比较都是从0开始，到$i-1结束[这个里面的数据是已经排序好的数据]，后面待排序的数字在这个已经排序好的数据中，二分查找自己应该插入的位置

```php
//[1, 3, 5, 7, 9] 19
// L     M     R // mid = 2 arr[2] = 5
//5 > 19 ? false 说明待插入元素不在左半边，这个时候，需要把L的位置移动到M+1的位置
//[1, 3, 5, 7, 9] 19
//       L  M  R // mid = 3 arr[3] = 7
//7 > 19 ? false 说明待插入的元素在右半边，这个时候，L = M + 1 = 4
//[1, 3, 5, 7, 9] 19
//             L //判断 L <= R,继续比较，
//这个时候，M = L, 9 > 19 ? false, L = M + 1 
// L > M 说明找到需要插入的位置就在L了
//下面这个交换也是有意思的。上面的L是代表最终需要插入的位置，但是如果直接插入进去，原来的数怎么半？
//j = 5, l = 5
//每次交换位置的数据都是把待插入之后的数据先向后移动交换
//但是不能超出待插入位置
for ($j = $i - 1; $j >= $left; $j--) {
    $arr[$j + 1] = $arr[$j];
}
```

* 具体的PHP实现

```php
function binarySort3(array $arr)
{
    for ($i = 1; $i < count($arr); $i++) {
        $tmp = $arr[$i];

        $left = 0;
        $right = $i - 1;
        $mid = -1;
        //30, 2, 4, 5,90, 1, 28
//        echo sprintf("%d 次循环， left = %d, right = %d tmp = %d mid = %d\n", $i, $left, $right, $tmp, ($left + $right) >> 1);
        while ($left <= $right) {
            $mid = ($left + $right) >> 1;
//            echo sprintf("%d 次循环， mid = %d\t", $i, $mid);
            if ($arr[$mid] > $tmp) {
                $right = $mid - 1;
            } else {
                $left = $mid + 1;
            }
        }
//        echo "\n";

        echo sprintf("%d 次循环， j = %d, left = %d\n", $i, $i - 1, $left);
        for ($j = $i - 1; $j >= $left; $j--) {
            echo sprintf("j + 1 = %d , j = %d \t", $arr[$j + 1], $arr[$j]);
            $arr[$j + 1] = $arr[$j];

            echo implode('-', $arr) . "\t";
        }

        echo "\n";
        $arr[$left] = $tmp;
    }

    return $arr;
}
```