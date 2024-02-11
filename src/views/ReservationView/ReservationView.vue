<template>
  <el-dialog
    v-model="showFolioDialog"
    :fullscreen="true"
    :destroy-on-close="true"
  >
    <div>empty</div>
  </el-dialog>
  <div v-if="rootSpaces" class="wrapper">
    <el-row>
      <el-col :span="12">
        <h3>{{ selectedReservation.customer_first }}&nbsp{{ this.selectedReservation.customer_last }}</h3>
        <div>
          <span>{{ selectedReservation.checkin }}</span>
          <span> : </span>
          <span>{{ selectedReservation.checkout }}</span>
        </div>
        <div v-if="rootSpace">{{ $t('message.spaceLabel') }}: {{ rootSpace.title }}</div>
        <div>{{ $t('message.people') }}: {{ selectedReservation.people }}</div>
        <div>{{ $t('message.beds') }}: {{ selectedReservation.beds }}</div>
        <div>Res Id : {{ selectedReservation.id }}</div>
      </el-col>
      <el-col :span="12">
        <el-button @click="loadFolio" style="float: right">Folio</el-button>
      </el-col>
    </el-row>
    <el-collapse>
      <el-collapse-item title="Edit Reservation" name="2">
        <EditReservation
          v-if="rootSpaces"
          
          :componentKey="componentKey"

          :resId="selectedReservation.id"
          :checkin="selectedReservation.checkin"
          :checkout="selectedReservation.checkout"
          :people="selectedReservation.people"
          :beds="selectedReservation.beds"
          :isAssigned="selectedReservation.is_assigned"
          :spaceTypePref="selectedReservation.space_type_pref"
          :spaceId="selectedReservation.space_id"
          :customer="selectedReservation.customer"
          :customerFirst="selectedReservation.customer_first"
          :customerLast="selectedReservation.customer_last"
          @edit-reservation:modify-reservation-1="modifyReservation1">
        </EditReservation>
      </el-collapse-item>
      <el-collapse-item title="Notes" name="1">
        <el-button size="small" @click="">an</el-button>
        <div>
          <el-input/>
        </div>
        <table class="historyTable">
          <tbody>
            <tr v-for="note in selectedReservation.notes">
              <td>{{note.t}}</td>
              <td>{{note.n}}</td>
              <td>{{note.d}}</td>
            </tr>
          </tbody>
        </table>
      </el-collapse-item>
      <el-collapse-item title="History" name="2">
        <table class="historyTable">
          <tbody>
            <tr v-for="history in selectedReservation.history">
              <td>{{history.t}}</td>
              <td>{{history.n}}</td>
              <td>{{history.d}}</td>
            </tr>
          </tbody>
        </table>
      </el-collapse-item>
    </el-collapse>

 
 </div>
</template>

<script lang="js">
  import EditReservation from '@/views/ReservationView/editReservation.vue'
  import _ from 'lodash'
  import { reservationData } from '@/data/reservationData.js'
  import { resViewStore } from '@/stores/resViewStore.js'
  import { rootSpacesStore } from '@/stores/rootSpacesStore.js'
  import { ElMessage } from 'element-plus'
  import useHandleRequestError from '@/composables/useHandleRequestError.js'
  export default {
    setup () {
        //  import composable function for request error handling
        const { handleRequestError } = useHandleRequestError()
        return { handleRequestError }
    },
    name: 'ReservationView',
    props: [ 
      'selectedReservation'
    ],
    components: { EditReservation },

    emits: [ 
      'reservation-view:update-reservations',
      'reservation-view:update-selected-reservation'
    ],
    data () {
      return {
        componentKey: 1,
        rootSpaces: null,
        showHistory: false,
        showFolioDialog: false
      }
    },
    computed: {
      rootSpace () {
        const rs = _.find( this.rootSpaces, o => {
          return o.id == this.selectedReservation.space_id
        })
        return rs
      }
    },
    methods: {
      loadFolio () {
        const els = document.querySelectorAll("li.is-active")
        //  remove 'is-active' class from main menu items
        _.each(els, el => {
          el.classList.remove('is-active')
        })
        this.$router.push('folio/' + this.selectedReservation.folio)
      },
      modifyReservation1 ( args ) {
        //console.log('reservationview gets command')
        //console.table( args )
        reservationData.modifyReservation1( args ).then( response => {
          console.log('modifyReservation1:')
          console.log(response.data)
          if( response.data.success == true ) {
            //  tell parent (resView3) to reload reservations
            this.$emit('reservation-view:update-reservations')
            //  tell the parent (resView3) to update selected reservation
            //  this will iterate down the event chain and update here
            //  and on editReservation
            this.$emit('reservation-view:update-selected-reservation', response.data.current_res )
            //  increment the componentKey prop to signal we need to clean up
            this.componentKey += 1
            ElMessage({
              type: 'success',
              message: 'Reservation updated'
            })
          } else {
            ElMessage({
              type: 'warning',
              message: 'There was an error'
            })
          }
        }).catch( error => {
          this.handleRequestError( error )
        })
      },
      reservationCheckin () {
        api.reservations.reservationCheckin ( this.selectedReservation.id, this.token ).then(
          response => {
            if( response.data.checkin == true && response.data.reservation_after_checkin) {
              this.$emit('reservation-view:update-reservations')
              this.$emit('reservation-view:update-selected-reservation', response.data.reservation_after_checkin )
              ElMessage({
                type: 'success',
                message: 'Reservation checked in'
              })
            } else {
              ElMessage({
                type: 'warning',
                message: 'There was an error'
              })
            }
        }).catch( error => {
          this.handleRequestError( error )
        })
      },
      reservationCheckout () {
        api.reservations.reservationCheckout ( this.selectedReservation.id, this.token ).then(
          response => {
            console.log(response)
            if( response.data.checkout == true && response.data.reservation_after_checkout) {
              this.$emit('reservation-view:update-reservations')
              this.$emit('reservation-view:update-selected-reservation', response.data.reservation_after_checkout )
              ElMessage({
                type: 'success',
                message: 'Reservatin checked out'
              })
            }
        }).catch( error => {
          this.handleRequestError( error )
        })
      }
    },
    mounted () {
      //  get rootSpaces . . . 
      if( resViewStore().showHideRootSpaceCopy ) {
        this.rootSpaces = resViewStore().showHideRootSpaceCopy
      } else { 
        this.rootSpaces = rootSpacesStore().rootSpaces
      }
    }
  }

</script>

<style>

.historyTable {
  display: block;
  border-collapse: collapse;
  border: 1px solid rgb(139, 139, 139);
  width: 100%;
  height: 160px;
  overflow: scroll;
}

.historyTable td {
  border: 1px solid rgb(139, 139, 139);
  padding: 2px;
}

.wrapper{
  padding-right: 4px;
  padding-left: 4px;
}
</style>