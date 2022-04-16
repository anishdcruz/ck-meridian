<template>
	<div class="content-inner" v-if="show">
		<x-panel padding margin>
			<div slot="title">
				<router-link to="/expenses">{{$t('expense')}}</router-link> / {{model.name}}
				<small>({{model.number}})</small>
			</div>
			<div slot="extra">
				<router-link :to="`/expenses`" class="btn btn-default btn-sm">
					<small class="icon icon-arrow-left-c"></small>
				</router-link>
				<x-dropdown size="sm" dir="right" icon="more">
					<x-dropdown-menu slot="menu">
						<x-dropdown-item>
							<a @click.stop="removeDB('expenses', model.id)">{{$t('delete')}}</a>
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
				<x-group col="8" label="category">
					<p>{{model.category.name}}</p>
				</x-group>
				<x-group col="8" label="payment_date">
					<p>{{model.payment_date | toDate}}</p>
				</x-group>
		</x-row>

		<x-row line>
			<x-group col="8" label="sub_total">
				<p>{{model.sub_total | formatMoney}}</p>
			</x-group>
			<template v-if="Number(config.tax_enable)">
				<x-group col="8"
					:label="`${config.tax_label} @ ${model.tax} %`">
					<p>{{model.tax_total | formatMoney(false)}}</p>
				</x-group>
	    	<x-group col="8" v-if="Number(config.tax_2_enable)"
	    		:label="`${config.tax_2_label} @ ${model.tax_2} %`">
	    		<p>{{model.tax_2_total | formatMoney(false)}}</p>
	    	</x-group>
			</template>
			<x-group col="8" label="grand_total">
				<p>{{model.grand_total | formatMoney}}</p>
			</x-group>
		</x-row>
		<x-row line>
			<x-group col="8" label="payment_method">
				<pre>{{$t(model.method)}}</pre>
			</x-group>
			<x-group col="8" label="note">
				<pre>{{model.note || '-'}}</pre>
			</x-group>
			<x-group col="8" label="reference">
				<p>{{model.reference || '-'}}</p>
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