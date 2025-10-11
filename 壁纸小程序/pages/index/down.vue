<template>
	<view class="page-continer">
		<image :src="src" mode="aspectFill" class="image"></image>
		<view class="button-group">
			<button class="back-btn" @click="toBack">
				<image src="/static/index/back.svg" mode="aspectFit" class="icon"></image>
				<text>返回</text>
			</button>
			<button class="down-btn" @click="toDown">
				<image src="/static/index/down.svg" mode="aspectFit" class="icon"></image>
				<text>下载</text>
			</button>
			<button class="share-bth" open-type="share">
				<image src="/static/index/share.svg" mode="aspectFit" class="icon"></image>
				<text>分享</text>
			</button>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				src: ''
			}
		},
		onLoad(options) {
			this.src = options.src
		},
		onShareAppMessage() {
			return {
				title: '分享了一张壁纸',
				path: '/pages/index/down?src=' + this.src,
				imageUrl: this.src
			}
		},
		onShareTimeline() {
			return {
				title: '分享了一张壁纸',
				path: '/pages/index/down?src=' + this.src,
				imageUrl: this.src
			}
		},
		methods: {
			toBack() {
				if (getCurrentPages().length > 1) {
					uni.navigateBack()
				}else {
					uni.reLaunch({
						url: "/pages/index/index"
					})
				}
			},
			toDown() {
				uni.downloadFile({
					url: this.src,
					success: (res) => {
						if(res.statusCode == 200) {
							uni.saveImageToPhotosAlbum({
								filePath: res.tempFilePath,
								success: () => {
									uni.showToast({
										title: '下载成功',
										icon: 'success'
									})
								},
								fail: (err) => {
									console.error("保存图片失败：", err)
									uni.showToast({
										title: '保存失败',
										icon: 'error'
									})
								}
							})
						}else {
							// console.error("图片下载服务器异常：", res)
							uni.showToast({
								title: '下载失败',
								icon: 'error'
							})
						}
					},
					fail: (err) => {
						// console.error("下载失败：", err)
						uni.showToast({
							title: '下载失败',
							icon: 'error'
						})
					}
				})
			}
		}
	}
</script>

<style>
	.page-continer {
		position: relative;
		width: 100vw;
		height: 100vh;
		background-color: #000;
	}

	.image {
		width: 100vw;
		height: 100vh;
	}

	.button-group {
		position: absolute;
		bottom: 60rpx;
		left: 10%;
		width: 80%;
		height: 100rpx;
		display: flex;
		justify-content: space-around;
		align-items: center;
		border-radius: 60rpx;
		background-color: rgba(255, 255, 255, 0.6);
		backdrop-filter: blur(5px);
	}

	.back-btn,
	.down-btn,
	.share-bth {
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
		padding: 0;
		width: 80rpx;
		height: 80rpx;
		border-radius: 40rpx;
		background-color: transparent;
	}

	.back-btn::after,
	.down-btn::after,
	.share-bth::after {
		border: none;
	}

	.back-btn .icon,
	.down-btn .icon,
	.share-bth .icon {
		width: 40rpx;
		height: 40rpx;
	}

	.back-btn text,
	.down-btn text,
	.share-bth text {
		height: 40rpx;
		color: #000;
		font-size: 20rpx;
	}
</style>