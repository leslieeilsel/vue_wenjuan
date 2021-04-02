<!--
程序名：数据分析页面
功能：对问卷调查结果的数据进行分析并用图表可视化展示
-->
<template>
  <div
    id="pdfDom"
    class="Count"
    v-loading="loading"
    element-loading-text="生成中..."
    style="width:100%"
  >
    <div v-if="!(detail.length == 0)" class="opera-buttons">
      <el-button type="primary" size="mini" @click="exportJSON">导出json</el-button>
      <!-- <download-excel class = "export-excel-wrapper" :data = "detail" name = "filename.xls">
          
      </download-excel> -->
      <!-- <el-button type="success" size="mini" @click.native="exportPdf"
        >导出PDF</el-button
      > -->
    </div>
    <div v-if="detail.length == 0">暂时没有数据</div>
    <el-card class="question" :key="index"  v-for="(item, index) in detail">
      <div slot="header" class="clearfix">
        <span>{{ index + 1 + "." + item.qtitle }} 
          <el-tag effect="dark"  type= 'success'>
            填报人数: {{ item.personCount }}人
          </el-tag>
        
           <el-tag effect="dark"  type= 'warning'>
   {{item.qtype=='1'?'单选':(item.qtype=='2'?'多选':'填空')}}
          </el-tag>
          
          </span>
      </div>
      <!--如果数据库中的问题类型为单项选择或者多项选择-->
      <!--则将数据库中的数据以表格、柱状图、饼状图、圆环图、条形图的方式进行展示-->
      <div v-if="item.qtype == '1' || item.qtype == '2'">
        <el-table
          size="small"
          :data="item.options"
          style="width: 100%"
          stripe
          class="table"
        >
          <el-table-column prop="title" label="选项"></el-table-column>
          <el-table-column
            prop="personCount"
            label="数量"
            width="180"
          ></el-table-column>
          
          <el-table-column
            prop="percent"
            label="占比"
            width="180"
          ></el-table-column>
        </el-table>
        <br />

        <el-button
          size="mini"
          type="primary"
          plain
          @click.native="changeValue(index, 1)"
          >柱状图</el-button
        >
        <el-button
          size="mini"
          type="primary"
          plain
          @click.native="changeValue(index, 2)"
          >饼状图</el-button
        >
        <el-button
          size="mini"
          type="primary"
          plain
          @click.native="changeValue(index, 3)"
          >圆环图</el-button
        >
        <el-button
          size="mini"
          type="primary"
          plain
          @click.native="changeValue(index, 4)"
          >条形图</el-button
        >
        <el-button
          size="mini"
          type="primary"
          plain
          @click.native="changeValue(index, 0)"
          >隐藏图表</el-button
        >

        <div :id="'img' + index" class="img" v-show="visible[index] == 1"></div>
        <div
          :id="'bing' + index"
          class="bing"
          v-show="visible[index] == 2"
        ></div>
        <div
          :id="'ring' + index"
          class="ring"
          v-show="visible[index] == 3"
        ></div>
        <div :id="'tz' + index" class="tz" v-show="visible[index] == 4"></div>
      </div>
      <!--如果数据库中的问题类型为text类型则将数据以弹窗表格的形式进行显示-->
      
      <div v-if="item.qtype == '3' && item.content">
       
        <el-tag
    v-for="jdItem,jdIndex in item.content.split('&$%').slice(0,-1)"
    :key="jdIndex"
    effect="dark" style="margin-left:5px">
    {{ jdItem }}
  </el-tag>
      </div>
    </el-card>
    <!-- <el-dialog title="详细内容" :visible.sync="dialogTableVisible">
      <el-table :data="tableData">
        <el-table-column property="context" label="答案"></el-table-column>
      </el-table>
      <el-pagination
        @size-change="sizeChange"
        @current-change="currentChange"
        :current-page.sync="currentPage"
        :page-sizes="[10, 20, 50, 100]"
        :page-size.sync="pageSize"
        layout="total, sizes, prev, pager, next, jumper"
        :total="total"
      >
      </el-pagination>
    </el-dialog> -->
  </div>
