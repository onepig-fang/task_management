<?php

// 定义请求方式
$method = "POST";

// 导入公共代码
include '../common/common.php';


// 接收参数
$xcxName = $inputData['name'] ?? null;
$xcxAppid = $inputData['appid'] ?? null;
$xcxPath = $inputData['path'] ?? null;

// 验证数据不能为空
if (empty($xcxName) || empty($xcxAppid) || empty($xcxPath)) {
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
$id = $_DB->insert('xcx', [
    'name' => $xcxName,
    'appid' => $xcxAppid,
    'path' => $xcxPath,
]);

// 返回结果
if($id !== '0') {
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