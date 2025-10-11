<template>
  <div class="px-xl h-100 h-xxxl">
    <t-row align="middle" justify="end" class="h-100">
      <t-col flex="none">
        <t-button theme="default" shape="square" variant="text" @click="changeCollapsed">
          <t-icon name="view-list" />
        </t-button>
      </t-col>
      <t-col flex="auto"></t-col>
      <t-col flex="none">
        <t-button class="header-user-btn" theme="default" variant="text">
          <template #icon>
            <t-icon class="header-user-avatar" name="user-circle" />
          </template>
          <div class="header-user-account">{{ userStore.name || 'KongHen' }}</div>
        </t-button>
      </t-col>
    </t-row>
  </div>
</template>

<script
  setup
  lang="ts"
>
  import { useUserStore } from '@/store'

  const emit = defineEmits(['onChangeCollapsed']);

  const userStore = useUserStore();

  const changeCollapsed = () => {
    emit('onChangeCollapsed');
  };
</script>

<style scoped></style>