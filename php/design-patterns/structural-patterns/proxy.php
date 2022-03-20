<?php
/**
 * @Notes:
 *
 * @File Name: proxy.php
 * @Date: 2022/3/20
 * @Created By: Jay.Li
 */

interface RequestInterface
{
    public function request(string $uri):string;
}

class HttpReq implements RequestInterface
{
    public function request(string $uri):string
    {
        // TODO: Implement request() method.
        return "我是最总的request方法~ uri : $uri\n";
    }
}

class Proxy implements RequestInterface
{
    protected HttpReq $req;

    public array $cache = [];

    public function __construct(HttpReq $req)
    {
        $this->req = $req;
    }

    public function request(string $uri):string
    {
        // TODO: Implement request() method.
        if (!isset($this->cache[$uri])) {
            echo "Cache not Proxy client\n";

            $result = $this->req->request($uri);

            $this->cache[$uri] = $result;
        } else {
            echo "Cache Proxy Client~ \n";
        }

        return $this->cache[$uri];
    }
}

$req = new HttpReq();

$proxy = new Proxy($req);

$res1 = $proxy->request("https://www.baidu.com/");
echo $res1;
$res1 = $proxy->request("https://www.lglg.xyz/");
echo $res1;
$res1 = $proxy->request("https://www.lglg.xyz/");
echo $res1;
$res1 = $proxy->request("https://www.baidu.com/");
echo $res1;