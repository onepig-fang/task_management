<?php

// 定义请求方式
$method = "GET";

// 导入公共代码
include '../common/common.php';

// 引入DB类
include '../common/DB.php';

// 实例化DB
$_DB = new DB($dbConfig);

// 获取在用任务条数
$usingTaskTotal = $_DB->count('tasks', ['status' => 1]);
// 获取禁用任务条数
$disabledTaskTotal = $_DB->count('tasks', ['status' => 0]);

// 获取在用小程序条数
$usingXcxTotal = $_DB->count('xcx', ['status' => 1]);
// 获取禁用小程序条数
$disabledXcxTotal = $_DB->count('xcx', ['status' => 0]);

// 获取在用域名条数
$usingDomainTotal = $_DB->count('domains', ['status' => 1]);
// 获取禁用域名条数
$disabledDomainTotal = $_DB->count('domains', ['status' => 0]);

// 获取今日总广告观看数量
$todayViewTotal = $_DB->sum('task_stats', 'view', ['created_at' => date('Y-m-d')]);


// 获取今日广告观看数量
$todayDataList = $_DB->select('task_stats', ['*'], ['created_at' => date('Y-m-d')], 'did DESC', 5);


// 获取所有广告观看数量
$allDidTotal = $_DB->sum('task_stats', 'did');
$allViewTotal = $_DB->sum('task_stats', 'view');
$allClickTotal = $_DB->sum('task_stats', 'click');


// 生成近7天日期数组
$weekDays = [];
for ($i = 0; $i < 7; $i++) {
  $weekDays[] = date('Y-m-d', strtotime("-$i days"));
}

// 获取近7天所有观看数据
$weekViewList = [];
foreach ($weekDays as $key =>$value) {
  $weekViewList[$key]['date'] = $value;
  $weekViewList[$key]['index'] = $key + 1;
  $weekViewList[$key]['did'] = $_DB->sum('task_stats', 'did', ['created_at' => $value]);
  $weekViewList[$key]['view'] = $_DB->sum('task_stats', 'view', ['created_at' => $value]);
  $weekViewList[$key]['click'] = $_DB->sum('task_stats', 'click', ['created_at' => $value]);
  
  if ($weekViewList[$key]['did'] > 0) {
    $weekViewList[$key]['view_rate'] = round($weekViewList[$key]['view'] / $weekViewList[$key]['did'] * 100, 1) . '%';
  } else {
    $weekViewList[$key]['view_rate'] = '0.0%';
  }

  if ($weekViewList[$key]['view'] > 0) {
    $weekViewList[$key]['click_rate'] = round($weekViewList[$key]['click'] / $weekViewList[$key]['view'] * 100, 1) . '%';
  } else {
    $weekViewList[$key]['click_rate'] = '0.0%';
  }
}


// 返回结果
$result = [
  'code' => 200,
  'msg' => '查询成功',
  'data' => [
    'task' => [$usingTaskTotal, $disabledTaskTotal],
    'xcx' => [$usingXcxTotal, $disabledXcxTotal],
    'domain' => [$usingDomainTotal, $disabledDomainTotal],
    'ad_view' => [$allViewTotal, $todayViewTotal],
    'today' => $todayDataList,
    'all_view' => [$allDidTotal, $allViewTotal, $allClickTotal],
    'week' => $weekViewList
  ]
];
echo json_encode($result);
