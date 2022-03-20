#### 工厂方法模式

>工厂方法模式是一种创建型设计模式， 其在父类中提供一个创建对象的方法， 允许子类决定实例化对象的类型。

1. 在编写代码时，无法预知对象具体的依赖时，可以使用工厂方法
2. 工厂方法将具体的使用和创建分离开
3. 工厂方法可以更加容易扩展，当要扩展这一组件的时候，可以根据协议更加容易的扩展，不需要去修改原来的代码，造成混乱
4. 

**具体的实现**

1. 创造一个统一的接口，所有的产品都去实现这个接口
2. 在创建类中添加一个空的工厂方法，返回类型必须是遵循是上层的接口
3. 在创建者代码中找到对于产品构造函数的所有引用。
4. 为工厂方法中的每种产品编写一个创建者子类， 然后在子类中重写工厂方法， 并将基本方法中的相关创建代码移动到工厂方法中

**具体的例子**

1. 网站有不同的登录方式，QQ,微信，手机号，邮箱
2. 申明一个接口，接口中有登录，登出,创建文章数据
3. 再次申明一个抽象创建者类，抽象一个创建者方法，返回接口
4. 不同的创建者继承这个创建者类
5. 每次调用，传递不同的不同的创建者

**代码实现**

* 申明接口

```php
interface UserInterface
{
    public function login(string $user, string $password):string;

    public function createPost(string $content):bool;

    public function logout():string;
}
```

* 不同方式的登录都实现这个接口

```php
class QqUser implements UserInterface
{
    public function login(string $user, string $password):string
    {
        // TODO: Implement login() method.
        return sprintf("QQ 登录 用户名：%s 密码：%s\n", $user, $password);
    }

    public function logout():string
    {
        // TODO: Implement logout() method.
        return "QQ 用户退出~\n";
    }

    public function createPost(string $content): bool
    {
        // TODO: Implement createPost() method.
        return true;
    }
}

class WechatUser implements UserInterface
{
    public function login(string $user, string $password):string
    {
        // TODO: Implement login() method.
        return sprintf("微信 登录 用户名：%s 密码：%s\n", $user, $password);
    }

    public function logout():string
    {
        // TODO: Implement logout() method.
        return "微信 用户退出~\n";
    }

    public function createPost(string $content): bool
    {
        // TODO: Implement createPost() method.
        return true;
    }
}

class PhoneUser implements UserInterface
{

    public function login(string $user, string $password):string
    {
        // TODO: Implement login() method.
        return sprintf("手机 登录 用户名：%s 密码：%s\n", $user, $password);
    }

    public function logout():string
    {
        // TODO: Implement logout() method.
        return "手机 用户退出~\n";
    }

    public function createPost(string $content): bool
    {
        // TODO: Implement createPost() method.
        return true;
    }
}
```

* 抽象创建者

````php
abstract class SignInUser
{
    abstract public function factoryMethod(): UserInterface;

    public function content(string $post, string $user, string $password)
    {
        $obj = $this->factoryMethod();

        $res1 = $obj->login($user, $password);

        echo $res1;

        $res2 = $obj->createPost($post);

        if ($res2) {
            echo "发布文章成功了~\n";
        }

        echo $obj->logout();
    }
}
````

* 具体的创建者

````php
class QqSignInUser extends SignInUser
{
    #[Pure]
    public function factoryMethod(): UserInterface
    {
        // TODO: Implement factoryMethod() method.
        return new QqUser();
    }
}

class WechatSignInUser extends SignInUser
{
    #[Pure]
    public function factoryMethod(): UserInterface
    {
        // TODO: Implement factoryMethod() method.
        return new WechatUser();
    }
}

class PhoneSignInUser extends SignInUser
{
    #[Pure]
    public function factoryMethod(): UserInterface
    {
        // TODO: Implement factoryMethod() method.
        return new PhoneUser();
    }
}
````

* 调用

````php
$client = function (SignInUser $inUser, $content, $user, $password) {
    $inUser->content($content, $user, $password);
};
echo "######\n";
$client(new QqSignInUser(), '123', '张三', '11111****');
echo "######\n";
$client(new WechatSignInUser(), '456', '李四', '2222****');
echo "######\n";
$client(new PhoneSignInUser(), '789', '王二', '3333****');
````