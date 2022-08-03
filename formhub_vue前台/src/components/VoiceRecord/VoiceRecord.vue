<template>
  <div class="container">
    <el-dialog title="提示" :visible.sync="dialogVisible" width="30%">
      <span>是否删除此录音</span>
      <span slot="footer" class="dialog-footer">
        <el-button @click="dialogVisible = false">取 消</el-button>
        <el-button type="primary" @click="handleDelClose()">确 定</el-button>
      </span>
    </el-dialog>

    <div
      v-for="item in voiceList"
      :key="item.stream ? item.stream.id : 0"
      class="voice-list"
    >
      <audio v-if="justPlay" controls :src="getVoiceUrl" style="height: 30px">
        Your browser does not support the
        <code>audio</code> element.
      </audio>
      <el-button type="primary" v-if="!justPlay" plain @click="play(item)"
        >播放{{ item.duration && item.duration.toFixed(0) + "s" }}
      </el-button>
      <el-button
        v-if="!justPlay"
        type="warning"
        @click="handleDelOpen(item)"
        style="margin-left: 10px"
        >删除</el-button
      >
    </div>
    <div
      v-if="!justPlay"
      @mousedown="start"
      @mouseup="end"
      @touchend="end" @touchstart="start"
      class="press"
      style=" "
      v-show="canInputVoice"
    >
      {{ title }}
    </div>
    <!-- 支持绘制波浪图  -->
    <canvas id="canvas"></canvas>
  </div>
</template>

<script>
import Recorder from "js-audio-recorder";
import _ from "loadsh";
import { downfile } from "../api";
export default {
  props: ["getVoice", "justPlay", "voicedown"],
  data() {
    return {
      streamList: [
        {
          stream: {
            id: 1
          },
          duration: 10
        },
        {
          stream: {
            id: 1
          },
          duration: 10
        }
      ],
      ctx: null,
      oCanvas: null,
      // 波浪图-录音
      drawRecordId: null,
      willRemoveRecoardItem: null,
      dialogVisible: false,
      title: "长按录音",
      state: {
        normal: "长按录音",
        during: 0
      },
      voiceList: this.voicedown,
      progressId: 0,
      time: 1,
      timer: {},
      recorder: {}, // 音频对象
      currenPlayTimer: null,
      show: false
    };
  },
  watch: {
    // 如果大于60s 停止录音
    time(val) {
      if (val > 300) {
        this.end();
      }
    }
  },
  computed: {
    getVoiceUrl() {
      return `https://fbapi.aoyixiu.cn/file/id/${this.voiceList[0]}`;
    },
    // 控制最多录制三个
    canInputVoice() {
      return this.voiceList.length <= 2;
    }
  },
  mounted() {
    this.recorder = new Recorder();
    Recorder.getPermission().then(
      () => {},
      error => {
        navigator.mediaDevices
          .getUserMedia({
            audio: true
          })
      }
    );
  },
  methods: {
    handleDelOpen(item) {
      this.dialogVisible = true;
      this.willRemoveRecoardItem = item;
    },
    handleDelClose() {
      this.dialogVisible = false;
      this.delVoice(this.willRemoveRecoardItem);
    },
    // 播放录音
    // 播放录音支持快速点击重复播放同一个录音，支持切换播放
    play(recorder) {
      clearInterval(this.currenPlayTimer);
      for (const item of this.voiceList) {
        item.stopPlay();
        item.percentage = 0;
        this.progressId++;
      }
      // getCurrentPlayTime()是个大坑，不能实时获取
      // 这里用到的两个0.1s延时必要的。否则切换语音播放时计算进度条动画会有问题
      setTimeout(() => {
        const r = _.cloneDeep(recorder);
        recorder.play(); // 播放录音
        // 播放时长
        this.currenPlayTimer = setInterval(() => {
          try {
            //console.log(recorder.percentage);
            const num =
              (r.getPlayTime().toFixed(2) / r.duration.toFixed(2)) * 100;
            if (num < 100) {
              recorder.percentage = num;
            } else {
              recorder.percentage = 100;
            }
            if (r.getPlayTime().toFixed(1) === r.duration.toFixed(1)) {
              clearInterval(this.currenPlayTimer);
            }
          } catch (error) {
            this.currenPlayTimer = null;
          }
          this.progressId++;
        }, 100);
      }, 100);
    },
    // 开始录音
    start() {
      this.recorder = new Recorder();
      this.recorder.start(); // 开始录音
      this.timer = setInterval(() => {
        this.show = true;
        this.state.during = this.time++;
        this.title = this.state.during;
      }, 1000);
    },
    // 停止录音
    end() {
      clearInterval(this.timer);
      this.recorder.stop(); // 停止录音
      this.show = false;
      this.drawRecordId && cancelAnimationFrame(this.drawRecordId);
      this.drawRecordId = null;
      // 如果录音小于1.5秒就提示录音过短，toFixed是四舍五入所以是小于1.5秒
      if (this.recorder.duration.toFixed(0) < 1) {
        this.$message.error("时常过短，请重新录制");
        this.recorder.destroy();
        setTimeout(() => {
          this.time = 1;
          this.title = this.state.normal;
        }, 100);
        return;
      }
      this.recorder.percentage = 0;
      this.voiceList.push(this.recorder);
      this.recorder = new Recorder();
      setTimeout(() => {
        this.time = 1;
        this.title = this.state.normal;
      }, 100);
      this.getVoiceList();
    },
    // 删除录音
    delVoice(item) {
      this.isShowRemoveRecoard = false;
      item.stopPlay();
      this.voiceList = _.remove(this.voiceList, i => {
        return i.stream.id !== item.stream.id;
      });
      this.getVoiceList();
    },
    // 上传录音
    getVoiceList() {
      const urlList = [];
      for (const item of this.voiceList) {
        //console.log(item.duration.toFixed(0))
        const blob = item.getWAVBlob();
        const newbolb = new Blob([blob], {
          type: "audio/wav"
        });
        const fileOfBlob = new File([newbolb], new Date().getTime() + ".wav");
        fileOfBlob.duration = parseInt(item.duration.toFixed(0));
        urlList.push(fileOfBlob);
      }
      // 目前难以区分相关的两段语音 故 强制只能上传一个
      this.getVoice(urlList[0]);
      //console.log(urlList[0])
      return urlList;
    },
    // 清除录音
    clearAll() {
      this.voiceList = [];
    }
  }
};
</script>

<style scoped>
.container {
  user-select: none;
  height: 30px;
  width: 100%;
  display: flex;
  flex-direction: column;
}

.press {
  display: flex;
  height: 40px;
  padding: 10px;
  background-color: #39b54a;
  color: white;
  justify-content: center;
  align-items: center;
}

.voice-list {
  display: flex;
  width: 100%;
  margin-bottom: 15px;
}

.progress {
  width: 100%;
  display: flex;
}
</style>
