<template>
  <div>
    <button @click="btnClick">开始</button>
  </div>
</template>
​
<script>
import { HZRecorder} from "./HZRecorder.js";
export default {
  name: 'HelloWorld',
  props: {
    msg: String
  },
  data() {
    return {
      index:0,
      timeCount:0,
      recorder :HZRecorder,
      audio_context:null,
    };
  },
methods:{
  btnClick: function () {
     this.recorder = new HZRecorder();
    this.recorder.start()
    setInterval(() => {
      var blob = this.recorder.getBlob();
      const file = new File([blob], 'audio.wav');
      console.log("-----------"+ file.size)
      var blob2 = blob.slice(this.index,blob.size -1);
      console.log("----blob2-------" + blob2.size)
      this.index = blob.size -1;
      // this.recorder.start()
      this.timeCount = this.timeCount +1
      if (this.timeCount === 50){
        const blobUrl = window.URL.createObjectURL(blob)
        const a = document.createElement('a')
        a.style.display = 'none'
        a.download = new Date().getTime() +'.wav'
        a.href = blobUrl
        a.click()
      }
    }, 100)
  }
},
  mounted() {
   var that = this
    this.$nextTick(() => {
      try {
        // <!-- 检查是否能够调用麦克风 -->
        window.AudioContext = window.AudioContext || window.webkitAudioContext;
        navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia;
        window.URL = window.URL || window.webkitURL;
        that.audio_context = new AudioContext();
        console.log('navigator.getUserMedia ' + (navigator.getUserMedia ? 'available.' : 'not present!'));
      } catch (e) {
        alert('No web audio support in this browser!');
      }
      navigator.getUserMedia({audio: true}, function (stream) {
        that.recorder = new HZRecorder(stream,{
          sampleBits: 16,
          sampleRate: 8000
        })
        console.log('初始化完成');
      }, function(e) {
        console.log('No live audio input: ' + e);
      });
    })
  },
}
</script>
​
<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
h3 {
  margin: 40px 0 0;
}
ul {
  list-style-type: none;
  padding: 0;
}
li {
  display: inline-block;
  margin: 0 10px;
}
a {
  color: #42b983;
}
</style>