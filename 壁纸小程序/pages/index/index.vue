<template>
  <view class="container" :style="'background-image: url(' + backgroundUrl + ');'">
    <view class="user-box" @click="goUser" :style="'top: ' + top + 'px; left: ' + left + 'px; height: ' + height + 'px; width: ' + height + 'px;'">
      <image src="/static/index/user.svg"></image>
    </view>
    <view class="search-box">
      <view class="search-content">
        <view class="search-title"><text>搜壁纸</text></view>
        <view class="search-subtitle"><text>好壁纸，一搜就有</text></view>
        <input v-model="text" placeholder="请输入关键词" class="search-input" placeholder-class="search-placeholder" />
        <button class="search-button" @click="handleSearch">搜索</button>
      </view>
    </view>
  </view>
</template>

<script>
  export default {
    data() {
      return {
        backgroundUrl: '/static/bg.jpg',
        text: '',
        top: 20,
        left: 10,
        height: 35,
      };
    },
    onReady() {
			// 获取屏幕宽度
			const screenWidth = uni.getWindowInfo().windowWidth
			// 获取胶囊信息
			const menuButtonInfo = uni.getMenuButtonBoundingClientRect()
			// 计算胶囊右边距
			this.top = menuButtonInfo.top
			this.left = screenWidth - menuButtonInfo.right + 3
			this.height = menuButtonInfo.height
			this.getPhoto();
    },
    methods: {
      getPhoto() {
        uni.request({
          url: 'https://www.duitang.com/napi/blog/list/by_search/?kw=%E6%89%8B%E6%9C%BA%E5%A3%81%E7%BA%B8&start=0',
          method: 'GET',
          success: (res) => {
            const data = res.data;
						// console.log("获取成功");
						// console.log(data);
            if (data.status ===  1) {
              const list = data.data.object_list;
              if (list.length > 0) {
                const item = list[Math.floor(Math.random() * list.length)];
                this.backgroundUrl = item.photo.path;
              }
            }
          },
					fail: (err) => {
						console.error("获取失败：", err)
					}
        });
      },
			goUser() {
				uni.navigateTo({
					url: "/pages/user/user"
				})
			},
      handleSearch() {
				const key = this.text.trim()
				if (key) {
          uni.navigateTo({
            url: '/pages/index/list?key=' + encodeURIComponent(key)
          });
        } else {
          uni.showToast({
            title: '请输入搜索内容',
            icon: 'none'
          });
        }
      }
    }
  };
</script>

<style>
  .container {
    box-sizing: border-box;
    width: 100vw;
    height: 100vh;
    padding: 20rpx;
		background-color: #000;
    background-repeat: no-repeat;
    background-size: cover;
    background-position: bottom;
  }

  .user-box {
    box-sizing: border-box;
    position: fixed;
    padding: 15rpx;
    background-color: rgba(255, 255, 255, 0.7);
    border-radius: 40rpx;
		backdrop-filter: blur(5px);
  }

  .user-box image {
    width: 100%;
    height: 100%;
  }

  .search-box {
    box-sizing: border-box;
    position: fixed;
    bottom: 50rpx;
    left: 3%;
    width: 94%;
    height: 500rpx;
    background-color: rgba(255, 255, 255, 0.7);
		backdrop-filter: blur(5px);
    border-radius: 70rpx;
    padding: 35rpx;
    box-sizing: border-box;
  }

  .search-content {
    box-sizing: border-box;
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
  }

  .search-title,
  .search-subtitle {
    width: 100%;
    text-align: center;
  }

  .search-title text {
    font-size: 40rpx;
    font-weight: bold;
  }

  .search-subtitle {
    margin-top: 15rpx;
  }

  .search-subtitle text {
    font-size: 26rpx;
  }

  .search-input {
    box-sizing: border-box;
    overflow: hidden;
    width: 100%;
    height: 80rpx;
    margin-top: 50rpx;
    padding: 0 20rpx;
    font-size: 32rpx;
    border-radius: 40rpx;
    background-color: rgba(0, 0, 0, 0.6);
    text-align: center;
    color: #fff;
  }

  .search-placeholder {
    color: rgba(255, 255, 255, 0.8);
  }

  .search-button {
    box-sizing: border-box;
    width: 100%;
    height: 80rpx;
    margin-top: 30rpx;
    font-size: 32rpx;
    font-weight: bold;
    color: rgb(255, 255, 255);
    background-color: rgb(0, 0, 0);
    border-radius: 40rpx;
    border: none;
  }
</style>