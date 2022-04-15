<?php

namespace Jay\List\Traits;

use Jay\List\Exceptions\IndexOutOfBoundsException;

/**
 * @Notes:
 *
 * @File Name: CheckNode.php
 * @Date: 2022/4/15
 * @Created By: Jay.Li
 */
trait CheckNode
{
    /**
     * @throws IndexOutOfBoundsException
     */
    protected function checkElementIndex(int $index)
    {
        if (!$this->isElementIndex($index)) {
            throw new IndexOutOfBoundsException("Index: " . $index . " Size: " . $this->size);
        }
    }

    /**
     * @Notes: 判断索引id是否在范围内
     *
     * @User: Jay.Li
     * @Methods: isElementIndex
     * @Date: 2022/4/15
     * @param int $index
     *
     * @return bool
     */
    protected function isElementIndex(int $index): bool
    {
        return $index >= 0 && $index < $this->size;
    }
}