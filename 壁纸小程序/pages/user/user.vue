<template>
	<view>
		<view class="card">
			<view>图友</view>
			<view>温山软水繁星千万，都不及你半分眉眼。</view>
		</view>
		<!-- 获取能量 -->
		<view class="power">
			<button class="function" open-type="share">
				<image src="/static/user/invite.svg"></image>
				<view>分享好友</view>
			</button>
			<button class="function" open-type="contact">
				<image src="/static/user/help.svg"></image>
				<view>问题反馈</view>
			</button>
			<view class="function" @click="lookAd">
				<image src="/static/user/video.svg"></image>
				<view>随机壁纸</view>
			</view>
			<view class="function" @click="work">
				<image src="/static/user/work.svg"></image>
				<view>我要合作</view>
			</view>
		</view>
		<view class="powerList" @click="list">
			<image src="/static/user/list.svg"></image>
			<view>常见问题</view>
			<image src="/static/user/right.svg"></image>
		</view>
		<view class="powerList" @click="user">
			<image src="/static/user/about.svg"></image>
			<view>隐私声明</view>
			<image src="/static/user/right.svg"></image>
		</view>
		<view class="powerList" @click="clear">
			<image src="/static/user/clear.svg"></image>
			<view>清理缓存</view>
			<image src="/static/user/right.svg"></image>
		</view>
	</view>
</template>

<script>
	export default {
		onShareAppMessage() {
			return {
				title: "我发现了一款非常好用的壁纸小程序，快来试试",
				path: '/pages/user/user'
			}
		},
		onShareTimeline() {
			return {
				title: "我发现了一款非常好用的壁纸小程序，快来试试",
				path: '/pages/user/user'
			}
		},
		methods: {
			// 观看广告
			lookAd() {
				uni.navigateTo({
					url: "/pages/user/view"
				})
			},
			// 合作
			work() {
				uni.navigateTo({
					url: '/pages/user/work'
				})
			},
			// 常见问题
			list() {
				uni.navigateTo({
					url: "/pages/user/list"
				})
			},
			// 隐私声明
			user() {
				wx.openPrivacyContract({
					fail: () => {
						uni.showToast({
							title: "打开失败，请稍后重试",
							icon: "none"
						})
					},
				})
			},
			// 清理缓存
			clear() {
				uni.clearStorageSync();
				uni.showToast({
					title: "清理成功",
					icon: "success"
				})
			}
		}
	}
</script>

<style>
	page {
		box-sizing: border-box;
		padding: 30rpx 5% 30rpx;
		background-color: #f7f7f7;
	}
	/* 用户信息 */
	.card {
		box-sizing: border-box;
		padding: 50rpx 30rpx;
		border-radius: 30rpx;
		background-color: rgba(255, 255, 255, 0.7);
		backdrop-filter: blur(5px);
		text-align: center;
	}
	.card view:first-child {
		font-size: 40rpx;
		font-weight: bold;
		margin-bottom: 30rpx;
	}
	.card view:last-child {
		font-size: 28rpx;
		color: #676767;
	}
	/* 获取能量 */
	.power {
		margin-top: 20rpx;
		display: flex;
		justify-content: space-between;
	}
	.function {
		margin: 0;
		box-sizing: border-box;
		padding: 50rpx 10rpx;
		width: calc((100% - 60rpx) / 4);
		border-radius: 30rpx;
		background-color: rgba(255, 255, 255, 0.7);
		backdrop-filter: blur(5px);
		text-align: center;
		font-size: 24rpx;
		color: #6a6a6a;
	}
	button {
		border: none;
		line-height: 32rpx;
	}
	button::after {
		content: none;
	}
	.function image {
		margin-bottom: 20rpx;
		width: 50rpx;
		height: 50rpx;
	}
	.function view {
		margin-top: 10rpx;
	}
	.powerList {
		position: relative;
		display: flex;
		box-sizing: border-box;
		margin-top: 20rpx;
		padding: 26rpx 40rpx;
		border-radius: 30rpx;
		background-color: rgba(255, 255, 255, 0.7);
		backdrop-filter: blur(5px);
	}
	.powerList image {
		width: 40rpx;
		height: 40rpx;
	}
	.powerList image:last-child {
		position: absolute;
		right: 20rpx;
	}
	.powerList view {
		padding-left: 30rpx;
		font-size: 30rpx;
	}
	.spgg {
		position: relative;
		top: -10px;
		margin-top: 60rpx;
		width: 100%;
	}
</style>