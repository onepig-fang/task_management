<template>
  <t-card>
    <t-space direction="vertical" size="small" class="w-100">
      <t-typography-title level="h4">任务记录</t-typography-title>
      <t-table :data="list" :columns="columns" row-key="id" :loading="loading" :hover="true" size="medium"
        tableLayout="fixed" style="width: 100%;">
        <template #status="{ row }">
          <t-tag v-if="row.status === 2" shape="round" theme="success" variant="light-outline">
            已点击
          </t-tag>
          <t-tag v-else-if="row.status === 1" shape="round" theme="warning" variant="light-outline">
            已观看
          </t-tag>
          <t-tag v-else shape="round" theme="danger" variant="light-outline">
            未完成
          </t-tag>
        </template>
      </t-table>

      <t-pagination v-model:current="page" v-model:pageSize="size" :total="total" :page-size-options="[10, 20, 50]"
        :disabled="loading" @change="fetchList" />
    </t-space>
  </t-card>
</template>

<script
  setup
  lang="ts"
>
  import { ref, onMounted } from 'vue'
  import type { ViewItem, ViewParams, ViewResult } from '@/api/model/tasksModel'
  import { getViewList } from '@/api/tasks'

  /* 表格配置 */
  const loading = ref(false);
  const list = ref<ViewItem[]>([]);
  const page = ref(1);
  const size = ref(10);
  const total = ref(0);

  const columns = [
    { title: 'ID', colKey: 'id', width: 70, fixed: 'left' },
    { title: '任务ID', colKey: 'task_id', width: 80, fixed: 'left', ellipsis: true },
    { title: '访问IP', colKey: 'ip', width: 140, ellipsis: true },
    { title: '设备识别码', colKey: 'did', width: 180, ellipsis: true },
    { title: '状态', colKey: 'status', width: 100 },
    { title: '创建时间', colKey: 'created_at', width: 200 },
    { title: '完成时间', colKey: 'completed_at', width: 200 },
  ];

  const fetchList = () => {
    loading.value = true;
    getViewList({
      page: page.value,
      size: size.value
    } as ViewParams)
      .then((res: ViewResult) => {
        console.log('浏览历史数据:', res);
        list.value = res.list || [];
        total.value = res.total || 0;
      })
      .catch(error => {
        console.error('获取浏览历史数据失败:', error);
      })
      .finally(() => {
        loading.value = false;
      });
  }

  onMounted(() => {
    fetchList();
  })

</script>