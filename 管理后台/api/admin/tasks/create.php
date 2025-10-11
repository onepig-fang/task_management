<?php

// 定义请求方式
$method = "POST";

// 导入公共代码
include '../common/common.php';


// 接收参数
$name = $inputData['name'] ?? null;
$domain = $inputData['domain'] ?? null;
$type = $inputData['type'] ?? null;
$award = $inputData['award'] ?? null;
$click = $inputData['click'] ?? null;

// 验证数据不能为空
if (empty($name) || empty($domain) || !is_numeric($type) || !in_array($type, [1, 2]) || empty($award) || !in_array($click, [0, 1])) {
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

// 插入数据
$id = $_DB->insert('tasks', [
    'name' => $name,
    'domain' => $domain,
    'type' => $type,
    'award' => $award,
    'click' => $click,
]);


// 返回结果
if($id > 0) {
    $result = [
        'code' => 200,
        'msg' => '添加成功',
        'data' => [
            'id' => $id
        ]
    ];
} else {
    $result = [
        'code' => 400,
        'msg' => '添加失败',
        'data' => null
    ];
}
echo json_encode($result);
