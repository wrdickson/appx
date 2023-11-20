<template>
  <el-table
    ref="resViewTable"
    :data="tableData"
    :row-style="rowStyle"
    :height="tableHeight"
    row-class-name="resview-row"
    cell-class-name="resview-table-cell"
    header-cell-class-name="resview-header-cell"
    header-row-class-name="resview-header-row"
    style="width: 100%; padding: 0px"
    @cell-click="cellClick"
    @empty-block-click="emptyBlockClick">

    <el-table-column fixed prop="title" :label="spaceLabel" width="120">
    </el-table-column>

    <el-table-column prop="expand" fixed width="27">
      <template #default="scope">
        <div data-rel-day="0" style="display: flex; align-items: center;">
          <c1
            :spaceId="scope.row.id"
            :showChildren="scope.row.showChildren"
            :children="scope.row.children"
            @c1-toggle-show-children="c1ToggleShowChildren"
            ></c1>
        </div>
      </template>
    </el-table-column>
    <template v-for="day in tDateArray">
      <el-table-column :prop=day.dayString :label=day.dayLabel width="90">
      <template #default="scope">
        <div style="display: flex; align-items: center;">
          <resBlock
            @resBlockClick="resBlockClick"
            v-if="scope.row[day.dayString + 'resid']"
            :resId="scope.row[day.dayString + 'resid']"
            :start="scope.row[day.dayString + 'start']"
            :end="scope.row[day.dayString + 'end']"
            :name="scope.row[day.dayString + 'customer']"
            :span="scope.row[day.dayString + 'span']"
            :startTruncated="scope.row[day.dayString + 'starttruncated']"
            :endTruncated="scope.row[day.dayString + 'endtruncated']"
            :isFirst = "scope.row[day.dayString + 'isfirst']"
            :isLast = "scope.row[day.dayString + 'islast']"
            :status = "scope.row[day.dayString + 'status']"
          />
          <emptyBlock
            @emptyBlockClick="emptyBlockClick"
            v-if="scope.row[day.dayString + 'blocked']"
            :resIdRef="scope.row[day.dayString + 'resIdRef']"
            :start="scope.row[day.dayString + 'start']"
            :end="scope.row[day.dayString + 'end']"
            :span="scope.row[day.dayString + 'span']"
            :startTruncated="scope.row[day.dayString + 'starttruncated']"
            :endTruncated="scope.row[day.dayString + 'endtruncated']"
          />
          <unassignedBlock
            @unassigned-block-select-res="unassignedResSelect"
            v-if="scope.row[day.dayString + 'unassigned']"
            :unassignedResArr="scope.row[day.dayString + 'unassigned']"
          />
        </div>
      </template>
      </el-table-column>
    </template>
  </el-table>

</template>

<script lang="js">
import resBlock from './resBlock.vue'
import emptyBlock from './emptyBlock.vue'
import unassignedBlock from './unassignedBlock.vue'
import { resViewStore } from '@/stores/resViewStore.js'
import c1 from './C1.vue'
import dayjs from 'dayjs'
import _ from 'lodash'
export default {
  name: 'ResViewTable',
  components: { c1, resBlock, emptyBlock, unassignedBlock },
  emits: [ 'resview-toggle-show-children', 'resBlockClick', 'emptyBlockClick', 'emptyCellClick', 'unassigned-res-selected' ],
  props: [
    'tDateArray',
    'tableData',
    'trigger',
    'resSpaceCopy',
    'tableHeight'
  ],
  data () {
    return {
    }
  },
  computed: {
    spaceLabel() {
      return this.$t('message.spaceLabel')
    }
  },
  methods: {
    setInitialScroll () {
      //  check to see if we have scroll postion in store
      if( resViewStore().scrollTop > 0 ) {
        this.setScrollTop(resViewStore().scrollTop)
      }
      if (resViewStore().scrollLeft > 0 ) {
        this.setScrollLeft(resViewStore().scrollLeft)
      }
    },
    setScrollLeft ( leftS ) {
      this.$refs.resViewTable.$refs.scrollBarRef.setScrollLeft(leftS)
    },
    setScrollTop ( topS ) {
      this.$refs.resViewTable.$refs.scrollBarRef.setScrollTop(topS)
    },
    c1ToggleShowChildren ( spaceId ) {
      console.log('resViewTable toggle', spaceId)
      //  tell resView to toggle this
      this.$emit('resview-toggle-show-children', spaceId)
    },
    cellClick ( row, column ) {
      //  get the 'prop' of the colun
      let colProp = column.property
      //  if there's no res data from that column, emit the click
      let resIdKey = colProp + 'resid'
      let blockedKey = colProp + 'blocked'
      if (! row[resIdKey] ){
        let obj = {
          spaceId: row.id,
          selectedDate: dayjs(column.property.substr(1)).format('YYYY-MM-DD')
        }
        //  finally, don't emit from clicks on first two rows:
        //  prop = "description", prop = "expand"
        if( column.property != 'description' && column.property != 'expand') {
          this.$emit( 'emptyCellClick', obj )
        }
      }
    },
    emptyBlockClick ( row, column ) {
      console.log('emptyBlockClick on resViewTable', row, column)
    },
    getChildrenIds( spaceId ) {
      const childrenArr = []
      const getChildren = (spaceId) => {
        let children = _.find(this.resSpaceCopy, (o) => {
          return o.childOf == spaceId
        })
        if( children ) {
          _.each(children, ( child ) => {
            console.log('child', child.id)
            childrenArr.push( child.id )
            getChildren( child.id )
          })
        } else {
          console.log('cArr', childrenArr)
          return childrenArr
        }
      }
      getChildren(spaceId)
    },
    resBlockClick( resId ) {
      //  pass the emitted event up to the parent
      this.$emit('resBlockClick', resId)
    },
    rowStyle ( obj ) {
      //  exclude level 0 items
      if(obj.row.childOf > 0) {
        const childOfRecord = _.find(this.tableData, (o) => {
          return o.id == obj.row.childOf
        })
        if(  childOfRecord.showChildren == true ) {
          return ''
        } else {
          return 'display: none;'
        }
      }
    },
    unassignedResSelect ( resId ) {
      //console.log('resId @ resViewTable', resId)
      // pass the event to parent (resView3)
      this.$emit('unassigned-res-selected', resId)
    }
  },
  mounted () {
    //  TODO super hackey ( but necessary! ) here . . .
    //  I tried and failed to use nextTick(), but probably fucked it up
    const myTimeout = setTimeout(this.setInitialScroll, 1)

    //  set scroll events
    //  see https://github.com/element-plus/element-plus/discussions/9679
    //  for the mysterious "wrapRef" function
    this.$refs.resViewTable.$refs.scrollBarRef.wrapRef.onscroll = (event) => {
      resViewStore().scrollLeft = event.target.scrollLeft
      resViewStore().scrollTop = event.target.scrollTop
    }
  },
  watch: {
    resSpaceCopy (old, newd){
      console.log('resSpaceCopy change on resViewTable')
    },
    tableData ( ) {
      console.log('tableData wach triggered on resViewTable')
    },
    trigger () {
      console.log('trigger watch on resViewTable', this.trigger)
    }
  }
}

</script>

<style>
.resview-table-cell .cell {
  padding: 0px !important;
}
.resview-header-cell .cell  {
  padding: 0px !important;
}
.resview-row .el-table__cell {
  padding: 0px !important;
}
.resview-header-row th {
  padding: 0px !important;
}

</style>