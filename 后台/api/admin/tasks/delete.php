<?php

// 定义请求方式
$method = "POST";

// 导入公共代码
include '../common/common.php';


// 接收参数
$ids = $inputData['ids'] ?? [];

// 验证数据
if (!is_array($ids) || count($ids) <= 0) {
    $result = [
        'code' => 400,
        'msg' => '参数不能为空',
        'data' => null
    ];
    echo json_encode($result);
    exit();
}

// 移除ids数组中的相同值
$ids = array_unique($ids);

// 校验ids数组中全部为数字
foreach ($ids as $id) {
    if (!is_numeric($id)) {
        $result = [
            'code' => 400,
            'msg' => '参数必须全部为数字',
            'data' => null
        ];
        echo json_encode($result);
        exit();
    }
}


// 引入DB类
include '../common/DB.php';

// 实例化DB
$_DB = new DB($dbConfig);

// 删除数据
$result = $_DB->delete('tasks', [
    'id' => $ids
]);

// 返回结果
if($result > 0) {
    $result = [
        'code' => 200,      
        'msg' => '删除成功',
        'data' => null
    ];
} else {
    $result = [
        'code' => 400,
        'msg' => '删除失败',
        'data' => null
    ];
}
echo json_encode($result);

