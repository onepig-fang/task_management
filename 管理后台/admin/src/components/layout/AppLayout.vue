<template>
  <t-layout class="hv-100 app-layout">
    <t-aside><app-aside :collapsed="collapsed" /></t-aside>
    <t-layout style="min-width: 760px;">
      <t-header><app-header @onChangeCollapsed="changeCollapsed" /></t-header>
      <t-content class="app-content-box"><app-content /></t-content>
      <t-footer><app-footer /></t-footer>
    </t-layout>
  </t-layout>
</template>

<script
  setup
  lang="ts"
>
  import { ref } from 'vue';

  import AppAside from './AppAside.vue'
  import AppHeader from './AppHeader.vue'
  import AppContent from './AppContent.vue'
  import AppFooter from './AppFooter.vue'

  const collapsed = ref(false);
  const changeCollapsed = () => {
    collapsed.value = !collapsed.value;
  }

</script>

<style
  lang="less"
  scoped
>
  .app-content-box {
    overflow: auto;
    /* 启用滚动功能 */
    -ms-overflow-style: none;
    /* 适用于 Internet Explorer 和旧版 Edge */
    scrollbar-width: none;
    /* 适用于 Firefox */
  }

  /* Webkit 浏览器 */
  .app-content-box::-webkit-scrollbar {
    display: none;
  }
</style>
