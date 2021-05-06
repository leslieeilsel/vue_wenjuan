<!--
程序名：网站登录页面
功能：进入网站的入口
-->
<template>
  <div class="login">
    <div class="main_login">
      <div class="title">登 录</div>
      <el-row type="flex" justify="center">
        <!-- 登录表单 -->
        <el-form ref="loginForm" :rules="rules" :model="loginForm">
            <el-form-item prop="username">
                <el-input @keyup.enter.native="login('loginForm')" icon="el-icon-search" placeholder="请输入用户名" v-model="loginForm.username">
                  <i class="el-icon-user" slot="prefix"> </i>
                </el-input>
            </el-form-item>
            <el-form-item prop="password">
                <el-input @keyup.enter.native="login('loginForm')" placeholder="请输入密码" v-model="loginForm.password" show-password>
                  <i class="el-icon-lock" slot="prefix"> </i>
                </el-input>
            </el-form-item>
            
            <!-- <el-form-item prop="captcha">
              <el-input  v-model="loginForm.captcha" autocomplete="off" placeholder="请输入验证码">
              </el-input>
            </el-form-item>
             <el-form-item>
              <img src="" ref="capthcha" @click="getCaptcha()">
            </el-form-item> -->

            <!-- 登录按钮 -->
            <el-form-item>
              <el-button type="primary" @click="login('loginForm')" style="text-align: center;width: 150px">登  录</el-button>
            </el-form-item>
          </el-form>
      </el-row>
      <!-- 注册和忘记密码链接 -->
      <div class="link">
        <el-link type="primary"><router-link to="/register">注册新账号</router-link></el-link>

        <!--<el-link type="primary" :underline="false" href="/resetpass">忘记密码</el-link>-->
      </div>
    </div>
  </div>
</template>
<script>
  import { userLogin, loginStatus} from './api'
export default {
  name: 'Login',
  data() {
    return {
      // 表单数据
      loginForm: {
        username: '',  //用户名
        password: '',  //密码
        captcha: ''
      },
      rules: {
        //表单验证（用户名验证规则）
        username: [
          {required: true,message: '账号不能为空', trigger: 'blur'},
          { min: 8, message: '账号长度最短位', trigger: 'blur' },
          { max: 15, message: '账号长度最长15位', trigger: 'blur' }
        ],
        //表单验证（密码验证规则）
        password: [
          {required: true, message: '密码不能为空', trigger: 'blur'},
          { min: 8, message: '密码长度最少为8位', trigger: 'blur' },
          { max: 15, message: '账号长度最长15位', trigger: 'blur' }
        ],
      },
    }
  },
//页面初始化
  mounted() {
    this.logincheck();
    // this.getCaptcha();
  },
//方法定义
  methods: {
    logincheck(){
        loginStatus({})
      .then(data=>{
 
        if(data.code==500){
          this.$router.push({path:'/login'})
        }else{
          this.$cookieStore.setCookie("uid", data.msg)

        //  sessionStorage.setItem("uid", data.msg)
          this.$router.push({path:'/home'})

        }
      })
    },
    //登录方法
    login(formName) {
      // 表单验证通过，可进行操作
      this.$refs[formName].validate((valid) => {
        if (valid) {
          userLogin({
           
            username:this.loginForm.username,  //用户名
            password:this.loginForm.password,  //密码md5加密
            verify:this.loginForm.captcha,  //密码md5加密
          })
            .then(data=>{
              console.log(data);
              let username = data.msg.displayname;
              let uid = data.msg.id;
              if (data.code==200) {  //登录成功，并提示
                this.$notify({
                  type: 'success',
                  message: '欢迎你,' + username + '!',
                  duration: 3000
                });
                this.$router.push({path:'/home'});  //跳转到用户主页面
                this.$cookieStore.setCookie("username", username)
                this.$cookieStore.setCookie("uid", uid)
                // sessionStorage.setItem("username", username)   //将用户名存入session中
                // sessionStorage.setItem("uid", uid)
                this.$emit("state");  //将状态传到base页面
              }
              else {
                this.$message({
                    type: 'error',
                    message: data.msg,
                    showClose: true
                })
              }
            })
        } else {
          return false;  //表单验证错误返回false
        }
      })
    },
    // //获取验证码
      getCaptcha(){
         this.$refs.capthcha.src = '/api/captcha/id/'+Math.random();
      }
  }
}
</script>

<style scoped>
  /* 主页面样式 */
 
  .login {
    position: absolute;  /*绝对定位*/
    width:100%;
    height:100%;
    background-color: #E4E7ED;
  }
/* 标题样式 */
  .title {
    font-size: large;  /*字体大小*/
    font-weight: bolder;  /*字体加粗*/
    text-align: center;
    color:black;
  }
/* 登录部分div样式 */
  .main_login {
    position: absolute;
    left:50%;
    top:40%;
    width:320px;
    transform: translate(-50%, -50%);
    padding:40px;
    border-radius: 5px;  /*圆角边框*/
    background: #F2F6FC;
  }
  /* 表单样式 */
  .el-form {
    padding-top: 5%;
    padding-left: 10%;
    padding-right: 10%;
    width:280px;
  }
  /* .el-row标签样式 */
  .el-row {
    height: 100%;
    width: 100%;
  }
  /* 文字链接div样式 */
  .link{
    margin-top: 0%;
    text-align: center;   /* 文本居中 */
    margin-left: -5%;
  }
  /* 文字链接样式 */
  .el-link{
    margin-left: 8px;
    line-height: 20px;
    font-size: 8px;
  }
</style>
