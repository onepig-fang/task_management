import type { HomeResult } from '@/api/model/homeModel';
import { request } from '@/utils/request';

const Api = {
  home: '/api/admin/home/index',
};

export function getHomeData() {
  return request.get<HomeResult>({
    url: Api.home,
  });
}
