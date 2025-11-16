export interface TaskItem {
    id: number;
    name: string;
    domain: string;
    type: 1 | 2;
    award: string;
    click: 0 | 1;
    status: 0 | 1;
    created_at: string;
    updated_at: string;
}

export interface TasksListResult {
  list: TaskItem[];
  total: number;
  page: number;
  size: number;
}

export interface ListTasksParams {
    page?: number;
    size?: number;
}

export interface CreateTaskParams {
    name: string;
    domain: string;
    type: 1 | 2;
    award: string;
    click: 0 | 1;
}

export interface UpdateTaskStatusParams {
  id: number;
  status: 0 | 1;
}

export type UpdateTaskParams = CreateTaskParams & UpdateTaskStatusParams

export interface DeleteTasksParams {
  ids: number[];
}

export interface WeekParams {
  id: number;
  year: number;
  week: number;
}

export interface WeekResult {
  id: number;
  year: number;
  week: number;
  day_list: string[];
  did_list: number[];
  view_list: number[];
  click_list: number[];
}

export interface ViewParams {
  id?: number;
  page: number;
  size: number;
}

export interface ViewItem {
  id: number;
  task_id: number;
  ip: string;
  did: string;
  award_type: 1 | 2 | 3;
  award: string;
  status: 0 | 1 | 2;
  url: string;
  created_at: string;
  completed_at: string;
}

export interface ViewResult {
  list: ViewItem[];
  total: number;
  page: number;
  size: number;
}


export interface DomainItem {
  url: string
}

export interface DomainResult {
  list: DomainItem[];
}