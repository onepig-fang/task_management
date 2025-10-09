export interface DomainItem {
  id: number;
  name: string;
  url: string;
  status: 0 | 1;
  updated_at: string;
  created_at: string;
}

export interface DomainListResult {
  list: DomainItem[];
  total: number;
  page: number;
  size: number;
}

export interface ListDomainParams {
  page?: number;
  size?: number;
}

export interface CreateDomainParams {
  name: string;
  url: string;
}

export interface UpdateDomainStatusParams {
  id: number;
  status: 0 | 1;
}

export type UpdateDomainParams = CreateDomainParams & UpdateDomainStatusParams

export interface DeleteDomainsParams {
  ids: number[];
}