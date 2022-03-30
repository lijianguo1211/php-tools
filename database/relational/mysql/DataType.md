### MySQL提供的数据类型

**字符类型**

|类型|大小|用途|
|:---:|:---:|:----:|
|char|0-255bytes|固定长度字符串
|varchar|0-65535bytes|变长字符串
|tinyblob|0-255bytes|二进制字符
|tinytext|0-255bytes|短文本字符串
|blob|0-65535bytes|二进制字符串
|text|0-65535bytes|长文本字符串
|mediumblob|0-16 777 215 bytes|中等二进制数据
|mediumtext|0-16 777 215 bytes|中等文本数据
|longblob|0-4 294 967 295 bytes|极大二进制数据
|longtext|0-4 294 967 295 bytes|极大文本数据

**数字类型**

|类型|大小|范围|用途|
|:---:|:---:|:---:|:---:|
|tinyint|1字节|2^8-1 (-128, 127)|小整数
|smallint|2字节|2^16-1 (-32 768，32 767)|大整数
|mediumint|3字节|2^24-1 (-8 388 608，8 388 607)|大整数
|int|4字节|2^32-1 (-2 147 483 648，2 147 483 647)|大整数
|bigint|8字节|2^64-1 (-9,223,372,036,854,775,808，9 223 372 036 854 775 807)|极大整数
|float|4字节|(-3.402 823 466 E+38，-1.175 494 351 E-38)，0，(1.175 494 351 E-38，3.402 823 466 351 E+38)|单精度浮点数
|double|8字节|(-1.797 693 134 862 315 7 E+308，-2.225 073 858 507 201 4 E-308)，0，(2.225 073 858 507 201 4 E-308，1.797 693 134 862 315 7 E+308)|双精度浮点数
|decimal|~|~|~

**时间类型**

|类型|大小|用途|
|:---:|:---:|:---:|
|year|1字节|YYYY
|date|3字节|YYYY-MM-DD
|time|3字节|HH:MM:SS
|datetime|8字节|YYYY-MM-DD HH:MM:SS
|timestamp|4字节|YYYYMMDD HHMMSS

**特殊类型**

|类型|大小|用途|
|:---:|:---:|:---:|
|enum|1~2个字节|枚举
|set|1~2~3~4~8个字节|集合
|json|~|json

```mysql
create table shirts (name varchar(40), size enum('x-small', 'small', 'medium', 'large', 'x-large'));

INSERT INTO shirts (name, size) VALUES ('dress shirt','large'), ('t-shirt','medium'),
                                       ('polo shirt','small');

SELECT name, size FROM shirts WHERE size = 'medium';

UPDATE shirts SET size = 'small' WHERE size = 'large';
```

````mysql
create TABLE t1 (`jdoc` json);

INSERT INTO t1 VALUES('{"key1": "value1", "key2": "value2"}');
INSERT INTO t1 VALUES(json_object('key1', 1, 'key2', 2, 'key3', 3));
````

```mysql
CREATE TABLE `myset` (`col` SET('a', 'b', 'c', 'd'));

INSERT INTO myset (col) VALUES ('a,d'), ('d,a'), ('a,d,a'), ('a,d,d'), ('d,a,d');

SELECT col FROM myset;

SELECT * FROM myset WHERE FIND_IN_SET('d', col)>0;

SELECT * FROM myset WHERE col LIKE '%a%';
```