a<template>
	<div class="content-inner" v-if="show">
		<x-panel padding margin>
			<div slot="title">
				<router-link to="/vendors">{{$t('vendor')}}</router-link> / {{model.name}}
				<small>({{model.number}})</small>
			</div>
			<div slot="extra">
				<router-link :to="`/vendors`" class="btn btn-default btn-sm">
					<small class="icon icon-arrow-left-c"></small>
				</router-link>
				<router-link :to="`/vendors/${model.id}/edit`" class="btn btn-default btn-sm">
					<small class="icon icon-edit"></small>
				</router-link>
				<x-button size="sm" type="error" icon="trash-b" @click="removeDB('vendors', model.id)"></x-button>
			</div>
			<x-row line>
				<x-group col="8" label="number">
					<p>{{model.number}}</p>
				</x-group>
				<x-group col="8" label="category">
					<p v-if="model.category">
						{{model.category.name}}
					</p>
				</x-group>
				<x-group col="8" label="organization">
					<p>
						{{model.organization}}
					</p>
				</x-group>
		</x-row>
		<x-row line>
			<x-group col="8" label="name">
				<p>{{model.name || '-'}}</p>
			</x-group>
			<x-group col="8" label="email">
				<p>{{model.email || '-'}}</p>
			</x-group>
			<x-group col="8" label="title">
				<p>{{model.title || '-'}}</p>
			</x-group>
			<x-group col="8" label="department">
				<p>{{model.department || '-'}}</p>
			</x-group>
		</x-row>
		<x-row line>
			<x-group col="8" label="phone">
				<p>{{model.phone || '-'}}</p>
			</x-group>
			<x-group col="8" label="mobile">
				<p>{{model.mobile || '-'}}</p>
			</x-group>
			<x-group col="8" label="fax">
				<p>{{model.fax || '-'}}</p>
			</x-group>
			<x-group col="8" label="website">
				<p>{{model.website || '-'}}</p>
			</x-group>
		</x-row>
		<x-row line>
			<x-group col="8" label="primary_address">
				<pre>{{model.primary_address || '-'}}</pre>
			</x-group>
			<x-group col="8" label="other_address">
				<pre>{{model.other_address || '-'}}</pre>
			</x-group>
		</x-row>
		<x-row line>
			<x-group col="8" label="total_paid">
				<pre>{{model.total_paid | formatMoney}}</pre>
			</x-group>
			<x-group col="8" label="currency">
				<pre>{{model.currency ? model.currency.name : '-'}}</pre>
			</x-group>
		</x-row>
		<x-row line>
			<x-group col="8" label="created_at">
				<pre>{{model.created_at | toDate}}</pre>
			</x-group>
		</x-row>
		</x-panel>

		<x-mini :url="`purchase-orders?vendor_id=${this.model.id}`" title="purchase_orders">
			<x-tr slot="heading">
			    <x-td type="th" size="3">{{$t('number')}}</x-td>
			    <x-td type="th" size="3">{{$t('issue_date')}}</x-td>
			    <x-td type="th" size="9">{{$t('vendor')}}</x-td>
			    <x-td type="th" size="3">{{$t('grand_total')}}</x-td>
			    <x-td type="th" size="3">{{$t('status')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/purchase-orders/${item.id}`)">
			  	<x-td>{{item.number}}</x-td>
			  	<x-td>{{item.issue_date | toDate}}</x-td>
			  	<x-td>
			  		<span>{{item.vendor.name}}</span>
			  		<span v-if="item.vendor.organization"> - {{item.vendor.organization}}</span>
			  	</x-td>
			  	<x-td>{{item.grand_total | formatMoney}}</x-td>
			  	<x-td>
			  		<span :class="`status status-${item.status.color}`">
			  			<span class="status-text">{{item.status.name}}</span>
			  		</span>
			  	</x-td>
			</x-tr>
		</x-mini>

		<x-mini :url="`payments-made?vendor_id=${this.model.id}`" title="payments_made">
			<x-tr slot="heading">
		    <x-td type="th" size="3">{{$t('number')}}</x-td>
			    <x-td type="th" size="3">{{$t('payment_date')}}</x-td>
			    <x-td type="th" size="3">{{$t('purchase_order')}}</x-td>
			    <x-td type="th" size="9">{{$t('vendor')}}</x-td>
			    <x-td type="th" size="3">{{$t('amount_received')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/payments-made/${item.id}`)">
			   <x-td>{{item.number}}</x-td>
			    <x-td>{{item.payment_date | toDate}}</x-td>
			    <x-td>{{item.purchase_order.number}}</x-td>
			    <x-td>
			    	<span>{{item.vendor.name}}</span>
			    	<span v-if="item.vendor.organization"> - {{item.vendor.organization}}</span>
			    </x-td>
			    <x-td>{{item.net_amount | formatMoney}}</x-td>
			</x-tr>
		</x-mini>
		<x-mini :url="`expenses?vendor_id=${this.model.id}`" title="expenses">
			<x-tr slot="heading">
		    <x-td type="th" size="3">{{$t('number')}}</x-td>
			    <x-td type="th" size="3">{{$t('payment_date')}}</x-td>
			    <x-td type="th" size="6">{{$t('category')}}</x-td>
			    <x-td type="th" size="9">{{$t('vendor')}}</x-td>
			    <x-td type="th" size="3">{{$t('grand_total')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/expenses/${item.id}`)">
			   <x-td>{{item.number}}</x-td>
			    <x-td>{{item.payment_date | toDate}}</x-td>
			    <x-td>{{item.category.name}}</x-td>
			    <x-td>
			    	<span>{{item.vendor.name}}</span>
			    	<span v-if="item.vendor.organization"> - {{item.vendor.organization}}</span>
			    </x-td>
			    <x-td>{{item.grand_total | formatMoney}}</x-td>
			</x-tr>
		</x-mini>
	</div>
</template>
<script>
	import { showable } from '@js/lib/mixins'
	import { state } from '@js/shared/store'
	export default {
		mixins: [ showable ],
		data() {
			return {
				show: false,
				config: state.team_settings,
				model: {
					category: {}
				}
			}
		},
		methods: {

		}
	}
</script>