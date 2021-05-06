<!--
程序名：网站注册页面
功能：网站注册
-->
<template>
  <div class="register">
    <div class="main-register">
      <div class="title">注 册</div>
      <!-- 注册表单 -->
      <el-row>
        <el-form
          :model="registerForm"
          status-icon
          :rules="rules"
          size="medium"
          ref="registerForm"
          label-width="100px"
          class="demo-registeForm"
        >
          <!-- <el-form-item  label="用户名" prop="username">
              <el-input @keyup.enter.native="Register('registerForm')" v-model="registerForm.username" placeholder="请输入用户名(8-15位)"></el-input>
            </el-form-item> -->
          <el-form-item label="用户名" prop="username">
            <el-input
              @keyup.enter.native="Register('registerForm')"
              v-model="registerForm.username"
              autocomplete="off"
              placeholder="请输入密码(8-15位)"
              show-password
            >
            </el-input>
          </el-form-item>
          <el-form-item label="密码" prop="pass">
            <el-input
              @keyup.enter.native="Register('registerForm')"
              v-model="registerForm.pass"
              autocomplete="off"
              placeholder="请输入密码(8-15位)"
              show-password
            >
            </el-input>
          </el-form-item>
          <el-form-item label="确认密码" prop="checkPass">
            <el-input
              @keyup.enter.native="Register('registerForm')"
              v-model="registerForm.checkPass"
              autocomplete="off"
              placeholder="请再次输入密码"
              show-password
            >
            </el-input>
          </el-form-item>
          <!-- <el-form-item label="验证码" prop="captcha">
              <el-input  @keyup.enter.native="Register('registerForm')" v-model="registerForm.captcha" autocomplete="off" placeholder="请输入验证码" show-password>
              </el-input>
            </el-form-item>
             <el-form-item style="margin-left: -25%">
              <img :src="captchaSrc" ref="capthcha" @click="getCaptcha()">
            </el-form-item> -->
          <!-- 注册，重置按钮 -->
          <el-form-item style="margin-left: -25%">
            <!-- 登录页面链接 -->
            <div class="link" style="margin-top: -6px;float:left;">
              <el-link type="primary"
                ><router-link to="/login">已有账号?去登录</router-link></el-link
              >
            </div>
            <el-button type="primary" @click="Register('registerForm')"
              >注册</el-button
            >
            <el-button
              @click="resetForm('registerForm')"
              style="margin-right: -5%"
              >重置</el-button
            >
          </el-form-item>
        </el-form>
      </el-row>
    </div>
  </div>
</template>

<script>
import { isRegister, userAdd } from "./api";
export default {
  name: "Register",
  data() {
    // 检查用户名
    var validateUser = (rule, value, callback) => {
      if (value === "") {
        callback(new Error("请输入用户名"));
      } else {
        if (value.length < 8 || value.length > 15) {
          return callback(new Error("用户名 8-15字"));
        }
        isRegister({ username: value }).then(data => {
          if (data.code === 200) {
            this.$notify({
              title: "成功",
              message: data.msg,
              type: "success"
            });
          } else {
            this.$notify.error({
              title: "错误",
              message: data.msg
            });
            return;
          }
        });
        callback();
      }
    };
    // 检查密码
    var validatePass = (rule, value, callback) => {
      if (value === "") {
        callback(new Error("请输入密码"));
      } else {
        if (this.registerForm.checkPass !== "") {
          this.$refs.registerForm.validateField("checkPass");
        }
        callback();
      }
    };

    // 检查验证码
    var validateCap = (rule, value, callback) => {
      if (value === "") {
        callback(new Error("请输入验证码"));
      } else {
        callback();
      }
    };
    // 确认密码验证
    var validatePass2 = (rule, value, callback) => {
      if (value === "") {
        callback(new Error("请再次输入密码"));
      } else if (value !== this.registerForm.pass) {
        callback(new Error("两次输入密码不一致!"));
      } else {
        callback();
      }
    };
    return {
      // 验证码地址
      captchaSrc: "",
      res: "",
      // 表单数据
      registerForm: {
        username: "", // 用户名
        pass: "", // 密码
        checkPass: "", // 检查密码
        captcha: ""
      },

      // 验证规则
      rules: {
        // 用户名验证规则
        username: [
          { required: true, validator: validateUser, trigger: "blur" }
        ],
        // 密码验证规则
        pass: [
          { required: true, validator: validatePass, trigger: "blur" },
          { min: 8, message: "密码长度最少为8位", trigger: "blur" },
          { max: 15, message: "密码长度不能超过15位", trigger: "blur" }
        ],
        // 检查密码验证规则
        checkPass: [
          { required: true, validator: validatePass2, trigger: "blur" }
        ],
        // 检查验证码
        // 检查密码验证规则
        captcha: [{ required: true, validator: validateCap, trigger: "blur" }]
      }
    };
  },
  // 方法定义
  methods: {
    //获取验证码
    getCaptcha() {
      this.$refs.capthcha.src = "/api/captcha/id/" + Math.random();
    },

    // 注册
    Register(formName) {
      // 表单验证通过，可进行操作
      this.$refs[formName].validate(valid => {
        if (valid) {
          userAdd({
            username: this.registerForm.username, //用户名
            password: this.registerForm.pass, //密码md5加密
            verify: this.registerForm.captcha // 用户验证码
          }).then(data => {
            console.log(data);
            if (data.code == 200) {
              //注册成功
              this.$message({
                message: "注册成功，请登录!",
                type: "success"
              });
              this.$router.push({ path: "/login" });
            } else {
              //注册失败
              this.$message({
                type: "error",
                message: "该用户名已被注册",
                showClose: true
              });
            }
          });
        } else {
          //表单验证失败，返回false
          console.log("error submit!!");
          return false;
        }
      });
    },
    // 表单重置
    resetForm(formName) {
      this.$refs[formName].resetFields();
    }
  }
};
</script>

<style scoped>
/* 注册页面样式 */
.register {
  position: absolute;
  width: 100%;
  height: 100%;
  background-color: #e4e7ed;
}
/* 标题样式 */
.title {
  font-size: large;
  font-weight: bolder;
  text-align: center;
  color: black;
}
/* 注册表单区域样式 */
.main-register {
  position: absolute;
  left: 50%;
  top: 40%;
  width: 320px;
  transform: translate(-50%, -50%);
  padding: 40px;
  border-radius: 5px; /*圆角边框*/
  background: #f2f6fc;
}
/* el-form标签样式 */
.el-form {
  padding-top: 5%;
  padding-right: 10%;
}
/* el-form-item标签样式 */
.el-form-item {
  margin-left: -10%;
}
/* el-row标签样式 */
.el-row {
  height: 100%;
  width: 100%;
}
/* link标签样式 */
.link {
  margin-top: -12%;
  text-align: center;
  margin-left: -5%;
}
/* el-link标签样式 */
.el-link {
  margin-left: 8px;
  line-height: 20px;
  font-size: 8px;
}
</style>
