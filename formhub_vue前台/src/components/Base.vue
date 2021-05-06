<!--
程序名：网站导航栏
功能：网站顶部导航栏
-->
<template>
  <div class="main">
    <el-container>
      <el-header>
        <div class="logo" @click="toIndex">
          <img src="/static/images/logo.png" class="logoImg" />
          <span style="color: #303133">formhub</span>
          <span style="font-size: 13px;margin-left: 5px;color: #606266"
            >——免费的在线问卷调查系统</span
          >
        </div>
        <div
          style="float: right;margin-right: 50px;line-height: 60px;"
          class="headerLogin"
        >
          <!-- 未登录时显示 -->
          <template v-if="!showname && isPC">
            <el-button
              type="primary"
              plain
              style="font-size: 15px;"
              @click="toLogin"
              >登录</el-button
            >
            <el-button plain style="font-size: 15px;" @click="toRegiste"
              >注册</el-button
            >
          </template>
          <!-- 登录时显示 -->
          <template v-if="showname">
            <!-- 登录成功，显示用户名 -->
            <el-dropdown trigger="click" @command="handleCommand">
              <span class="el-dropdown-link">
                {{ username }}<i class="el-icon-arrow-down el-icon--right"></i>
              </span>
              <!-- 退出登录 -->
              <el-dropdown-menu slot="dropdown">
                <el-dropdown-item command="a">问卷管理</el-dropdown-item>
                <el-dropdown-item command="b">退出登录</el-dropdown-item>
              </el-dropdown-menu>
            </el-dropdown>
          </template>
        </div>
      </el-header>
      <el-main style="padding: 0">
        <router-view @state="state" />
      </el-main>
    </el-container>
  </div>
</template>
<script>
export default {
  name: "Base",
  data: function() {
    return {
      isPC: true,
      showname: false, //登录，注册按钮的显示状态
      username: "" //用户名
    };
  },
  mounted() {
    this.state();
    console.log(123);
    if (document.body.clientWidth < 540) {
      this.isPC = false;
    }
  },
  methods: {
    toIndex() {
      this.$router.push({ path: "/index" });
    },
    // 跳转问卷管理页面方法
    toHome() {
      this.$router.push({ path: "/home" });
    },
    // 跳转登录页面方法
    toLogin() {
      this.$router.push({ path: "/login" });
    },
    // 跳转注册页面方法
    toRegiste() {
      this.$router.push({ path: "/register" });
    },
    //判断session中是否存在数据，存在将showname置为true，否则false
    state() {
      let curUid = this.$cookieStore.getCookie("username");

      if (curUid) {
        this.showname = true;
        this.username = curUid;
      } else {
        this.showname = false;
      }
    },
    //下拉菜单操作
    handleCommand(command) {
      if (command == "a") {
        this.toHome();
      } else if (command == "b") {
        this.exit();
      }
    },
    //登出
    exit(command) {
      sessionStorage.clear(); //登出成功，清空session
      this.$cookieStore.delCookie("username"); //删除cookie
      this.$cookieStore.delCookie("uid"); //删除cookie
      this.$cookieStore.delCookie("PHPSESSID"); //删除cookie
      this.state(); // 调用state方法
      this.toLogin(); // 调用toLogin方法
    }
  }
};
</script>
<style scoped>
.main {
  position: absolute;
  width: 100%;
  height: 100%;
}
/* logo图片样式 */
.logoImg {
  width: 30px;
  vertical-align: middle;
}
/* logo框样式 */
.logo {
  height: 60px;
  display: inline-block;
  line-height: 60px;
  font-size: 20px;
  position: absolute;
  left: 100px;
  color: #303133;
  cursor: pointer;
}
.el-header {
  border-bottom: 2px solid #409eff;
  background-color: white;
}
.el-dropdown-link {
  cursor: pointer;
  color: #409eff;
}
.el-icon-arrow-down {
  font-size: 12px;
}
.demonstration {
  display: block;
  color: #8492a6;
  font-size: 14px;
  margin-bottom: 20px;
}

@media screen and (max-width: 540px) {
 
  .el-dropdown-menu {
    left: 50% !important;
    transform: translateX(-50%) !important;
  }
  .headerLogin {
    margin-right: 0 !important;
    float: none !important;
    line-height: 0 !important;
    position: absolute;
    top: 3px;
    /* margin: 0 auto; */
    left: 50%;
    transform: translateX(-50%);
  }
  .logo {
    position: relative;
    left: 0;
  }
}
</style>
