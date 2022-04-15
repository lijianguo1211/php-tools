<?php

namespace Jay\List;

use Jay\List\Exceptions\IndexOutOfBoundsException;
use Jay\List\Exceptions\NoSuchElementException;
use Jay\List\Traits\CheckNode;
use Jay\List\Traits\CurdLinkedList;

/**
 * @Notes:
 *
 * @File Name: LinkedList.php
 * @Date: 2022/4/15
 * @Created By: Jay.Li
 */
class LinkedList
{
    use CurdLinkedList;
    use CheckNode;

    protected int $size = 0;

    protected ?Node $first = null;

    protected ?Node $last = null;


    /**
     * @Notes: 当前链表的头节点
     *
     * @User: Jay.Li
     * @Methods: getFirst
     * @Date: 2022/4/15
     * @return Node|null
     */
    public function getFirst(): ?Node
    {
        return $this->first;
    }

    /**
     * @Notes: 当前链尾部节点
     *
     * @User: Jay.Li
     * @Methods: getLast
     * @Date: 2022/4/15
     * @return Node|null
     */
    public function getLast(): ?Node
    {
        return $this->last;
    }

    /**
     * @Notes: 根据索引id查询节点数据域信息
     *
     * @User: Jay.Li
     * @Methods: get
     * @Date: 2022/4/15
     * @param int $index
     *
     * @return mixed
     * @throws IndexOutOfBoundsException
     */
    public function get(int $index): mixed
    {
        $this->checkElementIndex($index);

        return $this->node($index)->getItem();
    }

    /**
     * @Notes: 链表大小
     *
     * @User: Jay.Li
     * @Methods: size
     * @Date: 2022/4/15
     * @return int
     */
    public function size():int
    {
        return $this->size;
    }

    /**
     * @Notes: 为链表添加一个节点（顺序添加）
     *
     * @User: Jay.Li
     * @Methods: add
     * @Date: 2022/4/15
     * @param $item
     *
     * @return bool
     */
    public function add($item):bool
    {
        $this->linkLast($item);

        return true;
    }

    /**
     * @Notes: 为链表添加一个节点（逆序）
     *
     * @User: Jay.Li
     * @Methods: addFirst
     * @Date: 2022/4/15
     * @param $item
     */
    public function addFirst($item)
    {
        $this->linkFirst($item);
    }

    /**
     * @Notes: 为链表添加节点（顺序）
     *
     * @User: Jay.Li
     * @Methods: addLast
     * @Date: 2022/4/15
     * @param $item
     */
    public function addLast($item)
    {
        $this->linkLast($item);
    }

    /**
     * @Notes: 设置【update】具体某一个节点的值
     *
     * @User: Jay.Li
     * @Methods: set
     * @Date: 2022/4/15
     * @param int $index
     * @param mixed $item
     *
     * @return mixed
     * @throws IndexOutOfBoundsException
     */
    public function set(int $index, mixed $item): mixed
    {
        $this->checkElementIndex($index);

        $node = $this->node($index);

        $oldItem = $node->getItem();

        $node->setItem($item);

        return $oldItem;
    }

    /**
     * @Notes:删除一个具体索引的节点
     *
     * @User: Jay.Li
     * @Methods: removeIndex
     * @Date: 2022/4/15
     * @param int $index
     *
     * @return mixed
     * @throws IndexOutOfBoundsException
     */
    public function removeIndex(int $index): mixed
    {
        $this->checkElementIndex($index);

        return $this->unLink($this->node($index));
    }

    /**
     * @Notes: 从头部开始删除一个节点
     *
     * @User: Jay.Li
     * @Methods: remove
     * @Date: 2022/4/15
     * @return mixed
     * @throws NoSuchElementException
     */
    public function remove(): mixed
    {
        return $this->removeFirst();
    }

    /**
     * @Notes: 从尾部开始删除一个节点
     *
     * @User: Jay.Li
     * @Methods: removeLast
     * @Date: 2022/4/15
     * @return mixed
     * @throws NoSuchElementException
     */
    public function removeLast(): mixed
    {
        $last = $this->last;

        if ($last === null) {
            throw new NoSuchElementException("This list last is empty");
        }

        return $this->unLinkLast($last);
    }

    /**
     * @Notes: 从尾部开始删除一个节点
     *
     * @User: Jay.Li
     * @Methods: removeLast
     * @Date: 2022/4/15
     * @return mixed
     * @throws NoSuchElementException
     */
    public function removeFirst(): mixed
    {
        $first = $this->first;

        if ($first === null) {
            throw new NoSuchElementException("This list first is empty");
        }

        return $this->unLinkFirst($first);
    }
}