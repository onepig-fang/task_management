<?php

// 定义请求方式
$method = "POST";

// 导入公共代码
include '../common/common.php';

// 获取参数
$task_id = $inputData['id'] ?? 1;
$year = $inputData['year'] ?? date('Y');
$week = $inputData['week'] ?? date('W');

// 验证参数
if (!is_numeric($task_id) || !is_numeric($year) || !is_numeric($week)) {
  $result = [
    'code' => 400,
    'msg' => '参数格式错误',
    'data' => null
  ];
  echo json_encode($result);
  exit();
}

// 转化年份和周数
$year = $year == 0 ? date('Y') : (int)$year;
$week = $week == 0 ? date('W') : (int)$week;


// 引入DB类
include '../common/DB.php';

// 实例化DB
$_DB = new DB($dbConfig);

// 计算指定周的开始和结束日期
$firstDay = strtotime("{$year}-W{$week}-1");
$lastDay = strtotime("{$year}-W{$week}-7");
$week_start = date('Y-m-d', $firstDay);
$week_end = date('Y-m-d', $lastDay);

// 查询指定周内的统计数据
$stats = $_DB->select('task_stats', ['*'], [
  'task_id' => $task_id,
  'created_at >=' => $week_start,
  'created_at <=' => $week_end
], 'created_at DESC');


// 补充缺失的日期
$day_list = [];
$did_list = [];
$view_list = [];
$click_list = [];

// 将统计数据转换为以日期为键的关联数组
$stats_by_date = [];
foreach ($stats as $stat) {
  $stats_by_date[$stat['created_at']] = $stat;
}

// 遍历日期范围，直接通过日期获取数据
for ($date = $week_start; $date <= $week_end; $date = date('Y-m-d', strtotime($date . ' +1 day'))) {
  $day_list[] = $date;
  if (isset($stats_by_date[$date])) {
    $did_list[] = (int)$stats_by_date[$date]['did'];
    $view_list[] = (int)$stats_by_date[$date]['view'];
    $click_list[] = (int)$stats_by_date[$date]['click'];
  } else {
    $did_list[] = 0;
    $view_list[] = 0;
    $click_list[] = 0;
  }
}


// 返回结果
$result = [
  'code' => 200,
  'msg' => '查询成功',
  'data' => [
    'id' => (int)$task_id,
    'year' => (int)$year,
    'week' => (int)$week,
    'day_list' => $day_list,
    'did_list' => $did_list,
    'view_list' => $view_list,
    'click_list' => $click_list,
  ]
];
echo json_encode($result);
