<template>
  <div @click="emptyBlockClick" :class="{emptyBlockDark: isDark, emptyBlockLight: !isDark}">
    <span>blocked{{resIdRef}}</span>
  </div>
</template>

<script>
//  dark theme
import { useDark } from '@vueuse/core'
export default {
  props: [ 'resIdRef', 'start', 'end', 'span', 'startTruncated', 'blockIndicator' ],
  name: 'emptyBlock',
  emits: [ 'empty-block-click' ],
  data () {
    return {
      isDark: useDark()
    }
  },
  methods: {
    emptyBlockClick () {
      console.log('emptyBlockClick @emptyBlock')
      this.$emit('empty-block-click')
    }
  }
}
</script>

<style>
  .emptyBlockDark {
    background-color: #3d3f3f;
    color: #3d3f3f;
    width: 100%;
    padding-right: 4px;
    padding-left: 4px;
    margin-right: 0px;
    margin-left: 0px;
    border-radius: 0px;
  }

  .emptyBlockLight {
    background-color: #d8e6fa;
    color: #dbe6fa;
    width: 100%;
    padding-right: 4px;
    padding-left: 4px;
    margin-right: 0px;
    margin-left: 0px;
    border-radius: 0px;
  }
</style>