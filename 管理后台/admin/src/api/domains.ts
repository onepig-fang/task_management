import type {
  ListDomainParams,
  DomainListResult,
  CreateDomainParams,
  UpdateDomainStatusParams,
  UpdateDomainParams,
  DeleteDomainsParams,
} from '@/api/model/domainsModel';
import { request } from '@/utils/request';

const Api = {
  GetList: '/domains/list',
  Create: '/domains/create',
  Delete: '/domains/delete',
  Status: '/domains/status',
  Update: '/domains/update',
};

export function getDomainList(params: ListDomainParams = {}) {
  return request.get<DomainListResult>({
    url: Api.GetList,
    params,
  });
}

export function createDomain(data: CreateDomainParams) {
  return request.post<{ id: number }>({
    url: Api.Create,
    data,
  });
}

export function deleteDomainsByIds(data: DeleteDomainsParams) {
  return request.post({
    url: Api.Delete,
    data,
  });
}

export function updateDomainStatus(data: UpdateDomainStatusParams) {
  return request.post({
    url: Api.Status,
    data,
  });
}

export function updateDomain(data: UpdateDomainParams) {
  return request.post({
    url: Api.Update,
    data,
  });
}