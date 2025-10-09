<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
// 暴露请求头Authorization
header('Access-Control-Expose-Headers: Authorization');

// 处理预检请求
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 只允许POST请求
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'code' => 405,
        'msg' => '只允许POST请求',
        'data' => null
    ]);
    exit();
}


// 引入配置文件
include '../config.php';

// 引入JWT类
include './common/JWT.php';

// 获取原始输入并解析JSON
$rawInput = file_get_contents('php://input');
$inputData = json_decode($rawInput, true);

// 获取请求的账号和密码
$username = $inputData['username'] ?? null;
$password = $inputData['password'] ?? null;

// 验证账号密码
if($username != $adminConfig['username'] || $password != $adminConfig['password']) {
    $result = [
        'code' => 400,
        'msg' => '用户名或密码错误',
        'data' => null
    ];
    echo json_encode($result);
    exit();
}


// 实例化JWT
$JWT = new JWT($jwtConfig);

// 生成JWT令牌
$userData = [
    'user' => $username
];
$jwtToken = $JWT->encode($userData);

// 添加令牌到响应头
header('Authorization: Bearer ' . $jwtToken);

// 返回结果
$result = [
    'code' => 200,
    'msg' => '登录成功',
    'data' => null
];

echo json_encode($result);