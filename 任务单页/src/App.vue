<template>
  <!-- 加载页 -->
  <div v-if="loading" class="fullpage">
    <t-loading :loading="loading" size="36px" text="加载中" layout="vertical" fullscreen />
  </div>

  <!-- 错误页 -->
  <div v-else-if="error" class="fullpage">
    <t-result theme="error" :title="errMsg" class="error-result" />
  </div>

  <!-- 任务页 -->
  <div v-else class="container">
    <div class="card-box">
      <div class="card">
        <h1 class="task-title">{{ title }}</h1>

        <div class="section">
          <h2 class="section-title">
            <i class="fas fa-tasks"></i>
            任务要求
          </h2>
          <ol class="requirements">
            <li class="requirement">观看广告</li>
            <li v-if="click" class="requirement">点击广告并体验5秒以上</li>
          </ol>
        </div>

        <div class="section">
          <h2 class="section-title">
            <i class="fas fa-graduation-cap"></i>
            任务教程
          </h2>
          <ol class="tutorial-steps">
            <li class="tutorial-step">点击下方"获取奖励"按钮</li>
            <li class="tutorial-step">观看完整广告内容</li>
            <li v-if="click" class="tutorial-step">点击广告并体验5秒以上</li>
            <li class="tutorial-step">返回此页面领取奖励</li>
          </ol>
        </div>

        <div class="section">
          <button class="video-link" @click="openTeach">查看视频教程</button>
        </div>

        <button class="reward-button" @click="isShowDialog = true">获取奖励</button>
      </div>
    </div>
  </div>

  <!-- 奖励弹窗 -->
  <t-dialog v-model:visible="isShowDialog" close-on-overlay-click :content="dialogContent" cancel-btn="取消"
    :confirm-btn="dialogButton" @confirm="getReward"></t-dialog>

  <!-- 展示个体二维码 -->
  <t-popup v-model="showQrcode" placement="center" destroy-on-close style="width: 80%;padding: 20px;">
    <img class="qrcode-image" :src="url" alt="个体二维码" />
    <div class="qrcode-text">请使用微信扫描二维码</div>
  </t-popup>
</template>

<script setup lang="ts">
  import { ref, onMounted, onUnmounted } from 'vue'
  import axios from 'axios'
  import { useDeviceId } from './deviceId'
  import { Message } from 'tdesign-mobile-vue'

  const showMessage = (theme: string, content = '获取成功', duration = 2000) => {
    if (Message[theme]) {
      Message[theme]({
        offset: [10, 16],
        content,
        duration,
        icon: true,
        zIndex: 20000,
      })
    }
  }

  // 后台地址
  const baseURL = 'https://task.dev.xma.run'
  // 视频教程地址
  const videoURL = 'https://www.bilibili.com/bangumi/play/ep733316'



  const loading = ref<boolean>(true)
  const did = useDeviceId()
  const error = ref<boolean>(false)
  const errMsg = ref<string>('')
  const status = ref<boolean>(false)
  const title = ref<string>('任务标题')
  const click = ref<boolean>(true)
  const urlType = ref<string>('url')
  const url = ref<string>('')
  const type = ref<number>(0)
  const award = ref<string>('')

  const isShowDialog = ref<boolean>(false)
  const dialogContent = ref<string>('')
  const dialogButton = ref<object>({ content: '警示操作', theme: 'danger' })

  const showQrcode = ref<boolean>(false)

  const openTeach = () => {
    window.open(videoURL, '_blank')
  }

  // 获取链接中的id参数
  const id = window.location.href.split('id=')[1]

  // 获取链接信息
  const getLinkInfo = () => {
    axios
      .post(baseURL + '/api/web/', {
        id: id,
        did: did,
      })
      .then((res) => {
        console.log(res)
        const data = res.data
        if (data.code == 500) {
          error.value = true
          errMsg.value = data.msg
        } else {
          title.value = data.data.title
          click.value = data.data.click === 1 ? true : false
          if (data.code === 200) {
            status.value = true
            type.value = data.data.type
            award.value = data.data.award
            if (type.value == 1) {
              dialogContent.value = '点击”跳转“按钮自动跳转到奖励地址。'
              dialogButton.value = { content: '跳转', theme: 'primary' }
            } else {
              dialogContent.value = '奖励内容：' + award.value
              dialogButton.value = { content: '复制', theme: 'primary' }
            }
            showMessage('success', data.msg)
            isShowDialog.value = true
          } else if (data.code === 400) {
            urlType.value = data.data.url_type
            url.value = data.data.url
            
            const action = urlType.value === 'image' ? '查看' : '跳转';
            const method = urlType.value === 'image' ? '查看小程序二维码，使用微信扫码' : '跳转到小程序后';
            const extraStep = click.value ? '需要点击广告并体验5秒以上，再' : '';

            dialogContent.value = `点击"${action}"按钮${method}，完整观看广告后，${extraStep}返回即可领取奖励！`;
            dialogButton.value = { content: action, theme: 'primary' };
            showMessage('error', data.msg)
          } else {
            showMessage('error', '未知错误')
          }
        }
      })
      .catch((err) => {
        console.error('网络请求失败：', err)
        error.value = true
        errMsg.value = '请求失败'
      })
      .finally(() => {
        loading.value = false
      })
  }

  const getReward = () => {
    if (status.value) {
      if (type.value == 1) {
        window.location.href = award.value
      } else if (type.value == 2) {
        // 将award的值写入剪切板
        try {
          navigator.clipboard.writeText(award.value)
          showMessage('success', '奖励已复制到剪切板')
        } catch (error) {
          showMessage('error', '复制失败')
        }
      }
    } else {
      if(urlType.value == 'url') {
        window.location.href = url.value
      }else if (urlType.value == 'image') {
        showQrcode.value = true
      }else {
        error.value = true
        errMsg.value = '小程序链接错误'
      }
    }
  }

  const onVisibilityChange = () => {
    if (document.hidden == false) {
      console.log('页面显示')
      if (!loading.value && !error.value && !status.value ) {
        loading.value = true
        status.value = false
        getLinkInfo()
      }
    }
  }

  onMounted(() => {
    // 请求获取任务信息
    getLinkInfo()
    // 监听页面可见性变化
    document.addEventListener('visibilitychange', onVisibilityChange)
  })

  onUnmounted(() => {
    // 移除页面可见性变化事件监听器
    document.removeEventListener('visibilitychange', onVisibilityChange)
  })
