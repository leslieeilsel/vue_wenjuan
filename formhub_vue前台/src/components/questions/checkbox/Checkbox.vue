<template>
      <!--多选题展示-->
      <el-checkbox-group 
      v-if="item.qtype == '2'" 
      v-model="item.checkboxValue">
        <div
          class="text item"
          v-for="(option, index) in item.options"
          :key="index"
        >
          <el-checkbox
            :label="option.id"
            :key="option.id"
            @change="checkReflash"
            style="margin: 5px;"
            >{{ option.title }}</el-checkbox>
        </div>
      </el-checkbox-group>

</template>
<script>
export default {
    props: {
        'item': Object
    },
    methods: {
        checkReflash(e) {
            this.$forceUpdate();
        },
    }
}
</script>

