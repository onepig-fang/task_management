import type { UserParams, UserListResult, UpdatePointsParams } from '@/api/model/userModel';
import { request } from '@/utils/request';

const Api = {
  GetList: '/api/user/list',
  UpdatePoints: '/api/user/points',
};

export function getUserList(params: UserParams = {}) {
  return request.get<UserListResult>({
    url: Api.GetList,
    params,
  });
}

export function updateUserPoints(data: UpdatePointsParams) {
  return request.post({
    url: Api.UpdatePoints,
    data,
  });
}