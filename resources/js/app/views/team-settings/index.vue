<template>
	<div class="settings">
    <div class="settings-sidebar">
      <div class="settings-sidebar-inner">
        <strong class="settings-title">{{$t('team_settings')}}</strong>
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
    import { state } from '@js/shared/store'
    export default {
        data() {
            return {
                state: state
            }
        },
        computed: {
            items() {
                let list = [
                    {to: '/team-settings', title: 'general'},
                    // {to: '/team-settings/email', title: 'email'},
                    {to: '/team-settings/documents', title: 'documents'},
                    {to: '/team-settings/taxes', title: 'taxes'},
                    {to: '/team-settings/contacts', title: 'contacts', break: true},
                    {to: '/team-settings/items', title: 'items'},
                    {to: '/team-settings/quotations', title: 'quotations'},
                    {to: '/team-settings/invoices', title: 'invoices'},
                    {to: '/team-settings/payments', title: 'payments'},
                    {to: '/team-settings/refunds', title: 'refunds'},
                    {to: '/team-settings/vendors', title: 'vendors', break: true},
                    {to: '/team-settings/purchase-orders', title: 'purchase_orders'},
                    {to: '/team-settings/expenses', title: 'expenses'},
                    {to: '/team-settings/terms', title: 'terms', break: true},
                    {to: '/team-settings/payment-gateway', title: 'payment_gateway'},
                    {to: '/team-settings/members', title: 'members', break: true},
                    {to: '/team-settings/invitations', title: 'invitations'},
                    {to: '/team-settings/roles', title: 'roles_and_permissions'}
                ]

                let modules = this.state.current_team_role.permissions.settings
                let filtered = list.filter((item) => {
                  if(modules[item.title]) {
                    return true
                  }
                })
                return filtered
            }
        }
    }
</script>