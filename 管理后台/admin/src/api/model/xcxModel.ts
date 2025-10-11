export interface XcxItem {
  id: number;
  name: string;
  appid: string;
  path: string;
  status: 0 | 1;
  created_at: string;
  updated_at: string;
}

export interface XcxListResult {
  list: XcxItem[];
  total: number;
  page: number;
  size: number;
}

export interface ListXcxParams {
  page?: number;
  size?: number;
}

export interface CreateXcxParams {
  name: string;
  appid: string;
  path: string;
}

export interface UpdateXcxStatusParams {
  id: number;
  status: 0 | 1;
}

export type UpdateXcxParams = CreateXcxParams & UpdateXcxStatusParams

export interface DeleteXcxParams {
  ids: number[];
}