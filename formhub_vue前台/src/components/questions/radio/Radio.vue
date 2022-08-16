<template>
  <div>
    <div class="text item" v-for="(option, index) in item.options" :key="index">
    <el-radio
        v-model="item.radioValue"
        :label="option.id"
        style="margin: 5px;"
        >{{ option.title }}</el-radio
      >
    </div>
  </div>
</template>
<script>
export default {
    props: {
        'item': Object
    }
}
</script>
