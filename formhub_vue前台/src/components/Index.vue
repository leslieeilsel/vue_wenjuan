<!--
程序名：网站首页
功能：对网站进行介绍
-->
<template>
  <div class="main">
    <div class="btn">
      <el-button
        class="toLogin"
        type="primary"
        plain
        style="font-size: 15px;"
        @click="toLogin"
        >登录</el-button
      >
      <el-button
        class="toRegiste"
        plain
        style="font-size: 15px;"
        @click="toRegiste"
        >注册</el-button
      >
    </div>
    <el-carousel :height="autoPageHeight">
      <el-carousel-item v-for="item in 3" :key="item">
        <img
          :src="'../static/images/' + item + '.PNG'"
          style="width:100%;line-height:100%"
        />
      </el-carousel-item>
    </el-carousel>

    <el-footer class="bottom" ref="footer">
      <span class="b">(一)涉及违反宪法所确定的基本原则；</span>
      <span class="b">
        (二)涉及危害国家安全，泄露国家秘密，颠覆国家政权，破坏国家统一；
      </span>
      <span class="b">(三)涉及损害党、国家、政府的荣誉和利益；</span>
      <span class="b">(四)涉及煽动民族仇恨、民族歧视，破坏民族团结；</span>
      <span class="b">(五)涉及破坏国家宗教政策，宣扬邪教和封建迷信；</span>
      <span class="b">(六)散布谣言，扰乱社会秩序，破坏社会稳定；</span>
      <span class="b"
        >(七)散布淫秽、色情、赌博、暴力、凶杀、恐怖或者教唆犯罪；</span
      >
      <span class="b">(八)侮辱或者诽谤他人，侵害他人合法权益；</span>
      <span class="b">(九)含有法律、行政法规禁止的其他内容；</span>
      <span class="b">(十)特殊调查：涉外调查、社会调查</span>
      <span class="b">其他违法行为</span>
      <p>Copyright © 2021 formhub. All rights reserved.</p>
    </el-footer>
  </div>
</template>
<script>
export default {
  data() {
    return {
      fullHeight: document.body.clientWidth,
      timer: false,
      footerH: 0,
      autoPageHeight: "0px"
    };
  },
  mounted() {
    const that = this;
    window.onresize = () => {
      return (() => {
        that.fullHeight = document.documentElement.clientHeight;
      })();
    };
    this.autoPageHeightH();
  },

  watch: {
    fullHeight() {
      if (!this.timer) {
        this.autoPageHeightH();
        this.timer = true;
        let that = this;
        setTimeout(function() {
          that.timer = false;
        }, 400);
      }
    }
  },

  methods: {
    autoPageHeightH() {
      this.$nextTick(() => {
        this.footerH = this.$refs.footer.$el.offsetHeight;
        this.fullHeight = document.body.offsetHeight;
        this.autoPageHeight = this.fullHeight - 60 - this.footerH + "px";
        console.log(this.fullHeight);
      });
    },
    // 跳转登录页面方法
    toLogin() {
      this.$router.push({ path: "/login" });
    },
    // 跳转注册页面方法
    toRegiste() {
      this.$router.push({ path: "/register" });
    }
  }
};
</script>
<style scoped>
.main {
  position: relative;
  width: 100%;
}
.el-carousel__item {
  background-color: #fff;
}
.bottom {
  height: auto !important;
  background-color: #2e3e4e;
  color: #9b9ea0;
  position: relative;
  padding: 10px;
}

.demoImg {
  width: 100%;
  height: 100%;
  /*border-radius: 5px;*/
}
.cover {
  position: absolute;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.6);
  z-index: 100;
  left: 0;
  top: 0;
  color: white;
  font-size: 50px;
  line-height: 60px;
  padding-top: 180px;
}
.el-carousel img {
  height: 100%;
  object-fit: contain;
}
.box-card {
  width: 50%;
  margin: 0 auto;
}
.b {
  font-size: 8px;
}
.b:hover {
  color: red;
  font-size: 16px;
}
.btn {
  display: none;
}
.toLogin,
.toRegiste {
  box-sizing: border-box;
  width: 50%;
  margin: 0;
}
@media screen and (max-width: 540px) {
  .btn {
    display: flex;
  }
  .el-carousel {
    height: auto !important;
  }
}
</style>
