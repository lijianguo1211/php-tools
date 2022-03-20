<?php
/**
 * @Notes:
 *
 * @File Name: prototype.php
 * @Date: 2022/3/20
 * @Created By: Jay.Li
 */

/**
 * Notes: 原型类
 *
 * @Class Name: Page
 * @Date: 2022/3/20
 * @Created By: Jay.Li
 */
class Page
{
    /**
     * 全是私有变量
     * @var  string
     */
    private string $title;

    private string $body;

    /**
     * @var  Author
     */
    private Author $auther;

    private array $comments = [];

    /**
     * @var DateTime
     */
    private DateTime $data;

    public function __construct(string $title, string $body, Author $author)
    {
        $this->title = $title;

        $this->body = $body;

        $this->auther = $author;

        $this->auther->addToPage($this);
        
        $this->data = new \DateTime();
    }

    public function addComments(string $commend):void
    {
        $this->comments[] = $commend;
    }

    /**
     * @Notes: 需要克隆的数据
     *
     * @User: Jay.Li
     * @Methods: __clone
     * @Date: 2022/3/20
     */
    public function __clone()
    {
        $this->title = "Copy of " . $this->title;

        $this->auther->addToPage($this);

        $this->comments = [];

        $this->data = new \DateTime();
    }


}

class Author
{
    /**
     * @var  string
     */
    private string $name;

    /**
     * @var  array
     */
    private array $pages = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }


    public function addToPage(Page $page)
    {
        $this->pages[] = $page;
    }
}

$author = new Author("李四");

$page = new Page("大家好", "展示问候语呀~", $author);

$page->addComments("start | stop | reload");

$cloneObj = clone $page;

print_r($cloneObj);