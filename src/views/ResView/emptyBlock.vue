<template>
  <div @click="emptyBlockClick" class="emptyBlock">
    <span>blocked{{resIdRef}}</span>
  </div>
</template>

<script>
export default {
  props: [ 'resIdRef', 'start', 'end', 'span', 'startTruncated', 'blockIndicator' ],
  name: 'emptyBlock',
  emits: [ 'empty-block-click' ],
  methods: {
    emptyBlockClick () {
      console.log('emptyBlockClick @emptyBlock')
      this.$emit('empty-block-click')
    }
  }
}
</script>

<style>
  .emptyBlock {
    background-color: rgb(194, 194, 194);
    color: rgb(194, 194, 194);
    width: 100%;
    padding-right: 4px;
    padding-left: 4px;
    margin-right: 0px;
    margin-left: 0px;
    border-radius: 0px;
    border-right: 1px solid blue;
  }
</style>