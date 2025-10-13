import type {
  ListTasksParams,
  TasksListResult,
  CreateTaskParams,
  UpdateTaskParams,
  DeleteTasksParams,
  UpdateTaskStatusParams,
  WeekParams,
  WeekResult,
  ViewParams,
  ViewResult,
  DomainResult,
} from '@/api/model/tasksModel';
import { request } from '@/utils/request';

const Api = {
  GetList: '/tasks/list',
  Create: '/tasks/create',
  Update: '/tasks/update',
  Delete: '/tasks/delete',
  Status: '/tasks/status',
  Week: '/tasks/week',
  View: '/tasks/view',
  Domain: '/tasks/domain',
};

export function getTaskList(params: ListTasksParams) {
  return request.get<TasksListResult>({
    url: Api.GetList,
    params,
  });
}

export function createTask(data: CreateTaskParams) {
  return request.post<{ id: number }>({
    url: Api.Create,
    data,
  });
}

export function updateTask(data: UpdateTaskParams) {
  return request.post({
    url: Api.Update,
    data,
  });
}

export function deleteTasks(data: DeleteTasksParams) {
  return request.post({
    url: Api.Delete,
    data,
  });
}

export function updateTaskStatus(data: UpdateTaskStatusParams) {
  return request.post({
    url: Api.Status,
    data,
  });
}

export function getWeekData(data: WeekParams) {
  return request.post<WeekResult>({
    url: Api.Week,
    data,
  });
}

export function getViewList(params: ViewParams) {
  return request.get<ViewResult>({
    url: Api.View,
    params,
  });
}

export function getDomainList() {
  return request.get<DomainResult>({
    url: Api.Domain,
  });
}