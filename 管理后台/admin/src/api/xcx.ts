import type {
  ListXcxParams,
  XcxListResult,
  CreateXcxParams,
  UpdateXcxParams,
  DeleteXcxParams,
  UpdateXcxStatusParams,
} from '@/api/model/xcxModel';
import { request } from '@/utils/request';

const Api = {
  GetList: '/api/admin/xcx/list',
  Create: '/api/admin/xcx/create',
  Update: '/api/admin/xcx/update',
  Delete: '/api/admin/xcx/delete',
  Status: '/api/admin/xcx/status',
};

export function getXcxList(params: ListXcxParams = {}) {
  return request.get<XcxListResult>({
    url: Api.GetList,
    params,
  });
}

export function createXcx(data: CreateXcxParams) {
  return request.post<{ id: number }>({
    url: Api.Create,
    data,
  });
}

export function updateXcx(data: UpdateXcxParams) {
  return request.post({
    url: Api.Update,
    data,
  });
}

export function deleteXcx(data: DeleteXcxParams) {
  return request.post({
    url: Api.Delete,
    data,
  });
}

export function updateXcxStatus(data: UpdateXcxStatusParams) {
  return request.post({
    url: Api.Status,
    data,
  });
}