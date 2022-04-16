<template>
  <div class="container">
  	<div class="navbar">
  	  <div class="navbar-inner">
  	    <div class="navbar-toggle">
  	      <span class="icon icon-android-menu" @click="toggleMenu"></span>
  	    </div>
  	    <div class="navbar-brand">
  	        <img src="../app/img/meridian-logo.png" class="navbar-logo">
  	    </div>
  	    <div class="navbar-dropdown">
  	      <x-dropdown size="sm" dir="right" icon="more">
  	        <strong class="navbar-dropdown-link"
  	          slot="button" slot-scope="{ toggle }"
  	          @click.stop="toggle()">
  	            <i class="icon icon-person"></i>
  	            <span>{{state.user.name}}</span>
  	            <i class="icon icon-arrow-down-b"></i>
  	          </strong>

  	        <x-dropdown-menu slot="menu">
               <x-dropdown-link icon="android-settings" :to="`/my-account`">
                {{$t('my_account')}}
              </x-dropdown-link>
              <div>
                <a :href="`${state.admin_prefix}/logout`" class="dropdown-link">
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
  </div>
</template>
<script>

  export default {
  	name: 'App',
  	data() {
  		return {
	  		navbar: 0,
	  		showAccountModal: false,
	  		state: window.ck_init
  		}
  	},
    computed: {
      links() {
        const list = [
        	{icon: 'home', title: 'overview', to: '/', break: true},
          	{icon: 'ios-people', title: 'users', to: '/users'},
          	{icon: 'document-text', title: 'subscriptions', to: '/subscriptions', break: true},
          	{icon: 'document-text', title: 'teams', to: '/teams'},

          	{icon: 'document-text', title: 'plans', to: '/plans'},
          	{icon: 'document-text', title: 'faqs', to: '/faqs', break: true},
          	{icon: 'ios-people', title: 'admins', to: '/admins'},
          	{icon: 'cash', title: 'currencies', to: '/currencies'}
        ]
        return list
      }
    },
  	methods: {

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