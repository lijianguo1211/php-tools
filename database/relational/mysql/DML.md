### 数据操作语言

**新增（insert）**

```
INSERT [LOW_PRIORITY | DELAYED | HIGH_PRIORITY] [IGNORE]
    [INTO] tbl_name
    [PARTITION (partition_name [, partition_name] ...)]
    [(col_name [, col_name] ...)]
    { {VALUES | VALUE} (value_list) [, (value_list)] ... }
    [AS row_alias[(col_alias [, col_alias] ...)]]
    [ON DUPLICATE KEY UPDATE assignment_list]

INSERT [LOW_PRIORITY | DELAYED | HIGH_PRIORITY] [IGNORE]
    [INTO] tbl_name
    [PARTITION (partition_name [, partition_name] ...)]
    SET assignment_list
    [AS row_alias[(col_alias [, col_alias] ...)]]
    [ON DUPLICATE KEY UPDATE assignment_list]

INSERT [LOW_PRIORITY | HIGH_PRIORITY] [IGNORE]
    [INTO] tbl_name
    [PARTITION (partition_name [, partition_name] ...)]
    [(col_name [, col_name] ...)]
    { SELECT ... 
      | TABLE table_name 
      | VALUES row_constructor_list
    }
    [ON DUPLICATE KEY UPDATE assignment_list]

value:
    {expr | DEFAULT}

value_list:
    value [, value] ...

row_constructor_list:
    ROW(value_list)[, ROW(value_list)][, ...]

assignment:
    col_name = 
          value
        | [row_alias.]col_name
        | [tbl_name.]col_name
        | [row_alias.]col_alias

assignment_list:
    assignment [, assignment] ...
```

* 常规插入数据一条

````mysql
INSERT INTO `table_name` () VALUES();
````

* 常规插入数据多条

```mysql
INSERT INTO `table_name` (a,b,c)
    VALUES(1,2,3), (4,5,6), (7,8,9);
```

* 插入数据从查询

```mysql
insert into `table_name` (a, b, c) select a1, b1, c1 from `table_name1` where id < 10;
```

**更新（update）**

```
UPDATE [LOW_PRIORITY] [IGNORE] table_reference
    SET assignment_list
    [WHERE where_condition]
    [ORDER BY ...]
    [LIMIT row_count]

value:
    {expr | DEFAULT}

assignment:
    col_name = value

assignment_list:
    assignment [, assignment] ...
```

```mysql
update `table_name` set `field` = value;
update `table_name` set `field` = value where id = 1;
update `table_name` set `field` = value where id in (select id from tests where id < 10);
update `table_name`, (select id from tests where id < 10) as t1 set `field` = value where t1.id = table_name.id  ;
```

**删除（delete）**

```
DELETE [LOW_PRIORITY] [QUICK] [IGNORE] FROM tbl_name [[AS] tbl_alias]
    [PARTITION (partition_name [, partition_name] ...)]
    [WHERE where_condition]
    [ORDER BY ...]
    [LIMIT row_count]
```

* 删除表中全部数据

```mysql
delete from `table_name`
```

* 删除表中符合条件记录的

```mysql
delete from `table_name` where `id` < 10 order by `id` desc limit 2;
```

* 多表删除

```mysql
DELETE t1, t2 FROM t1 INNER JOIN t2 INNER JOIN t3 WHERE t1.id=t2.id AND t2.id=t3.id;
```

* 删除全表数据，主键清空，从0开始

```mysql
truncate `table_name`;
```