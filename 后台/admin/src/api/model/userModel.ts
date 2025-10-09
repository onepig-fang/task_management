export interface UserItem {
  id: number;
  uid: string;
  openid: string;
  uionid: string;
  points: number;
  created_at: string;
  last_login_at: string;
}

export interface UserListResult {
  list: UserItem[];
  total: number;
  page: number;
  size: number;
}

export interface UserParams {
  page?: number;
  size?: number;
  keyword?: string;
}

export interface UpdatePointsParams {
  uid: string;
  points: number;
}