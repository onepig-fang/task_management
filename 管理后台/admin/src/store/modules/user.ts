import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { login } from '@/api/admin';
import type { LoginParams } from '@/api/model/adminModel';
import { parseJwt } from '@/utils/jwt';

// 使用组合式API方式定义store
export const useUserStore = defineStore(
  'user',
  () => {
    // 状态
    const name = ref('');
    const token = ref('');
    const exp = ref(0);

    const isLogin = computed(() => !!token.value && exp.value > Date.now() / 1000);

    // 设置token并从JWT中解析用户信息
    function setToken(tokenValue: string) {
      token.value = tokenValue;

      // 从JWT中解析用户信息
      if (tokenValue) {
        const jwtPayload = parseJwt(tokenValue);
        if (jwtPayload) {
          // 直接设置用户信息
          name.value = jwtPayload.data.user;
          exp.value = jwtPayload.exp;
        }
      } else {
        // 清空
        name.value = '';
        exp.value = 0;
      }
    }

    // 重置状态
    function resetState() {
      name.value = '';
      token.value = '';
      exp.value = 0;
    }

    // 登录
    async function userLogin(params: LoginParams) {
      try {
        // 后端将 JWT 放在响应头 Authorization 中，token 在响应拦截器里统一提取并保存
        await login(params);
        return { name: name.value };
      } catch (error) {
        return Promise.reject(error);
      }
    }

    // 登出 - 直接清除本地用户信息，不发送网络请求
    function logout() {
      resetState();
    }

    return {
      name,
      token,
      exp,
      isLogin,
      setToken,
      resetState,
      login: userLogin,
      logout
    };
  },
  {
    persist: {
      key: 'user',
      storage: localStorage,
      pick: ['token']
    }
  }
);