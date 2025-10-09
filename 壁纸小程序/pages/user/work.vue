<template>
	<view class="image-box">
		<image :src="src" :show-menu-by-longpress="true" mode="widthFix"></image>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				src: 'https://cdn.x7x.fun/uploads/20251008/d30fa9f92b5a82fc.png'
			}
		},
		onLoad(options) {
			if(options.u) {
				this.src = option.u
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
	.image-box {
		box-sizing: border-box;
		padding: 30rpx;
		position: fixed;
		top: 50%;
		left: 5%;
		transform: translateY(-50%);
		width: 90%;
		min-height: 300rpx;
		border-radius: 20rpx;
		background-color: rgba(255, 255, 255, 0.7);
		backdrop-filter: blur(5px);
	}
	.image-box image {
		width: 100%;
	}
</style>