</template>
<script>
import FileSaver from 'file-saver'
import echarts from "echarts";
import {getWenjuan} from './api'
export default {
  data() {
    return {
      jsonContent: '',
      dialogTableVisible: false,
      visible: [],
      loading: false,
      detail: [],
      currentPage: 1,
      pageSize: 10,
      total: 0,
      tableData: [],
      questionId: 0,
      wjId: 0,
      exportExcelLoading: false,
      answerText2ExcelQeustionId: 0
    };
  },
  mounted() {
    // this.dataAnalysis()
    // //console.log('数据分析');
  },
  methods: {
    exportJSON () {
      // 将json转换成字符串
      const data = JSON.stringify(this.jsonContent)
      const blob = new Blob([data], {type: ''})
      FileSaver.saveAs(blob, this.jsonContent.title+'.json')
    },

    getPercent(count, total) {
      //console.log("-------");
       //console.log(count, total);
      let res = (count / total).toFixed(2);
     
      return (isNaN(res)?1:res)*100 + '%'; 
    },
    answerText2Excel(questionId) {
      this.answerText2ExcelQeustionId = questionId;
      designOpera({
        opera_type: "answer_text_to_excel",
        questionId: questionId
      }).then(data => {
        this.doDownload(data.b64data, data.filename, "excel");
        this.answerText2ExcelQeustionId = 0;
      });
    },
   
    //切换图表
    changeValue(num, value) {
      this.$set(this.visible, num, value);
      //console.log("num=" + num);
      //console.log("value=" + value);
      if (value == 1) {
        this.setImg(num);
      } else if (value == 2) {
        this.setPar(num);
      } else if (value == 3) {
        this.setRing(num);
      } else if (value == 4) {
        this.setTz(num);
      }
    },
    //      请求后端数据
    dataAnalysis(id) {
      this.loading = true;
      this.detail = [];
      this.wjId = id;
      
      

      getWenjuan({
          id:this.wjId,
        })
          .then(data=>{
          //  data = JSON.parse(data)
            this.jsonContent = data.msg;
            this.detail=data.msg.questions;
            let that = this;
            this.detail.map(function(item,index) {
              item.options.map(function(item2){
                 item2['percent']= that.getPercent(item2.personCount , item.personCount)
                 //console.log( item2['percent']);
                  return item2
              })
              
             return item;
            });
      
            this.visible = [];
        this.loading = false;
          })
      this.dialogShow = false;
    },
    getTitleArr(arr) {
      
      let res = [];
      arr.forEach(item=>{
        res.push(item.title);
      })
      return res;
    },
     getPersonCount(arr) {
      
      let res = [];
      arr.forEach(item=>{
        res.push(item.personCount);
      })
      return res;
    },
   resizeMyChartContainer(f_canvas) {
       if(window.innerWidth<=540) {
          f_canvas.style.height = window.innerHeight * 0.65 + 'px';
        f_canvas.style.width = window.innerWidth * 0.75 + 'px';
        
       }
       return f_canvas
       
      },
    //柱状图
    setImg(id) {
     //console.log(this.detail[id])
      let myChart = echarts.init(this.resizeMyChartContainer(document.getElementById("img" + id)));
      var option = {
        tooltip: {},
        legend: {
          data: ["数量"]
        },
        dataset: {
          dimensions: ["options", "personCount", "percent"],
          source: this.detail[id]
        },
        xAxis: {
          type: "category",
          data: this.getTitleArr(this.detail[id].options)
        },
        yAxis: {
          type: 'value',
          
        },
        series: [
          {
            name: "数量：",
            type: "bar",
            barWidth: 30,
            color: "deepskyblue",
            data: this.getPersonCount(this.detail[id].options)
          }
        ]
      };
      myChart.setOption(option);
    },
    getParOptionData(arr) {
      let res = []
      arr.forEach(item=>{
        //console.log(item);
        res.push(item.title)
      })
      return res
    },
    getParSeriesData(arr) {
      let res = []
      arr.forEach(item=>{
        res.push({value:item.personCount ,name:item.title})
      })
      return res
    },
    // 饼状图
    setPar(id) {
      let myChart = echarts.init(this.resizeMyChartContainer(document.getElementById("bing" + id)));
      var option = {
        tooltip: {},

        color: ["#409EFF", "#FFB54D", "#FF7466", "#44DB5E"],
        legend: {
        orient: 'vertical',
        left: 'left',
        data: this.getParOptionData(this.detail[id].options)
    },
        series: [
          {
            name: "统计结果：",
            type: "pie",
            radius: "55%",
            center: ["50%", "50%"],
            data: this.getParSeriesData(this.detail[id].options),
            itemStyle: {
              emphasis: {
                shadowBlur: 10,
                shadowOffsetX: 0,
                shadowColor: "rgba(0, 0, 0, 0.5)"
              }
            }
          }
        ]
      };
      myChart.setOption(option);
    },
    // 圆环图
    setRing(id) {
      ////console.log(id);
      let myChart = echarts.init(this.resizeMyChartContainer(document.getElementById("ring" + id)));
      var option = {
        tooltip: {},

        color: ["#409EFF", "#FFB54D", "#FF7466", "#44DB5E"],
        legend: {
        orient: 'vertical',
        left: 'left',
        data: this.getParOptionData(this.detail[id].options)
    },
        series: [
          {
            name: "数量：",
            type: "pie",
            radius: ["50%", "70%"],
            avoidLabelOverlap: false,
            data: this.getParSeriesData(this.detail[id].options),
            label: {
              normal: {
                show: false,
                position: "center"
              },
              emphasis: {
                show: true,
                textStyle: {
                  fontSize: "30",
                  fontWeight: "bold"
                }
              }
            },
            labelLine: {
              normal: {
                show: false
              }
            }
          }
        ]
      };
      myChart.setOption(option);
    },
    //圆环图
    setTz(id) {
      ////console.log(id);
      let myChart = echarts.init(this.resizeMyChartContainer(document.getElementById("tz" + id)));
      var option = {
    tooltip: {
        trigger: 'axis',
        axisPointer: {
            type: 'shadow'
        }
    },
    color: ["#409EFF", "#FFB54D", "#FF7466", "#44DB5E"],
    legend: {
        data: ['填报']
    },
    grid: {
        left: '3%',
        right: '4%',
        bottom: '3%',
        containLabel: true
    },
    xAxis: {
        type: 'value',
        boundaryGap: [0, 0.01]
    },
    yAxis: {
        type: 'category',
        data: this.getTitleArr(this.detail[id].options)
    },
    series: [
        {
            name: '填报',
            type: 'bar',
            data: this.getPersonCount(this.detail[id].options)
        }
    ]
};

      myChart.setOption(option);
    },
    //文本内容
    setText(id) {
      return {
        resule: this.detail[id].result
      };
    }
  }
};
</script>
<style scoped>
.Count {
}
.Count .question {
  max-width: 800px;
  width: 80%;
  display: inline-block;
  margin: 5px;
  text-align: left;
}
.Count .table {
}
.Count .img {
  width: 500px;
  height: 300px;
}
.Count .bing {
  width: 500px;
  height: 300px;
}
.Count .ring {
  width: 500px;
  height: 300px;
}
.Count .tz {
  width: 500px;
  height: 300px;
}
.opera-buttons {
  padding: 10px;
}
</style>
