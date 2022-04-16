<template>
	<div class="settings">
    <div class="settings-sidebar">
      <div class="settings-sidebar-inner">
        <strong class="settings-title">{{$t('my_account')}}</strong>
        <ul class="settings-list">
          <template v-for="item in items">
            <li class="sidebar-break" v-if="item.break"></li>
            <li>
              <router-link class="settings-link" :to="item.to">
                <span class="settings-text">{{$t(item.title)}}</span>
                <i class="settings-icon2 icon icon-arrow-right-b"></i>
              </router-link>
            </li>
          </template>
        </ul>
      </div>
    </div>
    <div class="settings-inner">
      <transition name="fade" mode="out-in">
        <router-view></router-view>
      </transition>
    </div>
	</div>
</template>
<script>
    export default {
        computed: {
        	items() {
        		let list = [
                    {to: '/my-account', title: 'general'},
                    {to: '/my-account/security', title: 'security'}
                ]

                return list
        	}
        }
    }
</script>