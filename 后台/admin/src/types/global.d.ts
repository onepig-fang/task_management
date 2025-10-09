// 全局类型定义
declare global {
  // 通用对象类型
  type Recordable<T = any> = Record<string, T>;
}

export {};