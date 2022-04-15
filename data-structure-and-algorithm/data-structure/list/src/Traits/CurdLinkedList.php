<?php

namespace Jay\List\Traits;

use Jay\List\Node;

/**
 * @Notes:
 *
 * @File Name: CurdLinkedList.php
 * @Date: 2022/4/15
 * @Created By: Jay.Li
 */
trait CurdLinkedList
{
    /**
     * @Notes: 删除一个最开始的节点
     *
     * @User: Jay.Li
     * @Methods: unLinkFirst
     * @Date: 2022/4/15
     * @param Node $first
     *
     * @return mixed
     */
    private function unLinkFirst(Node $first): mixed
    {
        $element = $first->getItem();

        $next = $first->next;

        $first->setItem(null)->next = null;

        $this->first = $next;

        if ($next === null) {
            $this->last = null;
        } else {
            $next->prev = null;
        }

        $this->size--;

        return $element;
    }

    /**
     * @Notes:删除最后一个节点
     *
     * @User: Jay.Li
     * @Methods: unLinkLast
     * @Date: 2022/4/15
     *
     * @param Node $last
     *
     * @return mixed
     */
    private function unLinkLast(Node $last): mixed
    {
        $element = $last->getItem();

        $prev = $last->prev;

        $last->setItem(null)->prev = null;

        $this->last = $prev;

        if ($prev === null) {
            $this->first = null;
        } else {
            $this->last->next = null;
        }

        $this->size--;

        return $element;
    }

    /**
     * @Notes: 删除一个指定节点
     *
     * @User: Jay.Li
     * @Methods: unLink
     * @Date: 2022/4/15
     * @param Node $node
     *
     * @return mixed
     */
    private function unLink(Node $node): mixed
    {
        $element = $node->getItem();

        //待删除几点的后一个节点
        $next = $node->next;

        //待删除节点的前一个节点
        $prev = $node->prev;

        //如果待删除的前一个节点为空指针,就需要把待删除的节点的下一个节点设为头节点
        if ($prev === null) {
            $this->first = $next;
        } else {
            //把待删除节点的前一个节点的next指针指向待删除节点的下一个节点
            $prev->next = $next;
            //待删除节点prev指针设为null
            $node->prev = null;
        }

        //如果待删除的节点就是最后一个节点，就需要把待删除节点的前一个节点设为最后一个节点
        if ($next === null) {
            $this->last = $prev;
        } else {
            //把待删除节点的下一个节点的prev指针指向待删除节点的前一个节点
            $next->prev = $prev;
            //同时把待删除节点的next指针只为null
            $node->next = null;
        }

        //长度减一
        $this->size--;

        //数据域设为空
        $node->setItem(null);

        return $element;
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

    /**
     * @Notes:根据传递的索引id找节点
     *
     * @User: Jay.Li
     * @Methods: node
     * @Date: 2022/4/15
     * @param int $index
     *
     * @return Node
     */
    protected function node(int $index): Node
    {
        if ($index < ($this->size >> 1)) {
            $x = $this->first;
            for ($i = 0; $i < $index; $i++) {
                $x = $x->next;
            }
        } else {
            $x = $this->last;
            for ($i = $this->size - 1; $i > $index; $i--) {
                $x = $x->prev;
            }
        }

        return $x;
    }
}