<?php

/**
 * JWT (JSON Web Token) 处理类
 * 提供JWT令牌的签发、验证和刷新功能
 */
class JWT
{
    // 默认JWT密钥
    private $secret = 'KongHen02-q430656781-wKongHen02';
    
    // 默认过期时间（秒）- 12小时
    private $expire_time = 3600 * 12;
    
    // 刷新令牌的时间（秒）- 30分钟内可刷新
    private $refresh_time = 1800;

    /**
     * 构造函数
     * @param array $config JWT配置
     */
    public function __construct($config = [])
    {
        if(isset($config['secret']) && $config['secret']) {
            $this->secret = $config['secret'];
        }
        if(isset($config['expire_time']) && $config['expire_time']) {
            $this->expire_time = $config['expire_time'];
        }
        if(isset($config['refresh_time']) && $config['refresh_time']) {
            $this->refresh_time = $config['refresh_time'];
        }
    }

    /**
     * 签发JWT令牌
     * @param array $payload 用户数据载荷
     * @return string JWT令牌
     */
    public function encode($payload)
    {
        // JWT头部
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256'
        ];

        // JWT载荷
        $now = time();
        $jwt_payload = [
            'iss' => 'KongHen02',         // 签发者
            'iat' => $now,                // 签发时间
            'exp' => $now + $this->expire_time, // 过期时间
            'data' => $payload            // 用户数据
        ];

        // Base64编码头部和载荷
        $header_encoded = $this->base64UrlEncode(json_encode($header));
        $payload_encoded = $this->base64UrlEncode(json_encode($jwt_payload));

        // 创建签名
        $signature = $this->createSignature($header_encoded . '.' . $payload_encoded);

        // 返回完整的JWT
        return $header_encoded . '.' . $payload_encoded . '.' . $signature;
    }

    /**
     * 验证JWT令牌
     * @param string $token JWT令牌
     * @return array|false 验证成功返回载荷数据，失败返回false
     */
    public function decode($token)
    {
        try {
            // 分割JWT
            $parts = explode('.', $token);
            if (count($parts) !== 3) {
                return false;
            }

            list($header_encoded, $payload_encoded, $signature) = $parts;

            // 验证签名
            $expected_signature = $this->createSignature($header_encoded . '.' . $payload_encoded);
            if (!hash_equals($signature, $expected_signature)) {
                return false;
            }

            // 解码载荷
            $payload = json_decode($this->base64UrlDecode($payload_encoded), true);
            if (!$payload) {
                return false;
            }

            // 检查过期时间
            if (isset($payload['exp']) && $payload['exp'] < time()) {
                return false;
            }

            // 检查签发时间
            if (isset($payload['iat']) && $payload['iat'] > time()) {
                return false;
            }

            return $payload;

        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * 刷新JWT令牌
     * @param string $token 原JWT令牌
     * @return string|false 刷新成功返回新令牌，失败返回false
     */
    public function refresh($token)
    {
        // 验证原令牌
        $payload = $this->decode($token);
        if (!$payload) {
            return false;
        }

        // 检查是否在刷新时间窗口内
        $current_time = time();
        $token_exp = $payload['exp'];
        
        // 如果令牌还有很长时间才过期，不允许刷新
        if ($token_exp - $current_time > $this->refresh_time) {
            return false;
        }

        // 提取用户数据
        $user_data = isset($payload['data']) ? $payload['data'] : [];

        // 签发新令牌
        return $this->encode($user_data);
    }

    /**
     * 验证令牌是否即将过期（在刷新窗口内）
     * @param string $token JWT令牌
     * @return bool 即将过期返回true，否则返回false
     */
    public function isNearExpiry($token)
    {
        $payload = $this->decode($token);
        if (!$payload) {
            return false;
        }

        $current_time = time();
        $token_exp = $payload['exp'];
        
        return ($token_exp - $current_time) <= $this->refresh_time;
    }

    /**
     * 创建签名
     * @param string $data 待签名数据
     * @return string 签名
     */
    private function createSignature($data)
    {
        $signature = hash_hmac('sha256', $data, $this->secret, true);
        return $this->base64UrlEncode($signature);
    }

    /**
     * Base64 URL安全编码
     * @param string $data 待编码数据
     * @return string 编码结果
     */
    private function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Base64 URL安全解码
     * @param string $data 待解码数据
     * @return string 解码结果
     */
    private function base64UrlDecode($data)
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
}