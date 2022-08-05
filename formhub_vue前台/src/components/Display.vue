<!--
程序名：问卷填写页面
功能：用户打开问卷链接对问卷进行填写
-->
<template>
  <div class="display" v-loading="loading" element-loading-text="加载中...">
    <!--输入验证码弹窗-->
    <el-dialog
      @close="dialogClose"
      :title="dialogTitle"
      :visible.sync="dialogShow"
      :close-on-click-modal="false"
      class="dialog"
    >
      <el-input v-model="code" placeholder="请输入提取码"></el-input>
      <br />
      <br />
      <div style="width: 100%;text-align: right">
        <el-button style="margin-left: 10px;" @click="dialogShow = false"
          >取消</el-button
        >
        <el-button
          type="primary"
          style="margin-left: 10px;"
          @click="checkAddQuestion"
          >完成</el-button
        >
      </div>
    </el-dialog>
    <div class="content">
      <h3>{{ detail.title }}</h3>
      <div class="top" v-if="detail.intro != ''">
        {{ detail.intro }}
      </div>
      <el-card
        class="box-card"
        v-for="(item, index) in detail.questions"
        :key="index"
      >
        <div slot="header" class="clearfix">
          <div class="questionTitle">
            <!--显示必填标识-->
            <span style="color: #F56C6C;">
              <span v-if="item.mustbe == '1'">*</span>
              <span v-else>&nbsp;</span>
            </span>
            {{ index + 1 + "." + item.qtitle }}
          </div>
        </div>

        <!--单选题展示-->
        <div
          class="text item"
          :key="index"
          v-for="(optionItem, index) in item.options"
        >
          <div v-if="item.qtype == '1'">
            <el-radio
              v-model="item.radioValue"
              :label="optionItem.id"
              style="margin: 5px;"
              >{{ optionItem.title }}</el-radio
            >
          </div>
        </div>

        <!--多选题展示-->
        <el-checkbox-group
          v-if="item.qtype == '2'"
          v-model="item.checkboxValue"
        >
          <div
            class="text item"
            :key="index"
            v-for="(optionItem, index) in item.options"
          >
            <el-checkbox
              :label="optionItem.id"
              :key="optionItem.id"
              @change="checkReflash"
              style="margin: 5px;"
              >{{ optionItem.title }}</el-checkbox
            >
          </div>
        </el-checkbox-group>

        <!--填空题展示-->
        <div v-if="item.qtype == '3'">
          <el-input
            :rows="5"
            type="textarea"
            maxlength="140"
            v-model="item.content"
            @input="()=>{inputReflash('voiceRecord' + index)}"
            placeholder="140字最多"
            resize="none"
          >
          </el-input>
          <VoiceRecord :ref="'voiceRecord' + index" :getVoice="recordVoice(item)" :voicedown="[]" />
        </div>
      </el-card>
      <el-button
        type="primary"
        style="margin: 5px;"
        v-show="submitShow"
        @click="submit"
        :loading="submitLoading"
        >{{ submitText }}</el-button
      >

      <div class="bottom">
        <el-link type="info"
          ><router-link to="/index"
            >formhub&nbsp;提供技术支持</router-link
          ></el-link
        >
      </div>
    </div>
  </div>
