<template>
  <div class="prompt-box">
    <div class="icon-wrapper">
        <span class="iconfont icon-yuyin"></span>
        <div class="wave">
            <span></span>
            <span></span>
            <span></span>
            <span></span>
        </div>
     </div>
     <div class="text-wrapper"> 正在录音 松开录音结束 </div>
  </div>
</template>

<script>
export default {
  props: [],
  data() {
    return {};
  },
  mounted() {
  },
  methods: {

  }
};
</script>

<style scoped lang="less">
  .prompt-box {
    position: fixed;
    top: 50%;
    left: 50%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    box-sizing: content-box;
    width: 88px;
    max-width: 70%;
    min-height: 88px;
    padding: 16px;
    color: #fff;
    font-size: 14px;
    line-height: 20px;
    white-space: pre-wrap;
    text-align: center;
    // word-wrap: break-word;
    background-color: rgba(50, 50, 51, 0.88);
    border-radius: 8px;
    transform: translate3d(-50%, -50%, 0);
    .icon-wrapper {
      display: flex;
      flex-direction: row;
      position: relative;
      margin: 0 auto;
      .iconfont {
        font-size: 40px;
        position: relative;
        display: inline-block;
        font-size: inherit;
        text-rendering: auto;
        -webkit-font-smoothing: antialiased;
      }
      .icon-yuyin {
        font-size: 40px;
      }
      .wave {
        padding: 0px;
        margin: 10px;
        span {
          width: 5px;
          height: 5px;
          display: block;
          margin: -9px;
          background: white;
          &:first-child {
            animation: wave 0.4s infinite ease;
          }
          &:nth-child(2) {
            animation: wave 0.5s infinite ease;
          }
          &:nth-child(3) {
            animation: wave 0.6s infinite ease;
          }
          &:nth-child(4) {
            animation: wave 0.8s infinite ease;
          }
        }
        @keyframes wave {
          0%,
          100% {
            width: 3px;
          }
 
          30%,
          70% {
            width: 5px;
          }
 
          60% {
            width: 10px;
          }
        }
      }
    }
 
    .text-wrapper {
      font-size: 14px;
      margin-top: 8px;
    }
  }
</style>
