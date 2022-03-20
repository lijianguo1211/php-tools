<?php

use JetBrains\PhpStorm\Pure;

/**
 * @Notes:
 *
 * @File Name: FactoryMethod.php
 * @Date: 2022/3/20
 * @Created By: Jay.Li
 */

interface UserInterface
{
    public function login(string $user, string $password):string;

    public function createPost(string $content):bool;

    public function logout():string;
}

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

$client = function (SignInUser $inUser, $content, $user, $password) {
    $inUser->content($content, $user, $password);
};
echo "######\n";
$client(new QqSignInUser(), '123', '张三', '11111****');
echo "######\n";
$client(new WechatSignInUser(), '456', '李四', '2222****');
echo "######\n";
$client(new PhoneSignInUser(), '789', '王二', '3333****');