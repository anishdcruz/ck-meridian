<template>
  <div class="container">
  	<div class="app-notification" v-if="!state.current_team.is_setup">
  		<p>
  			Your account need some setup before you can start.
  			<x-button size="sm" type="success" @click="toggleAccountSetup">Setup Now</x-button>
  		</p>
  	</div>
  	<div class="navbar">
  	  <div class="navbar-inner">
  	    <div class="navbar-toggle">
  	      <span class="icon icon-android-menu" @click="toggleMenu"></span>
  	    </div>
  	    <div class="navbar-brand">
  	        <img src="./img/meridian-logo.png" class="navbar-logo">
  	    </div>
  	    <div class="navbar-dropdown">
  	      <x-dropdown size="sm" dir="right" icon="more">
  	        <strong class="navbar-dropdown-link"
  	          slot="button" slot-scope="{ toggle }"
  	          @click.stop="toggle()">
  	            <i class="icon icon-person"></i>
  	            <span class="navbar-user">
  	            	<span>{{state.user.name}}</span>
  	            	<span class="navbar-team-name">{{currentTeam}}</span>
  	            </span>
  	            <i class="icon icon-arrow-down-b"></i>
  	          </strong>

  	        <x-dropdown-menu slot="menu">
               <x-dropdown-link icon="android-settings" :to="`/my-account`">
                {{$t('my_account')}}
              </x-dropdown-link>
               <x-dropdown-link icon="settings"
                :to="`/team-settings`">
                {{$t('team_settings')}}
              </x-dropdown-link>
              <template v-if="$inSAASMode">
              	<div v-if="!state.subscription.isSubscribed || state.subscription.onGracePeriod">
              	  <div class="dropdown-divide"></div>
              	  <router-link to="/my-account/subscription" class="dropdown-link">
              	    <div class="dropdown-team-item">
              	      <i class="dropdown-team-icon icon icon-refresh icon-green"></i>
              	      <span class="dropdown-team-name">{{$t('subscribe_now')}}</span>
              	    </div>
              	  </router-link>
              	</div>
              </template>

              <div v-if="state.subscription.isSubscribed">
                <div class="dropdown-divide"></div>
                <a @click.stop="toggleTeamModal" class="dropdown-link">
                  <div class="dropdown-team-item">
                    <i class="dropdown-team-icon icon icon-plus"></i>
                    <span class="dropdown-team-name">{{$t('create_team')}}</span>
                  </div>
                </a>
              </div>
              <template v-for="(team, index) in state.user.teams">
                <a :href="`/teams/switch/${team.id}`" class="dropdown-link">
                  <div class="dropdown-team-item">
                    <i class="dropdown-team-icon icon icon-checkmark icon-green"
                      v-if="isCurrentTeam(team)"></i>
                    <i class="dropdown-team-icon icon icon-android-people"
                      v-else></i>
                    <span class="dropdown-team-name">{{team.name}}</span>
                  </div>
                </a>
              </template>
              <div>
                <div class="dropdown-divide"></div>
                <a href="/logout" class="dropdown-link">
                  <div class="dropdown-team-item">
                    <i class="dropdown-team-icon icon icon-log-out"></i>
                    <span>{{$t('logout')}}</span>
                  </div>
                </a>
              </div>
  	        </x-dropdown-menu>
  	      </x-dropdown>
  	    </div>
  	  </div>
  	</div>
    <div class="container-main">
      <div class="sidebar" :style="{width: navbar ? '4%' : '17%'}">
        <div class="sidebar-inner">
          <template v-for="link in links">
            <router-link :to="link.to" class="sidebar-link">
              <div class="sidebar-icon">
                <span :class="`icon icon-${link.icon}`"></span>
              </div>
              <div v-if="!navbar" class="sidebar-text">{{$t(link.title)}}</div>
              <div v-if="!navbar" class="sidebar-arrow">
                <span class="icon icon-arrow-right-b"></span>
              </div>
            </router-link>
            <div class="sidebar-break" v-if="link.break"></div>
          </template>
        </div>
      </div>
      <div class="content" :style="{width: navbar ? '96%' : '83%'}">
        <transition name="fade" mode="out-in">
          <router-view :key="$route.path"></router-view>
        </transition>
      </div>
    </div>
    <create-team-modal v-if="showCreateTeam"
      @cancel="toggleTeamModal"
      @ok="handleCreateTeam"></create-team-modal>

      <account-setup-modal v-if="showAccountModal"
      @cancel="toggleAccountSetup"
      @ok="handleAccountSetup"></account-setup-modal>
  </div>
</template>
<script>
  import { state } from '@js/shared/store'
  import CreateTeamModal from '@js/partials/modals/CreateTeamModal.vue'
  import AccountSetupModal from '@js/partials/modals/AccountSetupModal.vue'

  export default {
  	name: 'App',
    components: { CreateTeamModal, AccountSetupModal },
  	data() {
  		return {
	        showCreateTeam: false,
	        state,
	  		navbar: 0,
	  		showAccountModal: false
  		}
  	},
  	mounted() {
  		if(!this.state.current_team.is_setup) {
  			this.toggleAccountSetup()
  		}
  	},
    computed: {
    	currentTeam() {
    		return this.state.current_team ? this.state.current_team.name : null
    	},
      isCurrentTeam() {
        return (team) => {
          if(!team) { return false }
          return team.id === this.state.current_team.id
        }
      },
      isOwnerOfCurrentTeam() {
        return (team) => {
          if(!team) { return false }
          return team.owner_id === this.state.user.id
        }
      },
      links() {
        if(!this.state.subscription.isSubscribed && !this.state.subscription.hasTeams) {
          return []
        }

        const list = [
          {icon: 'ios-people', title: 'contacts', to: '/contacts'},

          {icon: 'cube', title: 'items', to: '/items', break: true},
          {icon: 'document-text', title: 'quotations', to: '/quotations'},
          // {icon: 'document-text', title: 'sales_orders', to: '/sales_orders'},
          {icon: 'document-text', title: 'invoices', to: '/invoices'},
          {icon: 'android-refresh', title: 'recurring_invoices', to: '/recurring-invoices'},
          {icon: 'card', title: 'payments', to: '/payments'},
          {icon: 'email', title: 'refunds', to: '/refunds', break: true},

          {icon: 'ios-people', title: 'vendors', to: '/vendors'},
          {icon: 'cash', title: 'purchase_orders', to: '/purchase-orders'},
          {icon: 'ios-people', title: 'payments_made', to: '/payments-made'},
          {icon: 'cash', title: 'expenses', to: '/expenses', break: true},

          {icon: 'android-refresh', title: 'recurring_exports', to: '/recurring-exports'},

          // {icon: 'document-text', title: 'reports', to: '/reports'}
        ]

        let modules = this.state.current_team_role.permissions.modules
        let filtered = list.filter((item) => {
          if(modules[item.title]) {
            if(modules[item.title].view) {
              return true
            }
          }
        })
        filtered.unshift({icon: 'home', title: 'overview', to: '/', break: true})
        return filtered
      }
    },
  	methods: {
  		handleAccountSetup() {

  		},
  		toggleAccountSetup() {
  			this.showAccountModal = !this.showAccountModal
  		},
      handleCreateTeam(team) {
        window.location.replace(`/teams/switch/${team.id}`);
      },
      toggleTeamModal() {
        this.showCreateTeam = !this.showCreateTeam
      },
      toggleMenu() {

        if(this.navbar) {
          this.navbar = 0
        } else {
          this.navbar = 1
        }

        localStorage.setItem('ck.navbar', (this.navbar))
      }
    }
  }
</script>