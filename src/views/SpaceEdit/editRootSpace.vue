<template>
  <el-card>
    <template #header>
      <div class="card-header">
        <span>{{t('message.editSpace')}}</span>
        <el-button class="button" plain @click="emitClose">{{t('message.close')}}</el-button>
      </div>
    </template>
      <el-form
        :model="selectedSpaceCopy" 
        size="small"
        label-position="right"
        label-width="160px">

        <el-form-item :label="i18Title">
          <el-input v-model="selectedSpaceCopy.title"></el-input>
        </el-form-item>
        <el-form-item :label="i18IsActive">
          <el-select v-model="selectedSpaceCopy.isActive" placeholder="Select">
            <el-option :label="i18True" value="1"/>
            <el-option :label="i18False" value="0"/>
          </el-select>
        </el-form-item>
        <!--
        <el-form-item :label="i18ChildOf">
          <el-select v-model="selectedSpaceCopy.childOf" placeholder="Select">
            <el-option label="0" value="0"></el-option>
            <template v-for="space in rootSpaces">
              <el-option v-if="selectedSpaceCopy.id != space.id"  :label="space.title" :value="space.id" ></el-option>
            </template>
          </el-select>
        </el-form-item>
        -->
        <el-form-item :label="i18Type">
          <el-select v-model="selectedSpaceCopy.spaceType">
            <template v-for="spaceType in spaceTypes">
              <el-option :label="spaceType.title" :value="spaceType.id"></el-option>
            </template>
          </el-select>
        </el-form-item>
        <el-form-item :label="i18DisplayOrder">
          <el-input v-model="selectedSpaceCopy.displayOrder"></el-input>
        </el-form-item>
        <el-form-item :label="i18ShowChildren">
          <el-select v-model="selectedSpaceCopy.showChildren" placeholder="Select">
            <el-option :label="i18True" value="1"/>
            <el-option :label="i18False" value="0"/>
          </el-select>
        </el-form-item>
        <el-form-item :label="i18People">
            <el-select v-model="selectedSpaceCopy.people">
              <template v-for="people in peopleArr">
                <el-option :label="people" :value="people"></el-option>
              </template>
            </el-select>
        </el-form-item>
        <el-form-item :label="i18Beds">
          <el-select v-model="selectedSpaceCopy.beds">
            <template v-for="beds in bedsArr">
              <el-option :label="beds" :value="beds"></el-option>
            </template>
          </el-select>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" plain @click="updateSpace">{{t('message.update')}}</el-button>
        </el-form-item>
      </el-form>
      <el-divider/>
      <div>
        <el-popconfirm 
          :title="i18ConfirmDelete"
          :confirm-button-text="i18Yes"
          :cancel-button-text="i18No"
          @confirm="deleteSpace">
          <template #reference>
            <el-button plain style="float: right; margin-top: -15px; margin-bottom: 10px;" type="danger">{{i18DeleteSpace}}</el-button>
          </template>
        </el-popconfirm>
      </div>
  </el-card>

</template>

<script setup>
  import { computed, ref } from 'vue'
  import _ from 'lodash'
  import { useI18n } from 'vue-i18n'


  const { t } = useI18n() // call `useI18n`, and spread `t` from  `useI18n` returning
  
  
  const props = defineProps( [ 'rootSpaces', 'spaceTypes', 'selectedSpace' ])
  const emit = defineEmits([ 'update-space', 'emit-close', 'delete-space' ])


  const bedsArr = ref([1,2,3,4,5,6,7,8,9,10])
  const peopleArr= ref( [0,1,2,3,4,5,6,7,8,9,10] )
  const showSpaceEdit = ref( false )
  //  don't pass the whole object, or reactivity will be passed too
  const selectedSpaceCopy = ref( {
    id: props.selectedSpace.id,
    beds: props.selectedSpace.beds,
    childOf: props.selectedSpace.childOf,
    children: props.selectedSpace.children,
    displayOrder: props.selectedSpace.displayOrder,
    //  cast to string to make the bound element-ui select show "true" "false" not 1, 0
    isActive: props.selectedSpace.isActive.toString(),
    parents: props.selectedSpace.parents,
    people: props.selectedSpace.people,
    //  cast to string to make the bound element-ui select show "true" "false" not 1, 0
    showChildren: props.selectedSpace.showChildren.toString(),
    title: props.selectedSpace.title,
    spaceType: props.selectedSpace.spaceType
  })
  
  const i18Beds = computed (() => { return t('message.beds') } )
  const i18ChildOf = computed( () => { return t('message.childOf') })
  const i18ConfirmDelete = computed( () => {return t('message.confirmDelete') })
  const i18DeleteSpace = computed( () => {
    return t('message.delete') + ' ' + t('message.spaceLabel')
  })
  const i18DisplayOrder= computed( () => { return t('message.displayOrder') } )
  const i18False = computed( () => { return t('message.false') })
  const i18No = computed( () => {return t('message.no') })
  const i18People = computed( () => { return t('message.people') })
  const i18ShowChildren = computed( () => { t('message.showChildren') })
  const i18True = computed( () => { return t('message.true') })
  const i18Title = computed( () => { return t('message.title') })
  const i18Type = computed( () => { return t('message.type') })
  const i18Yes = computed( () => { return t('message.yes') })
  const i18IsActive = computed( () => { return t('message.isActive') })
  
  const deleteSpace =  () => {
    emit('delete-space', props.selectedSpace)
  }
  const emitClose =  () => {
    emit('emit-close')
  }
  const updateSpace =  () => {
    emit( 'update-space', selectedSpaceCopy.value )
  }
  
</script>

<style>
.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
</style>