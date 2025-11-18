<?php

/**
 * 返回JSON格式的响应
 */
function returnJson($code, $msg, $data = null)
{
  $result = [
    'code' => $code,
    'msg' => $msg
  ];

  if ($data !== null) {
    $result['data'] = $data;
  }

  echo json_encode($result);
  exit;
}


/**
 *  获取小程序信息
 */ 
function getAppInfo($db, $appid) {
  $appInfo = $db->select(
    'xcx',
    ['id', 'appid', 'secret'],
    ['appid' => $appid],
    '',
    1
  );
  if (empty($appInfo) || empty($appInfo[0]['appid']) || empty($appInfo[0]['secret'])) {
    returnJson(400, "小程序不存在");
  }
  return $appInfo[0];
}


/**
 * 同时获取微信 access_token 和用户 session 信息
 */
function getSessionList($appid, $secret, $js_code) {
  $urls = [
    "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}",
    "https://api.weixin.qq.com/sns/jscode2session?appid={$appid}&secret={$secret}&js_code={$js_code}&grant_type=authorization_code"
  ];

  // 初始化 cURL Multi
  $mh = curl_multi_init();
  $handles = [];

  foreach ($urls as $url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_multi_add_handle($mh, $ch);
    $handles[] = $ch;
  }

  // 执行请求
  do {
    curl_multi_exec($mh, $running);
    curl_multi_select($mh);
  } while ($running > 0);

  // 获取结果
  $results = [];
  foreach ($handles as $ch) {
    $results[] = json_decode(curl_multi_getcontent($ch), true);
    curl_multi_remove_handle($mh, $ch);
    curl_close($ch);
  }
  curl_multi_close($mh);

  // 验证结果
  if (empty($results[0]['access_token'])) {
    returnJson(400, '获取 access_token 失败');
  }

  if (isset($results[1]['errcode']) && $results[1]['errcode'] != 0) {
    returnJson(400, $results[1]['errmsg'] ?? '获取 session 信息失败');
  }

  return [
    'access_token' => $results[0]['access_token'],
    'openid' => $results[1]['openid'],
    'session_key' => $results[1]['session_key']
  ];
}


/**
 * 获取用户encryptKey
 */
function getSignatureKey($accessToken, $openid, $signature, $version) {
  $keyUrl = "https://api.weixin.qq.com/wxa/business/getuserencryptkey?access_token={$accessToken}&openid={$openid}&signature={$signature}&sig_method=hmac_sha256";
  $keyResponse = file_get_contents($keyUrl);
  $keyData = json_decode($keyResponse, true);
  $errCode = $keyData['errcode'] ?? null;
  if ($errCode != 0 || count($keyData['key_info_list']) == 0) {
    returnJson(400, $keyData['errmsg'] ?? '获取用户密钥失败');
  }
  $encryptkey = "";
  foreach ($keyData['key_info_list'] as $item) {
    if ($item['version'] == $version) {
      $encryptkey = $item['encrypt_key'] ?? "";
      break;
    }
  }
  return $encryptkey;
}