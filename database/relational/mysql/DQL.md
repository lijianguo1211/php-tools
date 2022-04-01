### 数据查询

**查询（select）**

```
SELECT
    [ALL | DISTINCT | DISTINCTROW ]
    [HIGH_PRIORITY]
    [STRAIGHT_JOIN]
    [SQL_SMALL_RESULT] [SQL_BIG_RESULT] [SQL_BUFFER_RESULT]
    [SQL_NO_CACHE] [SQL_CALC_FOUND_ROWS]
    select_expr [, select_expr] ...
    [into_option]
    [FROM table_references
      [PARTITION partition_list]]
    [WHERE where_condition]
    [GROUP BY {col_name | expr | position}, ... [WITH ROLLUP]]
    [HAVING where_condition]
    [WINDOW window_name AS (window_spec)
        [, window_name AS (window_spec)] ...]
    [ORDER BY {col_name | expr | position}
      [ASC | DESC], ... [WITH ROLLUP]]
    [LIMIT {[offset,] row_count | row_count OFFSET offset}]
    [into_option]
    [FOR {UPDATE | SHARE}
        [OF tbl_name [, tbl_name] ...]
        [NOWAIT | SKIP LOCKED]
      | LOCK IN SHARE MODE]
    [into_option]

into_option: {
    INTO OUTFILE 'file_name'
        [CHARACTER SET charset_name]
        export_options
  | INTO DUMPFILE 'file_name'
  | INTO var_name [, var_name] ...
}
```

* 查询所有字段

```mysql
select * from `table_name`;
```

* 查询某几个字段

```mysql
select `id`, `title` from `table_name`;
```

* 单个`where`查询

```mysql
select `id`, `title` from `table_name` where `id` = 1;
```

* 并行`where and`查询

```mysql
select `id`, `title` from `table_name` where `id` = 1 and `title` = 'ha';
```

* 多个`where or`或者查询

```mysql
select `id`, `title` from `table_name` where `id` = 1 or `id` = 2;
```

* `where in` 查询

```mysql
select `id`, `title` from `table_name` where `id` in (1, 2, 3);
select `id`, `title` from `table_name` where `id` not in (1, 2, 3);
```

* `where in` 子句查询

````mysql
select `id`, `title` from `table_name` where `id` in (select id from tests);
````

* `where exists` 子句查询

````mysql
select `id`, `title` from `table_name` where exists (select id from tests);
````

* `where between` 在...之间

```mysql
select `id`, `title` from `table_name` where `id` between 1 and 5;
select `id`, `title` from `table_name` where `id` not between 1 and 5;
```

* 排序 `order by` 默认是升序 `ASC` 倒叙 `DESC`

```mysql
select * from `table_name` order by `id` asc;
select * from `table_name` order by `id` desc;
```

* 分组 `group by`， 分组结果筛选`having`

```mysql
select score from users GROUP BY score

# 对某一个来源的数据做统计
select count(from_where), from_where from customers group by from_where;

# 对来源的数据大小再次做删选
select count(from_where), from_where from customers group by from_where having count(from_where) > 100;
```

* 关于聚合函数

```mysql
# count 统计总
select COUNT(*) from `users`;

# sum 统计和
select sum(`score`) from `users`;

# avg 平均值
select AVG(`score`) from `users`;

# max 最大值
select max(`score`) from `users`;

# min 最小值
select min(`socre`) from `users`;

# 去重 distinct
select distinct `socre` from `users`;

# 把字段组合为一个字段 字符串 GROUP_CONCAT
select GROUP_CONCAT(id) from users GROUP BY `score`;
# 174,213,284,313,454
# 61,71,115,256,318,323,363,367,394,395,459,475
# 46,99,165

# 把字段组合为一个字段 json JSON_ARRAYAGG
select JSON_ARRAYAGG(id) from users GROUP BY `score`;
# [174, 213, 284, 313, 454]
# [61, 71, 115, 256, 318, 323, 363, 367, 394, 395, 459, 475]
# [46, 99, 165]
 
# 把字段组合为一个字段 json JSON_OBJECTAGG
select JSON_OBJECTAGG(id, title) from users GROUP BY `score`;
# {"36": "violette11", "321": "judson77", "428": "jweimann"}
# {"29": "antone61", "44": "xwest"}
# {"6": "uconnelly", "146": "nsmith", "159": "cebert", "215": "joan87", "247": "xfunk", "287": "kklein", "320": "eliezer12", "349": "wmetz", "437": "antwan33"}

# 8新特性
# 窗口函数 over
# func() over(PARTITION BY 分组 ORDER BY 排序)
```

* 分页 `limit`

```mysql
# 取5条
select * from users limit 5;
# 从第5条以后取10条数据
select * from users limit 5, 10
```

**数据表连接**

* 内连接 `inner join` 两张或多张表一起有的数据才展示

```mysql
SELECT * from users inner join test1_name on users.id = test1_name.key_id;
```

* 左连接 `left join` 左表数据全展示，右表右数据的关联展示，没有数据的以null填充

```mysql
SELECT * from users left join test1_name on users.id = test1_name.key_id;
SELECT * from users left OUTER join test1_name on users.id = test1_name.key_id;
```

* 右连接 `right join` 和左连接相反

```mysql
SELECT * from users right join test1_name on users.id = test1_name.key_id;
SELECT * from users right outer join test1_name on users.id = test1_name.key_id;
```

* 联合两张表 `union | union all`

```mysql
# 重复数据去重
select * from users where id BETWEEN 1 and 10 union select * from users where id BETWEEN 5 and 20;

# 重复数据不会去重
select * from users where id BETWEEN 1 and 10 union all select * from users where id BETWEEN 5 and 20;
```

* `case when` 带条件筛选

```mysql
# 统计 用户当中男女用户的各自数量
SELECT
	count( CASE WHEN sex = 1 THEN id ELSE NULL end ) AS '女',
	count( CASE WHEN sex = 2 THEN id ELSE NULL end) AS '男' 
FROM
	`users`
```
