<template>
  <el-card>
    <template #header>
      <div class="card-header">
        <span>{{t('message.createSpace')}}</span>
        <el-button class="button" size="small" @click="emitClose">{{t('message.close')}}</el-button>
      </div>
    </template>
      <el-form
        :model="form" 
        size="small"
        label-position="right"
        label-width="160px">

        <el-form-item :label="i18Title">
          <el-input v-model="form.title"></el-input>
        </el-form-item>
        <el-form-item :label="i18IsActive">
          <el-select v-model="form.isActive" placeholder="Select">
            <el-option :label="i18True" value="1"/>
            <el-option :label="i18False" value="0"/>
          </el-select>
        </el-form-item>
        <el-form-item :label="i18ChildOf">
          <el-select v-model="form.childOf" placeholder="Select">
              <el-option label="0" value="0"/>
            <template v-for="space in rootSpaces">
              <el-option :label="space.title" :value="space.id" ></el-option>
            </template>
          </el-select>
        </el-form-item>
        <el-form-item :label="i18Type">
          <el-select v-model="form.spaceType">
            <template v-for="spaceType in spaceTypes">
              <el-option :label="spaceType.title" :value="spaceType.id"></el-option>
            </template>
          </el-select>
        </el-form-item>
        <el-form-item :label="i18DisplayOrder">
          <el-input v-model="form.displayOrder"></el-input>
        </el-form-item>
        <el-form-item :label="i18ShowChildren">
          <el-select v-model="form.showChildren" placeholder="Select">
            <el-option :label="i18True" value="1"/>
            <el-option :label="i18False" value="0"/>
          </el-select>
        </el-form-item>
        <el-form-item :label="i18People">
            <el-select v-model="form.people">
              <template v-for="people in peopleArr">
                <el-option :label="people" :value="people"></el-option>
              </template>
            </el-select>
        </el-form-item>
        <el-form-item :label="i18Beds">
          <el-select v-model="form.beds">
            <template v-for="beds in bedsArr">
              <el-option :label="beds" :value="beds"></el-option>
            </template>
          </el-select>
        </el-form-item>
        <el-form-item>
          <el-button  @click="createSpace">{{t('message.createSpace')}}</el-button>
        </el-form-item>
      </el-form>
  </el-card>
</template>

<script setup>
  import _ from 'lodash'
  import { computed, ref } from 'vue'
  import { useI18n } from 'vue-i18n'

  const { t } = useI18n() // call `useI18n`, and spread `t` from  `useI18n` returning
  const props = defineProps( [ 'rootSpaces', 'spaceTypes' ] )
  const emit = defineEmits(['create-space', 'emit-close'])
  
  const bedsArr = ref([1,2,3,4,5,6,7,8,9,10])
  const peopleArr = ref( [0,1,2,3,4,5,6,7,8,9,10] )
  const form = ref( {
    title: null,
    childOf: null,
    spaceType: null,
    displayOrder: null,
    showChildren: null,
    people: null,
    beds: null,
    isActive: "1"
  })

  const i18Beds = computed( () => { return t('message.beds') })
  const i18ChildOf = computed( () =>{ return t('message.childOf') })
  const i18ConfirmDelete = computed( () => { return t('message.confirmDelete') })
  const i18CreateSpace = computed( () => { return t('message.createSpace') })
  const i18DeleteSpace= computed( () => {
    return t('message.delete') + ' ' + t('message.spaceLabel')
  })
  const i18DisplayOrder = computed( () => { return t('message.displayOrder') })
  const i18False = computed( () => { return t('message.false') })
  const i18No = computed( () => {return t('message.no') })
  const i18People = computed( () => { return t('message.people') })
  const i18ShowChildren = computed( () => { return t('message.showChildren') })
  const i18True = computed( () => { return t('message.true') })
  const i18Title = computed( () => { return t('message.title') })
  const i18Type = computed( () => { return t('message.type') })
  const i18Yes = computed( () => { return t('message.yes') })
  const i18IsActive = computed( () =>{ return t('message.isActive') })


  const createSpace = () => {
    emit('create-space', form.value)
  }

  const emitClose = () => {
    emit('emit-close')
  }

</script>

<style>
.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
</style>