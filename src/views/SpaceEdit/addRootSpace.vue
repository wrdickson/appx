<template>
  <el-form
    label-width="120px">
    <el-form-item>
      <el-select v-model= selectedSpace placeholder="Select">
        <template v-for="space in rootSpaces">
          <el-option :label="space.title" :value="space.id"></el-option>
        </template>
      </el-select>
    </el-form-item>
  </el-form>
  <el-form
    :model="form" 
    label-width="120px">
    <el-form-item label="Title">
      <el-input v-model="form.title"></el-input>
    </el-form-item>
    <el-form-item label="Child Of">
      <el-select v-model="form.childOf" placeholder="Select">
        <template v-for="space in rootSpaces">
          <el-option :label="space.title" :value="space.id" ></el-option>
        </template>
      </el-select>
    </el-form-item>
    <el-form-item label="Type">
      <el-select v-model="form.spaceType">
        <template v-for="spaceType in spaceTypes">
          <el-option :label="spaceType.title" :value="spaceType.id"></el-option>
        </template>
      </el-select>
    </el-form-item>
    <el-form-item label="Sort order">
      <el-input v-model="form.sortOrder"></el-input>
    </el-form-item>
    <el-form-item label="Show children">
      <el-select v-model="form.showChildren" placeholder="Select">
        <el-option label="True" value="1"/>
        <el-option label="False" value="0"/>
      </el-select>
    </el-form-item>
    <el-form-item>
      <el-button v-if="formDataComplete" @click="createSpace">Create</el-button>
    </el-form-item>
  </el-form>
</template>

<script>
export default {
  name: 'editRootSpace',
  props: [ 'rootSpaces', 'spaceTypes', 'selectedSpace' ],
  data () {
    return {
      form: {
          childOf: 0,
          showChildren: null,
          sortOrder: 0,
          title: "",
          spaceType: null
      }
    }
  },
  computed: {
    formDataComplete() {
        if (this.form.title.length > 0 && this.form.spaceType && this.form.showChildren) {
            return true;
        }
        else {
            return false;
        }
    }
  }
}
</script>