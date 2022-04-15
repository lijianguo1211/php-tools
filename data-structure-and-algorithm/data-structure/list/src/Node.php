<?php

namespace Jay\List;

/**
 * @Notes:
 *
 * @File Name: Node.php
 * @Date: 2022/4/15
 * @Created By: Jay.Li
 */
class Node
{
    /**
     * @var  Node|null
     */
    public ?Node $next = null;

    /**
     * @var  Node|null
     */
    public ?Node $prev = null;

    /**
     * @var mixed
     */
    protected mixed $item;

    public function __construct(?Node $prev, mixed $element, ?Node $next)
    {
        $this->item = $element;

        $this->prev = $prev;

        $this->next = $next;
    }

    /**
     * @Notes:
     *
     * @User: Jay.Li
     * @Methods: getItem
     * @Date: 2022/4/15
     * @return mixed
     */
    public function getItem(): mixed
    {
        return $this->item;
    }

    /**
     * @Notes:
     *
     * @User: Jay.Li
     * @Methods: setItem
     * @Date: 2022/4/15
     * @param $element
     *
     * @return $this
     */
    public function setItem($element):self
    {
        $this->item = $element;

        return $this;
    }
}