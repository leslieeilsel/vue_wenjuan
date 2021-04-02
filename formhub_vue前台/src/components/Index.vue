<!--
程序名：网站首页
功能：对网站进行介绍
-->
<template>
  <div class="main">
    <el-carousel height="400px">
      <el-carousel-item v-for="item in 3" :key="item">
        <img :src="'../static/images/'+item+'.PNG'" style="width:100%;line-height:100%">
      </el-carousel-item>
    </el-carousel>

  
   
    <h3>用户须知</h3>
    <el-divider></el-divider>
    <el-card class="box-card">
  <div style="text-align: left">
      <p>
        为了规范互联网信息服务活动，促进互联网信息服务健康有序发展，让我们共同打造一个绿色、健康的互联网生态环境，以下内容禁止在本平台发布：
      </p>
      <p class="b">(一)涉及违反宪法所确定的基本原则；</p>
      <p class="b">
        (二)涉及危害国家安全，泄露国家秘密，颠覆国家政权，破坏国家统一；
      </p>
      <p class="b">(三)涉及损害党、国家、政府的荣誉和利益；</p>
      <p class="b">(四)涉及煽动民族仇恨、民族歧视，破坏民族团结；</p>
      <p class="b">(五)涉及破坏国家宗教政策，宣扬邪教和封建迷信；</p>
      <p class="b">(六)散布谣言，扰乱社会秩序，破坏社会稳定；</p>
      <p class="b">(七)散布淫秽、色情、赌博、暴力、凶杀、恐怖或者教唆犯罪；</p>
      <p class="b">(八)侮辱或者诽谤他人，侵害他人合法权益；</p>
      <p class="b">(九)含有法律、行政法规禁止的其他内容；</p>
      <p class="b">(十)特殊调查：涉外调查、社会调查</p>
      <p class="b">其他违法行为</p>
    </div>
  </el-card>
    <el-footer class="bottom">
      <p>Copyright © 2021 formhub. All rights reserved.</p>
    </el-footer>
  </div>
</template>
<script>
export default {
  data() {
    return {
     
    };
  }
};
</script>
<style scoped>
.main {
  position: relative;
  width: 100%;
  height: 100%;
}
 .el-carousel__item {
     background-color: #fff;
  }
.bottom {
  height: 150px !important;
  background-color: #2e3e4e;
  color: #9b9ea0;
  position: relative;
  padding: 20px;
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
.el-carousel img{
  height: 100%;
  object-fit:cover;
}
.box-card{
  width: 50%;
  margin: 0 auto;
}
.b {
  font-weight: 700;
}
</style>
