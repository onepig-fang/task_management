<?php

// 定义请求方式
$method = "GET";

// 导入公共代码
include '../common/common.php';

// 引入DB类
include '../common/DB.php';

// 实例化DB
$_DB = new DB($dbConfig);

// 查询数据
$list = $_DB->select('domains', ['url'], ['status' => 1]);


// 返回结果
$result = [
  'code' => 200,
  'msg' => '查询成功',
  'data' => [
    'list' => $list,
  ]
];
echo json_encode($result);
