<template>
    <t-menu theme="light" expand-mutex :value="active" :collapsed="props.collapsed">
        <!-- 顶部logo区域 -->
        <template #logo>
            <span class="side-nav-logo-wrapper">
                <logo-icon v-show="props.collapsed" class="side-nav-logo" />
                <full-logo-icon v-show="!props.collapsed" class="side-nav-logo" />
            </span>
        </template>
        <!-- 顶部logo区域结束 -->

        <!-- 侧边栏按钮区域 -->
        <template v-for="item in SiderBarList" :key="item.path">
            <div v-if="item.show !== false">
                <t-menu-item v-if="!item.children || !item.children.length" :value="item.aid" :to="item.path">
                    <template v-if="item.icon" #icon>
                        <t-icon :name="item.icon" />
                    </template>
                    {{ item.title }}
                </t-menu-item>
                <t-submenu v-else :value="item.path" :title="item.title">
                    <template #icon>
                        <t-icon :name="item.icon" />
                    </template>
                    <div v-for="child in item.children" :key="child.path">
                        <t-menu-item v-if="child.show !== false" :value="child.aid" :to="child.path">
                            <template v-if="child.icon" #icon>
                                <t-icon :name="child.icon" />
                            </template>
                            {{ child.title }}
                        </t-menu-item>
                    </div>
                </t-submenu>
            </div>
        </template>
        <!-- 侧边栏按钮区域结束 -->

        <div>
            <t-menu-item @click="handleLogout">
                <template #icon>
                    <t-icon name="poweroff" />
                </template>
                退出登录
            </t-menu-item>
        </div>

    </t-menu>

</template>

<script setup lang="ts">
    import { defineProps, computed } from 'vue'

    import { SiderBarList, getActive } from '@/router/index'

    import { MessagePlugin } from 'tdesign-vue-next';

    import FullLogoIcon from '@/assets/assets-logo-full.svg'
    import LogoIcon from '@/assets/assets-t-logo.svg'

    import { useUserStore } from '@/store'

    import { useRouter } from 'vue-router';

    const router = useRouter();

    const active = computed(() => getActive().meta.aid);

    const props = defineProps({
        collapsed: {
            type: Boolean,
            default: false
        }
    });

    const userStore = useUserStore();
    const handleLogout = () => {
        userStore.logout();
        MessagePlugin.success('退出登录');
        router.replace('/login');
    };
</script>

<style lang="less" scoped>
    .side-nav-logo-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
    }

    .side-nav-logo {
        height: var(--td-comp-size-s);
        width: 100%;
        color: var(--td-text-color-primary);
    }

    .side-footer-version {
        color: var(--td-text-color-primary);
        opacity: .4;
    }
</style>