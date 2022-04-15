<?php

namespace Jay\List;

/**
 * @Notes:
 *
 * @File Name: LinkedList.php
 * @Date: 2022/4/15
 * @Created By: Jay.Li
 */
class LinkedList
{
    protected int $size = 0;

    protected ?Node $first = null;

    protected ?Node $last = null;

    public function getFirst(): ?Node
    {
        return $this->first;
    }

    public function getLast(): ?Node
    {
        return $this->last;
    }

    public function next()
    {
        
    }

    public function size():int
    {
        return $this->size;
    }

    public function add($item):bool
    {
        $this->linkLast($item);

        return true;
    }

    public function addFirst($item)
    {
        $this->linkFirst($item);
    }

    public function addLast($item)
    {
        $this->linkLast($item);
    }

    public function removeLast()
    {
        
    }

    /**
     * @Notes: 在尾部添加数据
     *
     * @User: Jay.Li
     * @Methods: linkLast
     * @Date: 2022/4/15
     * @param $item
     */
    private function linkLast($item):void
    {
        $l = $this->last;

        $newNode = new Node($l, $item, null);

        $this->last = $newNode;

        if ($l === null) {
            $this->first = $newNode;
        } else {
            $l->next = $newNode;
        }

        $this->size++;
    }

    /**
     * @Notes: 在头部添加数据
     *
     * @User: Jay.Li
     * @Methods: linkFirst
     * @Date: 2022/4/15
     * @param $item
     */
    private function linkFirst($item):void
    {
        $f = $this->first;

        $newNode = new Node(null, $item, $f);

        $this->first = $newNode;

        if ($f === null) {
            $this->last = $newNode;
        } else {
            $f->prev = $newNode;
        }

        $this->size++;
    }
}