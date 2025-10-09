/**
 * JWT 解析工具
 */

/**
 * 解析 JWT Token
 * @param token JWT token字符串
 * @returns 解析后的payload对象
 */
export function parseJwt(token: string) {
  try {
    const base64Url = token.split('.')[1];
    const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
    const jsonPayload = decodeURIComponent(
      window
        .atob(base64)
        .split('')
        .map(function(c) {
          return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
        })
        .join('')
    );

    return JSON.parse(jsonPayload);
  } catch (e) {
    console.error('JWT解析失败', e);
    return null;
  }
}