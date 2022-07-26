<template>
<div class="container">
    <el-dialog title="提示" :visible.sync="dialogVisible" width="30%">
        <span>是否删除此录音</span>
        <span slot="footer" class="dialog-footer">
            <el-button @click="dialogVisible = false">取 消</el-button>
            <el-button type="primary" @click="handleDelClose();">确 定</el-button>
        </span>
    </el-dialog>

    <div v-for="item in voiceList" :key="item.stream.id" class="voice-list">
        <el-button type="primary" plain @click="play(item)">播放{{ item.duration.toFixed(0)}} s</el-button>
        <el-button type="warning" @click="handleDelOpen(item)" style="margin-left: 10px">删除</el-button>
    </div>
    <div @mousedown="start" @mouseup="end" class="press" v-show="canInputVoice">{{ title }}</div>
    <!-- 支持绘制波浪图  -->
    <canvas id="canvas"></canvas>
</div>
</template>

<script>
import Recorder from 'js-audio-recorder'
import _ from 'loadsh'
export default {
    data() {
        return {
            streamList: [{
                stream: {
                    id: 1
                },
                duration: 10
            }, {
                stream: {
                    id: 1
                },
                duration: 10
            }],
            ctx: null,
            oCanvas: null,
            // 波浪图-录音
            drawRecordId: null,
            willRemoveRecoardItem: null,
            dialogVisible: false,
            title: '长按录音',
            state: {
                normal: '长按录音',
                during: 0
            },
            progressId: 0,
            time: 1,
            timer: {},
            voiceList: [], // 录音列表
            recorder: {}, // 音频对象
            currenPlayTimer: null,
            show: false
        }
    },
    watch: {
        // 如果大于60s 停止录音
        time(val) {
            if (val > 60) {
                this.end()
            }
        }
    },
    computed: {
        // 控制最多录制三个
        canInputVoice() {
            return this.voiceList.length <= 2
        }
    },
    mounted() {
        this.recorder = new Recorder()
        Recorder.getPermission().then(() => {}, (error) => {
            navigator.mediaDevices.getUserMedia({
                audio: true
            }).then(stream => {
                console.log("获取语音录制")
            })
            console.log(`${error.name} : ${error.message}`)
        })
        // this.startCanvas()
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
        startCanvas() {
            // 录音波浪
            this.oCanvas = document.getElementById('canvas')
            this.ctx = this.oCanvas.getContext('2d')
        },
        /**
         * 绘制波浪图-录音
         * */
        drawRecord() {
            // 用requestAnimationFrame稳定60fps绘制
            this.drawRecordId = requestAnimationFrame(this.drawRecord)

            // 实时获取音频大小数据
            const dataArray = this.recorder.getRecordAnalyseData()
            const bufferLength = dataArray.length

            // 填充背景色
            this.ctx.fillStyle = 'rgb(200, 200, 200)'
            this.ctx.fillRect(0, 0, this.oCanvas.width, this.oCanvas.height)

            // 设定波形绘制颜色
            this.ctx.lineWidth = 2
            this.ctx.strokeStyle = 'rgb(0, 0, 0)'

            this.ctx.beginPath()

            var sliceWidth = this.oCanvas.width * 1.0 / bufferLength // 一个点占多少位置，共有bufferLength个点要绘制
            var x = 0 // 绘制点的x轴位置

            for (var i = 0; i < bufferLength; i++) {
                var v = dataArray[i] / 128.0
                var y = v * this.oCanvas.height / 2

                if (i === 0) {
                    // 第一个点
                    this.ctx.moveTo(x, y)
                } else {
                    // 剩余的点
                    this.ctx.lineTo(x, y)
                }
                // 依次平移，绘制所有点
                x += sliceWidth
            }

            this.ctx.lineTo(this.oCanvas.width, this.oCanvas.height / 2)
            this.ctx.stroke()
        },
        // 播放录音
        // 播放录音支持快速点击重复播放同一个录音，支持切换播放
        play(recorder) {
            clearInterval(this.currenPlayTimer)
            for (const item of this.voiceList) {
                item.stopPlay()
                item.percentage = 0
                this.progressId++
            }
            // getCurrentPlayTime()是个大坑，不能实时获取
            // 这里用到的两个0.1s延时必要的。否则切换语音播放时计算进度条动画会有问题
            setTimeout(() => {
                console.log("播放")
                const r = _.cloneDeep(recorder)
                recorder.play() // 播放录音
                // 播放时长
                this.currenPlayTimer = setInterval(() => {
                    try {
                        console.log(recorder.percentage);
                        const num = r.getPlayTime().toFixed(2) / r.duration.toFixed(2) * 100
                        if (num < 100) {
                            recorder.percentage = num
                        } else {
                            recorder.percentage = 100
                        }
                        if (r.getPlayTime().toFixed(1) === r.duration.toFixed(1)) {
                            clearInterval(this.currenPlayTimer)
                        }
                    } catch (error) {
                        this.currenPlayTimer = null
                    }
                    this.progressId++
                }, 100)
            }, 100)
        },
        // 开始录音
        start() {
            this.recorder = new Recorder()
            this.recorder.start() // 开始录音
            // this.drawRecord()
            this.timer = setInterval(() => {
                this.show = true
                this.state.during = this.time++
                this.title = this.state.during
            }, 1000)
        },
        // 停止录音
        end() {
            clearInterval(this.timer)
            this.recorder.stop() // 停止录音
            this.show = false
            this.drawRecordId && cancelAnimationFrame(this.drawRecordId)
            this.drawRecordId = null
            // 如果录音小于1.5秒就提示录音过短，toFixed是四舍五入所以是小于1.5秒
            console.log(this.recorder)
            if (this.recorder.duration.toFixed(0) < 1) {
                this.$message.error('时常过短，请重新录制')
                this.recorder.destroy()
                setTimeout(() => {
                    this.time = 1
                    this.title = this.state.normal
                }, 100)
                return
            }
            this.recorder.percentage = 0
            this.voiceList.push(this.recorder)
            console.log(this.voiceList)
            this.recorder = new Recorder()
            setTimeout(() => {
                this.time = 1
                this.title = this.state.normal
            }, 100)
        },
        // 删除录音
        delVoice(item) {
            this.isShowRemoveRecoard = false;
            item.stopPlay()
            this.voiceList = _.remove(this.voiceList, (i) => {
                return i.stream.id !== item.stream.id
            })
        },
        // 上传录音
        getVoiceList() {
            const urlList = []
            for (const item of this.voiceList) {
                console.log(item.duration.toFixed(0))
                const blob = item.getWAVBlob()
                const newbolb = new Blob([blob], {
                    type: 'audio/wav'
                })
                const fileOfBlob = new File([newbolb], new Date().getTime() + '.wav')
                fileOfBlob.duration = parseInt(item.duration.toFixed(0))
                urlList.push(fileOfBlob)
            }
            return urlList
        },
        // 清除录音
        clearAll() {
            this.voiceList = []
        }
    }
}
</script>

<style scoped>
.container {
    margin-top:10px;
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
