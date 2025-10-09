// 设备唯一识别码管理函数
export function useDeviceId(): string {
  const STORAGE_KEY = 'device_unique_id'

  // 生成唯一识别码
  const generateUniqueId = (): string => {
    const timestamp = Date.now().toString(36)
    const randomStr = Math.random().toString(36).substr(2, 9)
    const sessionStr = Math.random().toString(36).substr(2, 5)

    // 组合时间戳、随机数和会话标识
    return `${timestamp}-${randomStr}-${sessionStr}`
  }

  // 获取或生成设备ID
  const getDeviceId = (): string => {
    try {
      // 尝试从本地存储读取
      const cachedId = localStorage.getItem(STORAGE_KEY)

      if (cachedId) {
        return cachedId
      }

      // 生成新的唯一识别码
      const newId = generateUniqueId()

      // 缓存到本地存储
      localStorage.setItem(STORAGE_KEY, newId)

      return newId
    } catch (error) {
      // 如果localStorage不可用，返回生成的ID但不缓存
      console.warn('无法访问localStorage，生成临时设备ID:', error)
      return generateUniqueId()
    }
  }

  return getDeviceId()
}
