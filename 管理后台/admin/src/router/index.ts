// router/index.js
import { type RouteRecordRaw, createRouter, createWebHistory } from 'vue-router'
import AppLayout from '@/components/layout/AppLayout.vue'

// 导入用户状态
import { useUserStore } from '@/store'


// 定义侧边栏列表
type SiderBarType = {
  show?: boolean,
  aid: number,
  path: string,
  title: string,
  icon?: string,
  file?: string,
  children?: Array<SiderBarType>
}
export const SiderBarList: Array<SiderBarType> = [
  {
    aid: 1,
    path: '/',
    title: "控制台",
    icon: "home",
    file: '@/views/home/index.vue'
  },
  {
    aid: 2,
    path: '/tasks',
    title: "任务管理",
    icon: "layers",
    children: [
      {
        aid: 21,
        path: '/tasks/list',
        title: "任务列表",
        file: '@/views/tasks/list.vue'
      },
      {
        aid: 21,
        show: false,
        path: '/tasks/data/:id',
        title: "任务数据",
        file: '@/views/tasks/data.vue'
      },
      {
        aid: 22,
        path: '/tasks/view',
        title: "观看记录",
        file: '@/views/tasks/view.vue'
      },
    ]
  },
  {
    aid: 3,
    path: '/xcx/list',
    title: "小程序管理",
    icon: "logo-miniprogram",
    file: '@/views/xcx/list.vue'
  },
  {
    aid: 4,
    path: '/domains/list',
    title: "域名管理",
    icon: "link",
    file: '@/views/domains/list.vue'
  }
]

// 将侧边栏列表转为路由
const SiderRouter = () => {
  const viewModules = {
    ...import.meta.glob('@/views/**/*.vue'),
    ...import.meta.glob('/src/views/**/*.vue')
  } as Record<string, () => Promise<any>>;

  const getLoader = (file: string) => {
    const key1 = file;
    const key2 = file.replace(/^@/, '/src');
    return viewModules[key1] || viewModules[key2] || (() => import(/* @vite-ignore */ file));
  };

  const normalizePath = (p?: string) => {
    if (!p) return '';
    return p === '/' ? '' : p.replace(/^\//, '');
  };

  let list: Array<RouteRecordRaw> = []
  SiderBarList.forEach(item => {
    if (item.children) {
      item.children.forEach(child => {
        list.push({
          path: normalizePath(child.path),
          meta: {
            title: child.title,
            aid: child.aid
          },
          component: getLoader(child.file!)
        })
      })
    } else {
      list.push({
        path: normalizePath(item.path),
        meta: {
          title: item.title,
          aid: item.aid
        },
        component: getLoader(item.file!)
      })
    }
  })
  return list
}


const routes = [
  {
    path: '/login',
    component: () => import('@/views/login/index.vue'),
    meta: {
      title: '登录',
      requiresAuth: false
    }
  },
  {
    path: '/',
    component: AppLayout,
    meta: {
      title: '后台',
      requiresAuth: true
    },
    children: SiderRouter()
  },
  // 404页面
  {
    path: '/:pathMatch(.*)*',
    redirect: '/'
  }
]


// 获取当前页面路由
export const getActive = () => {
  const currentRoute = router.currentRoute.value
  return currentRoute
}

const router = createRouter({
  history: createWebHistory(),
  routes
})

// 路由守卫 - 认证检查
router.beforeEach((to, from, next) => {
  const userStore = useUserStore()
  const isAuthenticated = userStore.isLogin

  if (to.meta.title) {
    document.title = `${to.meta.title}`
  }

  if (to.meta.requiresAuth && !isAuthenticated) {
    next('/login')
  } else if (to.path === '/login' && isAuthenticated) {
    next('/')
  } else {
    next()
  }
})

export default router