</script>

<style scoped>
  .fullpage {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 999;
    width: 100vw;
    height: 100vh;
    background-color: #fff;
  }

  .error-result {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
  }

  .container {
    width: 100vw;
    min-height: 100vh;
    background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
  }

  .card-box {
    width: 100%;
    max-width: 400px;
  }

  .card {
    background: #fff;
    border-radius: 20px;
    padding: 25px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    position: relative;
    overflow: hidden;
  }

  .task-title {
    font-size: 24px;
    font-weight: 600;
    color: #333;
    margin-bottom: 20px;
    text-align: center;
    position: relative;
    padding-bottom: 15px;
  }

  .task-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 25%;
    width: 50%;
    height: 3px;
    background: linear-gradient(to right, #6a11cb, #2575fc);
    border-radius: 3px;
  }

  .section {
    margin-bottom: 25px;
  }

  .section-title {
    font-size: 18px;
    font-weight: 600;
    color: #444;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
  }

  .section-title i {
    margin-right: 8px;
    color: #6a11cb;
  }

  .requirements,
  .tutorial-steps {
    margin-left: 8px;
    padding-left: 20px;
  }

  .requirement::marker,
  .tutorial-step::marker {
    color: #9f64df;
    font-weight: 600;
  }

  .requirement,
  .tutorial-step {
    margin-bottom: 12px;
    padding-left: 3px;
    font-size: 16px;
    color: #555;
  }

  .video-link {
    display: flex;
    align-items: center;
    color: #2575fc;
    text-decoration: none;
    font-size: 14px;
    padding: 10px 15px;
    width: 100%;
    background: rgba(37, 117, 252, 0.1);
    border-radius: 10px;
    justify-content: center;
    border: none;
  }

  .reward-button {
    display: block;
    width: 100%;
    padding: 15px;
    background: linear-gradient(to right, #6a11cb, #2575fc);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 18px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(106, 17, 203, 0.3);
    margin-top: 10px;
  }

  .qrcode-image {
    width: 100%;
  }

  .qrcode-text {
    margin-top: 15px;
    width: 100%;
    color: #000;
    font-size: 16px;
    text-align: center;
  }
</style>
