/**
 * 程序名：Vue全局配置
 * 功能：Vue全局配置
 */
import Vue from "vue";
import App from "./App";
import router from "./router";
import ElementUI from "element-ui";
import "element-ui/lib/theme-chalk/index.css";
import VueClipboard from "vue-clipboard2";
import md5 from "./md5";
import moment from 'moment'
import * as echarts from "echarts";
import {setCookie,getCookie,delCookie} from '@/assets/cookie'
import './assets/myeleui.css' // 修改eleui样式
Vue.prototype.$cookieStore = {setCookie,getCookie,delCookie}
//定义一个全局过滤器实现日期格式化
Vue.filter('dateformat', function (dataStr, pattern = 'YYYY-MM-DD') {
  if (dataStr) {
    return moment(dataStr).format(pattern)
  } else {
    return dataStr
  }
})


Vue.use(VueClipboard);
Vue.use(ElementUI);
Vue.use(md5);
Vue.use(echarts);
Vue.config.productionTip = false;

/* eslint-disable no-new */
new Vue({
  el: "#app",
  router,
  components: { App },
  template: "<App/>"
});