import type { LoginParams, LoginResult } from '@/api/model/adminModel';
import { request } from '@/utils/request';

const Api = {
  Login: '/api/admin/login',
};

export function login(data: LoginParams) {
  return request.post<LoginResult>({
    url: Api.Login,
    data
  });
}