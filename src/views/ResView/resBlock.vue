<template>
  <div :class="classGenerate" v-if="status == 0" @click="resClicked(resId)" class="resBlockCheckedOut res-block">
    <span v-if="startTruncated && isFirst"><b>&lt</b></span>
    <span v-if="isFirst">{{ shortName }}</span>
    <span class="float-right" v-if="endTruncated && isLast"><b>&gt</b></span>
    <span class="sameAsCheckoutBackground">.</span>
  </div>

  <div :class="classGenerate" v-if="status == 1" @click="resClicked(resId)" class="resBlockCheckedIn res-block">
    <span v-if="startTruncated && isFirst"><b>&lt</b></span>
    <span v-if="isFirst">{{ shortName }}</span>
    <span class="float-right" v-if="endTruncated && isLast"><b>&gt</b></span>
    <span class="sameAsCheckinBackground">.</span>
  </div>
</template>

<script>
import _ from 'lodash'
export default {
  props: [ 'isFirst', 'isLast', 'resId', 'start', 'end', 'name', 'span', 'startTruncated', 'endTruncated', 'status' ],
  name: 'resBlock',
  emits: [ 'resBlockClick' ],
  data () {
    return {
      //classGenerate: 'resblock-' + this.resId
    }
  },
  computed: {
    classGenerate () {
      return 'resblock-' + this.resId
    },
    shortName () {
      if( this.name.length > 7) {
        return this.name.substring(0,7) + '...'
      } else { return this.name}
    }
  },
  methods: {
    resClicked ( resId ) {
      this.$emit( 'resBlockClick', resId )
    }
  }
}
</script>

<style scoped>
.float-right {
  float: right;
}
.resBlockCheckedIn {
    background-color: #67C23A;
    color: rgb(14, 14, 14);
    width: 100%;
    padding-right: 4px;
    padding-left: 4px;
    margin-right: 0px;
    margin-left: 0px;
    border-radius: 0px;
    
  }

  .resBlockCheckedOut {
    background-color: rgb(211, 208, 34);
    color: rgb(53, 53, 53);
    width: 100%;
    padding-right: 4px;
    padding-left: 4px;
    margin-right: 0px;
    margin-left: 0px;
    border-radius: 0px;
    
  }

  .resBlockSelected {
    background-color: #F56C6C;
  }

  .sameAsCheckinBackground {
    color: #67C23A;
  }

  .sameAsCheckoutBackground {
    color: rgb(211, 208, 34);
  }


</style>