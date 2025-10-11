export interface TodayDataItem {
  id: number;
  task_id: number;
  did: number;
  view: number;
  click: number;
  create_at: string;
}

export interface WeekDataItem {
  did: number;
  view: number;
  click: number;
  view_rate: string;
  click_rate: string;
}


export interface HomeResult {
  task: number[];
  xcx: number[];
  domain: number[];
  ad_view: number[];
  today: TodayDataItem[];
  all_view: number[];
  week: WeekDataItem[];
}