<template>
  <splitpanes vertical class="default-theme" style="height: 100%;">
    <pane size="30" style="height: 100%;">
      <div >
        <el-tabs type="border-card">
          <el-tab-pane label="Create">
            <CreateReservation/>
          </el-tab-pane>
          <el-tab-pane label="Reservation">
            <ReservationView
              v-if="selectedReservation"
              :selectedReservation="selectedReservation"
              @reservation-view:update-reservations="reloadReservations"
            />
          </el-tab-pane>
        </el-tabs>
      </div>
    </pane>
    <pane size="70" style="background-color: #1c1b22;">
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

  //  REFS
  const windowHeight = ref(0)
  const windowWidth = ref(0)
  const selectedReservation = ref(null)
  const resViewReloadTrigger = ref(1)

  //  METHODS
  const reloadReservations = () => {
    console.log('reload on workbench')
    resViewReloadTrigger.value += 1
  }

  const reservationSelected = ( res ) => {
    selectedReservation.value = res
    console.table(_.cloneDeep(selectedReservation.value))
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
  
</style>