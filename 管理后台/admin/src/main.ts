import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import store from '@/store'
import { useUserStore } from '@/store/modules/user'

// 引入TDesign组件库
import TDesign from 'tdesign-vue-next';
import 'tdesign-vue-next/es/style/index.css';
import '@/style/theme.css'
import '@/style/main.css'
import '@/style/layout.less'

const app = createApp(App)

app.use(store)
app.use(router)
app.use(TDesign);

// 在应用启动时恢复 token
const userStore = useUserStore();
if (userStore.token) {
  userStore.setToken(userStore.token);
}

app.mount('#app')
