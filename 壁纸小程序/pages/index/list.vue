<template>
	<view>
		<view class="image-continer">
			<view v-for="(item, index) in list" :key="index" class="image-box" :style="'height:'+(type==1?'var(--photo-height)':'var(--head-height)')+';'">
				<image v-show="item.loading" src="/static/index/loading.gif" mode="aspectFit"></image>
				<image v-show="!item.loading" :lazy-load="true" :src="item.src" mode="aspectFill" :data-index="index"
					@load="imgLoadOk" @click="toDown(item.src)"></image>
			</view>
		</view>
		<view class="toast">
			<text class="toast-text">{{ toastText }}</text>
		</view>
		<view class="go-top" v-show="showGoTop" @click="goTop">
			<image src="/static/index/gotop.svg" mode="aspectFit"></image>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				loading: true,
				more: true,
				type: 1,
				typeText: '壁纸',
				key: '',
				start: 0,
				list: [],
				windowHeight: 0,
				showGoTop: false
			}
		},
		onLoad(options) {
			// 获取传输的关键词
			this.key = options.key
			this.type = options.type
			if(options.type == 3) {
				this.typeText = '表情包'
			}else if(options.type == 2) {
				this.typeText = '头像'
			}else {
				this.typeText = '壁纸'
			}
			uni.setNavigationBarTitle({
				title: decodeURIComponent(this.key) + this.typeText
			})
		},
		onReady() {
			this.getList();
			this.windowHeight = uni.getWindowInfo().windowHeight
		},
		onPageScroll(e) {
			// console.log("滚动事件", e)
			if(e.scrollTop >= this.windowHeight) {
				this.showGoTop = true
			}else {
				this.showGoTop = false
			}
		},
		onReachBottom() {
			if(this.more && !this.loading) {
				this.getList()
			}
		},
		computed: {
			toastText() {
				if (this.more) {
					return '正在加载中'
				}else {
					if(this.list.length > 0) {
						return '没有更多了'
					}else {
						return '没有搜索到内容'
					}
				}
			}
		},
		methods: {
			setToast() {
				this.more = false
				if(this.list.length > 0) {
					this.toast = '没有更多了'
				}else {
					this.toast = '没有搜索到内容'
				}
			},
			getList() {
				this.loading = true
				uni.request({
					url: 'https://www.duitang.com/napi/blog/list/by_search/?kw=' + this.key + decodeURIComponent(this.typeText) + '&start=' + this.start,
					method: 'GET',
					success: (res) => {
						const data = res.data
						if(data.status == 1) {
							this.start = data.data.next_start
							if(data.data.object_list.length < 24) {
								this.more = false
							}
							data.data.object_list.forEach(item => {
								this.list.push({
									loading: true,
									src: item.photo.path
								})
							})
							// console.log("搜索成功：", this.list)
						}else {
							// console.error("搜索失败：", data)
							this.more = false
						}
					},
					fail: (err) => {
						// console.error("搜索失败：", err)
						this.more = false
					},
					complete: () => {
						this.loading = false
					}
				})
			},
			imgLoadOk(e) {
				// console.log("图片加载完成", e.currentTarget.dataset.index)
				this.list[e.currentTarget.dataset.index].loading = false
			},
			toDown(src) {
				uni.navigateTo({
					url: '/pages/index/down?src=' + src + '&type=' + this.type
				})
			},
			goTop() {
				uni.pageScrollTo({
					scrollTop: 0
				})
			}
		}
	}
</script>

<style>
	page {
		--photo-height: calc((100vw - 72rpx) / 3 * 2);
		--head-height: calc((100vw - 72rpx) / 3);
	}
	.toast {
		width: 100%;
		height: 100rpx;
		display: flex;
		justify-content: center;
		align-items: center;
	}

	.toast-text {
		font-size: 25rpx;
		color: rgba(0, 0, 0, .7);
	}

	.image-continer {
		box-sizing: border-box;
		display: grid;
		grid-template-columns: 1fr 1fr 1fr;
		gap: 16rpx;
		padding: 20rpx;
	}

	.image-box {
		width: 100%;
	}

	.image-box image {
		width: 100%;
		height: 100%;
		border-radius: 30rpx;
	}

	.go-top {
		position: fixed;
		bottom: 100rpx;
		right: 50rpx;
		width: 90rpx;
		height: 90rpx;
		border-radius: 50%;
		background-color: rgba(255, 255, 255, 0.7);
		backdrop-filter: blur(5px);
		display: flex;
		justify-content: center;
		align-items: center;
	}

	.go-top image {
		width: 65rpx;
		height: 65rpx;
	}
</style>