**代理模式**

> 代理模式是一种结构型设计模式， 让你能够提供对象的替代品或其占位符。 代理控制着对于原对象的访问， 并允许在将请求提交给对象前后进行一些处理。

1. 代理模式建议新建一个与原服务对象接口相同的代理类， 然后更新应用以将代理对象传递给所有原始对象客户端。 代理类接收到客户端请求后会创建实际的服务对 
象， 并将所有工作委派给它。
2. 比如客户访问一个站点，它首先访问的时代理服务器，代理服务器经过一层过滤之后，最后请求才到达真正的服务器
3. 代理服务可以延迟服务，等真正调用的时候，才会去示例对象
4. 代理可以实现访问控制，只有通过验证的程序才会去调用服务
5. 日志代理，专门记录请求记录
6. 缓存代理，把资源放在代理服务

**具体实现**

* 定义一个接口

```php
interface RequestInterface
{
    public function request(string $uri):string;
}
```

* 定义一个真正的服务类，实现接口

```php
class HttpReq implements RequestInterface
{
    public function request(string $uri):string
    {
        // TODO: Implement request() method.
        return "我是最总的request方法~ uri : $uri\n";
    }
}
```

* 定义一个代理类，也实现接口

```php
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
```

* 最后调用

```php
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

### output
//Cache not Proxy client
//我是最总的request方法~ uri : https://www.baidu.com/
//Cache not Proxy client
//我是最总的request方法~ uri : https://www.lglg.xyz/
//Cache Proxy Client~ 
//我是最总的request方法~ uri : https://www.lglg.xyz/
//Cache Proxy Client~ 
//我是最总的request方法~ uri : https://www.baidu.com/
```