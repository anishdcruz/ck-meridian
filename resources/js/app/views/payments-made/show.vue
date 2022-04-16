<template>
	<div class="content-inner" v-if="show">
		<x-panel padding margin>
			<div slot="title">
				<router-link to="/payments-made">{{$t('payment_made')}}</router-link> / {{model.name}}
				<small>({{model.number}})</small>
			</div>
			<div slot="extra">
				<router-link :to="`/payments-made`" class="btn btn-default btn-sm">
					<small class="icon icon-arrow-left-c"></small>
				</router-link>
				<x-dropdown size="sm" dir="right" icon="more">
					<x-dropdown-menu slot="menu">
						<x-dropdown-item>
							<a @click.stop="removeDB('payments-made', model.id)">{{$t('delete')}}</a>
						</x-dropdown-item>
					</x-dropdown-menu>
				</x-dropdown>
			</div>
			<x-row line>
				<x-group col="8" label="number">
					<p>{{model.number}}</p>
				</x-group>
				<x-group col="8" label="vendor">
					<router-link :to="`/vendors/${model.vendor_id}`">
					    {{model.vendor.name}}
					    <span v-if="model.vendor.organization">
					        - {{model.vendor.organization}}<br>
					    </span>
					</router-link>

				</x-group>
				<x-group col="8" label="purchase_order">
					<router-link :to="`/purchase-orders/${model.purchase_order_id}`">
					    {{model.purchase_order.number}}
					</router-link>
				</x-group>
		</x-row>

		<x-row line>
			<x-group col="8" label="payment_date">
				<p>{{model.payment_date | toDate}}</p>
			</x-group>
			<x-group col="8" label="amount_paid">
				<p>{{model.amount_paid | formatMoney}}</p>
			</x-group>
			<x-group col="8" label="transaction_fees">
				<p>{{model.transaction_fees | formatMoney}}</p>
			</x-group>
			<x-group col="8" label="net_amount">
				<p>{{model.net_amount | formatMoney}}</p>
			</x-group>
		</x-row>
		<x-row line>
			<x-group col="8" label="payment_method">
				<pre>{{$t(model.method)}}</pre>
			</x-group>
			<x-group col="8" label="note">
				<pre>{{model.note || '-'}}</pre>
			</x-group>
		</x-row>
		<x-row line>
			<x-group col="8" label="created_at">
				<pre>{{model.created_at | toDate}}</pre>
			</x-group>
		</x-row>
		</x-panel>
	</div>
</template>
<script>
	import { showable } from '@js/lib/mixins'
	import { state } from '@js/shared/store'
	import RefundModal from '@js/partials/RefundModal.vue'

	export default {
		components: { RefundModal },
		mixins: [ showable ],
		data() {
			return {
				show: false,
				config: state.team_settings,
				model: {
					category: {}
				},
				showModal: false
			}
		},
		methods: {
			toggleRefundModal() {
				this.showModal = !this.showModal
			}
		}
	}
</script>