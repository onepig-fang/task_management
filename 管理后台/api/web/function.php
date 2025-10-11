<?php

function getClientIP()
{
  $ip = '';

  if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
  } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
  } else {
    $ip = $_SERVER['REMOTE_ADDR'];
  }

  // 处理多个IP
  if (strpos($ip, ',') !== false) {
    $ipList = array_map('trim', explode(',', $ip));
    $ip = $ipList[0];
  }

  // IPv6地址可能包含端口号，需要处理
  if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
    // 如果是IPv6且有端口号，去除端口号
    if (preg_match('/\[([0-9a-fA-F:]+)\]:[0-9]+/', $ip, $matches)) {
      $ip = $matches[1];
    }
  }

  return $ip;
}
