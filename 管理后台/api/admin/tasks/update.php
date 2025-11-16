<?php

// 定义请求方式
$method = "POST";

// 导入公共代码
include '../common/common.php';


// 接收参数
$id = $inputData['id'] ?? null;
$name = $inputData['name'] ?? null;
$domain = $inputData['domain'] ?? null;
$type = $inputData['type'] ?? null;
$award = $inputData['award'] ?? null;
$click = $inputData['click'] ?? null;
$status = $inputData['status'] ?? null;

// 验证数据不能为空
if (!is_numeric($id) || empty($name) || empty($domain) || !in_array($type, [1, 2, 3]) || empty($award) || !in_array($click, [0, 1]) || !in_array($status, [0, 1])) {
    $result = [
        'code' => 400,
        'msg' => '参数不能为空',
        'data' => null
    ];
    echo json_encode($result);
    exit();
}

// 处理卡密
function linesToPipeStringAdvanced($text, $trimLines = true) {
    // 统一换行符
    $normalizedText = str_replace(["\r\n", "\r"], "\n", $text);
    // 按行分割
    $lines = explode("\n", $normalizedText);
    // 处理每行
    $processedLines = array_map(function ($line) use ($trimLines) {
        return $trimLines ? trim($line) : $line;
    }, $lines);
    // 过滤空行
    $nonEmptyLines = array_filter($processedLines, function ($line) {
        return $line !== '';
    });
    // 用 | 连接
    return implode('|', $nonEmptyLines);
}


if($type == 3) {
    $award = linesToPipeStringAdvanced($award);
}


// 引入DB类
include '../common/DB.php';

// 实例化DB
$_DB = new DB($dbConfig);

// 插入数据
$id = $_DB->update('tasks', [
    'name' => $name,
    'domain' => $domain,
    'type' => $type,
    'award' => $award,
    'click' => $click,
    'status' => $status,
], [
    'id' => $id
]);


// 返回结果
if($id > 0) {
    $result = [
        'code' => 200,
        'msg' => '修改成功',
        'data' => [
            'id' => $id
        ]
    ];
} else {
    $result = [
        'code' => 400,
        'msg' => '修改失败',
        'data' => null
    ];
}
echo json_encode($result);
