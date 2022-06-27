### Java关于时间的操作

**java时间相关的类**

|                 title                  |    时间线    |类图|
|:--------------------------------------:|:---------:|:----:|
|                 `date`                 |    第一代    |![time-01](https://qiniu.lglg.xyz/images/lglg/posts/202206/27/time-01.png)|
|               `Calendar`               |    第二代    |![time-02](https://qiniu.lglg.xyz/images/lglg/posts/202206/27/time-02.png)|
| `LocalDate ~ LocalTime ~LocalDateTime` |第三代|![time-03](https://qiniu.lglg.xyz/images/lglg/posts/202206/27/time-03.png)|

* 关于`java.util.Date`这个类，其中很多方法，都被官方标记为`@Deprecated`过时的方法。

1. 通过构造器返回一个时间

```java
class Test {
    public static void main(String[] args) {
        Date date = new Date();

        System.out.println(date);
        // Mon Jun 27 15:36:24 CST 2022
    }
}
```

2. 返回一个1970年1月1日以来的毫秒数

```java
class Test {
    public static void main(String[] args) {
        Date date = new Date();

        System.out.println(date.getTime());
        // 1656315384067
    }
}
```

3. `Date`日期格式化类`SimpleDateFormat`，构造器中传入需要格式化时间的参数

```java
class Test {
    public static void main(String[] args) {
        Date date = new Date();
        SimpleDateFormat simpleDateFormat = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss");
        String format = simpleDateFormat.format(date);
        System.out.println(format);
    }
}
```

格式化日期格式需要使用到的参数样例~
![time-04](https://qiniu.lglg.xyz/images/lglg/posts/202206/27/time-04.png)


--------

* `java.util.Calendar`这个第二代的时间类，默认是不能初始化的,它是一个抽象类

```java
class Test {
    public static void main(String[] args) {
        Calendar instance = Calendar.getInstance();
    }
}
```

1. 获取年月日时分秒，通过`get`方法传递参数获取具体的数据

```java
class Test {
    public static void main(String[] args) {
        Calendar instance = Calendar.getInstance();

        System.out.println(instance.getTime());

        System.out.println("年：" + instance.get(Calendar.YEAR));
        System.out.println("月：" + instance.get(Calendar.MONDAY));
        System.out.println("日：" + instance.get(Calendar.DAY_OF_MONTH));
        System.out.println("时：" + instance.get(Calendar.HOUR_OF_DAY));
        System.out.println("分：" + instance.get(Calendar.MINUTE));
        System.out.println("秒：" + instance.get(Calendar.SECOND));
    }
}
```

--------------------

> jdk8 加入了第三代的时间类 `LocalDate`（日期），`LocalTime`（时间），`LocalDateTime`（日期时间）

1. `LocalDateTime`是不能通过构造器初始化

```java
class Test {
    public static void main(String[] args) {
        LocalDateTime localDateTime = LocalDateTime.now();

        System.out.println(localDateTime);
        // 2022-06-27T16:40:52.280
    }
}
```

2. 得到各个部分的时间

```java
class Test {
    public static void main(String[] args) {
        LocalDateTime localDateTime = LocalDateTime.now();

        System.out.println("年：" + localDateTime.getYear());
        System.out.println("月：" + localDateTime.getMonthValue());
        System.out.println("日：" + localDateTime.getDayOfMonth());
        System.out.println("时：" + localDateTime.getHour());
        System.out.println("分：" + localDateTime.getMinute());
        System.out.println("秒：" + localDateTime.getSecond());
    }
}
```

3. 格式化日期`java.time.format.DateTimeFormatter`

```java
class Test {
    public static void main(String[] args) {
        LocalDateTime localDateTime = LocalDateTime.now();
        DateTimeFormatter dateTimeFormatter = DateTimeFormatter.ofPattern("yyyy-MM-dd HH:mm:ss");

        System.out.println(dateTimeFormatter.format(localDateTime));
        // 2022-06-27 16:59:09
    }
}
```

4. 封装了大量对时间日期的操作（加减时间）

```java
class Test {
    public static void main(String[] args) {
        LocalDateTime localDateTime = LocalDateTime.now();
        
        // 加一天
        localDateTime.plusDays(1);
        
        //减三天
        localDateTime.plusDays(-3);
        //...
    }
}
```



