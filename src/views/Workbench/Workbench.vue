<template>
  <!--
  <el-button @click="clickResTab">isDark = {{isDark}}</el-button>
  -->
  <splitpanes vertical class="default-theme" style="height: 100%">
    <pane size="33" style="height: 100%">
      <div>
        <el-tabs type="border-card" ref="tabsRef">
          <el-tab-pane label="Create">
            <CreateReservation
            @create-reservation:reload-reservations="reloadReservations"/>
          </el-tab-pane>
          <el-tab-pane label="Reservation" name="resTab" ref="resTabRef">
            <ReservationView
              v-if="selectedReservation"
              :selectedReservation="selectedReservation"
              @reservation-view:update-reservations="reloadReservations"
            />
          </el-tab-pane>
        </el-tabs>
      </div>
    </pane>
    <pane
      size="67"
      :class="{darkpane: isDark, lightpane: !isDark}"
    >
      <ResView
        @reservationSelected="reservationSelected"
        :reloadTrigger="resViewReloadTrigger"
      />
    </pane>

  </splitpanes>
</template>


<script setup>
  import { Splitpanes, Pane } from 'splitpanes'
  import 'splitpanes/dist/splitpanes.css'
  import { computed, onMounted, ref } from 'vue'
  import ResView from '@/views/ResView/ResView.vue'
  import CreateReservation from '@/views/CreateReservation/CreateReservation.vue'
  import ReservationView from '@/views/ReservationView/ReservationView.vue'
  import _ from 'lodash'
  //  dark theme
  import { useDark } from '@vueuse/core'

  //  REFS
  const windowHeight = ref(0)
  const windowWidth = ref(0)
  const selectedReservation = ref(null)
  const resViewReloadTrigger = ref(1)

  const resTabRef = ref()
  const tabsRef = ref()

  const isDark = useDark()

  //  METHODS
  const reloadReservations = () => {
    console.log('Workbench gets reload trigger')
    resViewReloadTrigger.value += 1
  }

  const clickResTab = () => {
    console.log('programatically generating a click event on tab-resTab')
    let resTab = document.getElementById('tab-resTab')
    resTab.click()
  }

  const reservationSelected = ( res ) => {
    selectedReservation.value = res
    //  generate a click on the res tab
    clickResTab()
  }

  //  ONMOUNTED
  onMounted( () => {
   windowWidth.value = window.innerWidth
   windowHeight.value = window.innerHeight
    window.addEventListener('resize', (event) => {
      windowWidth.value = window.innerWidth
      windowHeight.value = window.innerHeight
    }, true) 
  })
</script>

<style>

  .splitpanes--vertical > .splitpanes__splitter {
    min-width: 10px;
    background-color: #409eff !important;
    border-left: 0px !important;
  }

  .darkpane {
    background-color: #1c1b22 !important;
  }

  .lightpane {
    background-color: #ffffff !important;
  }
  
</style>