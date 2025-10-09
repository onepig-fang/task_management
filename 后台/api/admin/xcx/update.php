<?php

// 定义请求方式
$method = "POST";

// 导入公共代码
include '../common/common.php';


// 接收参数
$id = $inputData['id'] ?? null;
$name = $inputData['name'] ?? null;
$appid = $inputData['appid'] ?? null;
$path = $inputData['path'] ?? null;
$status = $inputData['status'] ?? 1;

// 验证数据不能为空
if (!is_numeric($id) || empty($name) || empty($appid) || empty($path) || !in_array($status, [0, 1])) {
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

// 更新数据
$result = $_DB->update('xcx', [
    'name' => $name,
    'appid' => $appid,
    'path' => $path,
    'status' => $status,
], [
    'id' => $id
]);


// 返回结果
if ($result > 0) {
    $result = [
        'code' => 200,
        'msg' => '修改成功',
        'data' => null
    ];
} else {
    $result = [
        'code' => 400,
        'msg' => '修改失败',
        'data' => null
    ];
}
echo json_encode($result);
