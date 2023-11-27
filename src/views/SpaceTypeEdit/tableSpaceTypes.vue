<template>
  <el-table 
    :data="spaceTypes" 
    stripe
    highlight-current-row
    height="500"
    size="small"
    style="width: 100%"
    cell-class-name="t-table-cell"
    table-layout="auto"
    @row-click="rowSelected"
    >
    <el-table-column prop="id" label="Id"></el-table-column>
    <el-table-column prop="title" label="Title"></el-table-column>
    <el-table-column prop="is_active" label="Is Active"></el-table-column>
    <el-table-column prop="display_order" label="Display Order"></el-table-column>

  </el-table>
</template>

<script lang="js">
  import { spaceTypesData } from '@/data/spaceTypes.js'
  export default {
    name: "TableSpaceTypes",
    props: [ 'reloadTrigger' ],
    emits: ['tableSpaceTypes:spaceTypeSelected'],
    data () {
      return {
        cellStyle: {
          padding: '5px'
        },
        selctedSpaceType: {},
        spaceTypes: [],
        tstriped: true
      }
    },
    computed: {
    },
    methods: {
      getSpaceTypes () {
        spaceTypesData.getSpaceTypes().then( response => {
        this.spaceTypes = response.data.space_types
        })
      },
      rowSelected ( o ) {
        console.log('selected')
        console.log('o', o)
        //  this is where we remove the reactivity
        this.$emit('tableSpaceTypes:spaceTypeSelected', {...o} )
      }
    },
    mounted () {
      this.getSpaceTypes()
    },
    watch: {
      reloadTrigger ( oldVal, newVal ) {
        this.getSpaceTypes()
      }
    }
  }
</script>
