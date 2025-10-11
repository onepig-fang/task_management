<?php

// 只允许GET请求
$method = $method ?? "POST";
if ($_SERVER['REQUEST_METHOD'] !== $method) {
    http_response_code(405);
    echo json_encode([
        'code' => 405,
        'msg' => '只允许'.$method.'请求',
        'data' => null
    ]);
    exit();
}

// 引入配置文件
include '../../config.php';

// 引入JWT类
include '../common/JWT.php';

// 获取JWT
$jwtToken = $_SERVER['HTTP_AUTHORIZATION'];
if (empty($jwtToken)) {
    $result = [
        'code' => 401,
        'msg' => '请先登录',
        'data' => null
    ];
    echo json_encode($result);
    exit();
}

// 移除请求头中的 Bearer 前缀
$jwtToken = preg_replace('/^Bearer\s+/i', '', $jwtToken);

// 实例化JWT
$JWT = new JWT($jwtConfig);

// 校验JWT
if($JWT->decode($jwtToken) === false) {
    $result = [
        'code' => 401,
        'msg' => '登录过期',
        'data' => null
    ];
    echo json_encode($result);
    exit();
}

// 校验是否接近有效期
if($JWT->isNearExpiry($jwtToken) === true) {
    // 刷新JWT
    $jwtToken = $JWT->refresh($jwtToken);
    header('Authorization: Bearer ' . $jwtToken);
}

// 重写数据库信息
$dbConfig = [
    'dsn' => 'mysql:host='.$dbConfig['host'].';port='.$dbConfig['port'].';dbname='.$dbConfig['dbname'].';charset='.$dbConfig['charset'],
    'user' => $dbConfig['username'],
    'pass' => $dbConfig['password'],
];

// 获取原始输入并解析JSON
$rawInput = file_get_contents('php://input');
$inputData = json_decode($rawInput, true);