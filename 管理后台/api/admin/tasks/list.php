<?php

// 定义请求方式
$method = "GET";

// 导入公共代码
include '../common/common.php';

// 获取参数
$page = $_GET['page'] ?? 1;
$limit = $_GET['size'] ?? 10;

// 验证参数不能为空
if (!is_numeric($page) || !is_numeric($limit)) {
    $result = [
        'code' => 400,
        'msg' => '参数不能为空',
        'data' => null
    ];
    echo json_encode($result);
    exit();
}

// 引入DB类
include '../common/DB.php';

// 实例化DB
$_DB = new DB($dbConfig);

// 计算偏移量
$offset = ($page - 1) * $limit;

// 查询数据
$list = $_DB->select('tasks', ['*'], [], 'created_at DESC', $limit, $offset);
$total = $_DB->count('tasks');


// 返回结果
$result = [
    'code' => 200,
    'msg' => '查询成功',
    'data' => [
        'list' => $list,
        'total' => $total,
        'page' => (int)$page,
        'size' => (int)$limit,
    ]
];
echo json_encode($result);