</template>
<script>
import VoiceRecord from "./VoiceRecord/VoiceRecord.vue";
import { answerOpera, getWenjuan, uploadW, upfile } from "./api";
export default {
  components: {
    VoiceRecord
  },
  data() {
    return {
      mustBeArr: [],
      code: "", //提取码
      loading: true,
      wjId: "",
      dialogShow: false,
      dialogTitle: "",
      dialogType: 1, //1添加 2修改
      oldItem: null, //编辑中问题的对象
      willAddQuestion: {
        type: "",
        title: "",
        options: [""],
        text: "",
        row: 1
      },
      allType: [
        {
          value: "radio",
          label: "单选题"
        },
        {
          value: "checkbox",
          label: "多选题"
        },
        {
          value: "text",
          label: "填空题"
        }
      ],
      title: "",
      desc: "",
      detail: [],
      startTimestamp: 0, //填写问卷开始时间戳 毫秒
      submitLoading: false, //提交按钮 加载中状态
      submitText: "提交", //提交按钮文字
      submitShow: false
    };
  },
  mounted() {
    this.wjId = this.$route.params.id;
    getWenjuan({
      id: this.wjId
    }).then(data => {
      if (data.code == 200) {
        //提交成功
        this.detail = data.msg;
        this.detail.questions.forEach((element, index) => {
          if (element.mustbe == "1") {
            // 加入必选问题id
            this.mustBeArr.push(element.id);
          }
          if (element.qtype == "2") {
            this.detail.questions[index].checkboxValue = [];
          }
        });

        this.loading = false;
        this.submitShow = true;
      } else {
        this.loading = false;
        this.$notify.error({
          title: "错误",
          message: data.msg
        });
        if (data.msg == "请输入正确提取码") {
          (this.dialogTitle = "输入提取码"), (this.dialogShow = true);
        }
      }
    });
  },
  methods: {
    recordVoice(item) {
      return data => {
        item.content = data;
      };
    },
    inputReflash(voiceRecordRef) {
      this.$refs[voiceRecordRef][0].clearAll();
      this.$forceUpdate();
    },
    checkReflash(e) {
      this.$forceUpdate();
    },
    dialogClose() {
      this.dialogShow = false;
    },
    checkAddQuestion() {
      getWenjuan(
        {
          id: this.wjId
        },
        this.code
      ).then(data => {
        if (data.code == 200) {
          //获取成功
          this.detail = data.msg;
          this.detail.questions.forEach((element, index) => {
            if (element.qtype == "2") {
              this.detail.questions[index].checkboxValue = [];
            }
          });
          this.loading = false;
          this.submitShow = true;
          this.dialogShow = false;
        } else {
          this.loading = false;
          this.$notify.error({
            title: "提取码错误",
            message: data.msg
          });
          if (data.msg == "请输入正确提取码") {
            (this.dialogTitle = "输入提取码"), (this.dialogShow = true);
            this.code = "";
          }
        }
      });
    },
    //提交问卷
    async submit() {
      this.submitLoading = true;
      this.submitText = "提交中";
      var wjId = this.$route.params.id;

      let wjquestions = [];
      let wjoptions = [];
      let wjqarr = [];
      for(let item of this.detail.questions){
        // 必选逻辑
        if (item.qtype == "1" && !item.radioValue) {
          break;
        }
        if (
          item.qtype == "2" &&
          (item.checkboxValue.length == 0 || !item.checkboxValue)
        ) {
          break;
        }
        if (item.qtype == "3" && !item.content) {
          break;
        }
        if (item && item.content && item.content.duration) {
          let {data: {msg}} =  await upfile(item.content);
          item.content = "upload" + msg;
        }
        wjquestions.push({ id: item.id, content: item.content });
        wjqarr.push(item.id);
        if (item.qtype == "1") {
          wjoptions.push(item.radioValue);
        } else if (item.qtype == "2") {
          item.checkboxValue.forEach(item2 => {
            wjoptions.push(item2);
          });
        }
      };
      for (let i = 0; i < this.mustBeArr.length; i++) {
        if (wjqarr.indexOf(this.mustBeArr[i]) == -1) {
          this.submitLoading = false;
          this.submitText = "提交";
          this.$notify.error({
            title: "填报未完成",
            message: "请填写完所有必选"
          });
          return;
        }
      }
      uploadW(
        {
          questions: wjquestions,
          options: wjoptions
        },
        wjId,
        this.$cookieStore.getCookie("formhub")
      ).then(data => {
        if (data.code == 200) {
          //提交成功
          this.submitLoading = false;
          this.submitText = "提交成功";
          this.$cookieStore.setCookie("formhub", wjId, 360000); //删除cookie
          this.$router.push({ path: "/thankyou" }); //跳到欢迎页
        } else {
          this.submitLoading = false;
          this.submitText = "提交";
          this.$notify.error({
            title: "提交错误重试",
            message: data.msg
          });
        }
      });
    }
  }
};
</script>
<style scoped>
.display {
  text-align: center;
  padding: 20px;
}
.display .top {
  color: #606266;
  padding: 0 10px 10px 10px;
  border-bottom: 3px solid #409eff;
  font-size: 15px;
  line-height: 22px;
  text-align: left;
}
.display .content {
  width: 100%;
  max-width: 800px;
  display: inline-block;
  text-align: center;
}
.display .box-card {
  text-align: left;
  width: 100%;
  margin: 10px 0 10px 0;
}
.display .bottom {
  margin: 20px 10px 20px 10px;
  color: #909399;
}
.display a:link,
a:visited,
a:active {
  color: #909399;
  text-decoration: none;
}
</style>
