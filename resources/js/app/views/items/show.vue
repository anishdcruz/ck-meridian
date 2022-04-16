<template>
	<div class="content-inner" v-if="show">
		<x-panel padding margin>
			<div slot="title">
				<router-link to="/items">{{$t('item')}}</router-link> / {{model.code}}
			</div>
			<div slot="extra">
				<router-link :to="`/items`" class="btn btn-default btn-sm">
					<small class="icon icon-arrow-left-c"></small>
				</router-link>
				<router-link :to="`/items/${model.id}/edit`" class="btn btn-default btn-sm">
					<small class="icon icon-edit"></small>
				</router-link>
				<x-button size="sm" type="error" icon="trash-b" @click="removeDB('items', model.id)"></x-button>
			</div>
				<x-row line>
					<x-group col="8" label="code">
						<p>{{model.code}}</p>
					</x-group>
					<x-group col="8" label="category">
						<p v-if="model.category">
							{{model.category.name}}
						</p>
					</x-group>
					<x-group col="8" label="uom">
						<p v-if="model.uom">
							{{model.uom.name}}
						</p>
					</x-group>
			</x-row>
			<x-row line>
				<x-group col="8" label="description">
					<pre>{{model.description}}</pre>
				</x-group>
				<x-group col="8" label="unit_price">
					<p>{{model.unit_price | formatMoney}}</p>
				</x-group>
			</x-row>
			<x-row line>
				<x-group col="8" label="created_at">
					<pre>{{model.created_at | toDate}}</pre>
				</x-group>
			</x-row>
		</x-panel>
		<x-mini :url="`quotations?item_id=${this.model.id}`" title="quotations">
			<x-tr slot="heading">
			    <x-td type="th" size="3">{{$t('number')}}</x-td>
			    <x-td type="th" size="3">{{$t('issue_date')}}</x-td>
			    <x-td type="th" size="3">{{$t('expiry_date')}}</x-td>
			    <x-td type="th" size="9">{{$t('contact')}}</x-td>
			    <x-td type="th" size="3">{{$t('grand_total')}}</x-td>
			    <x-td type="th" size="3">{{$t('status')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/quotations/${item.id}`)">
			  	<x-td>{{item.number}}</x-td>
			  	<x-td>{{item.issue_date | toDate}}</x-td>
			  	<x-td>
			  		<span v-if="item.expiry_date">{{item.expiry_date | toDate}}</span>
			  		<span v-else>-</span>
			  	</x-td>
			  	<x-td>
			  		<span>{{item.contact.name}}</span>
			  		<span v-if="item.contact.organization"> - {{item.contact.organization}}</span>
			  	</x-td>
			  	<x-td>{{item.grand_total | formatMoney}}</x-td>
			  	<x-td>
			  		<span :class="`status status-${item.status.color}`">
			  			<span class="status-text">{{item.status.name}}</span>
			  		</span>
			  	</x-td>
			</x-tr>
		</x-mini>
		<x-mini :url="`invoices?item_id=${this.model.id}`" title="invoices">
			<x-tr slot="heading">
			    <x-td type="th" size="3">{{$t('number')}}</x-td>
			    <x-td type="th" size="3">{{$t('issue_date')}}</x-td>
			    <x-td type="th" size="3">{{$t('due_date')}}</x-td>
			    <x-td type="th" size="9">{{$t('contact')}}</x-td>
			    <x-td type="th" size="3">{{$t('grand_total')}}</x-td>
			    <x-td type="th" size="3">{{$t('status')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/invoices/${item.id}`)">
			  	<x-td>{{item.number}}</x-td>
			  	<x-td>{{item.issue_date | toDate}}</x-td>
			  	<x-td>
			  		<span v-if="item.due_date">{{item.due_date | toDate}}</span>
			  		<span v-else>-</span>
			  	</x-td>
			  	<x-td>
			  		<span>{{item.contact.name}}</span>
			  		<span v-if="item.contact.organization"> - {{item.contact.organization}}</span>
			  	</x-td>
			  	<x-td>{{item.grand_total | formatMoney}}</x-td>
			  	<x-td>
			  		<span :class="`status status-${item.status.color}`">
			  			<span class="status-text">{{item.status.name}}</span>
			  		</span>
			  	</x-td>
			</x-tr>
		</x-mini>
		<x-mini :url="`purchase-orders?item_id=${this.model.id}`" title="purchase_orders">
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
	</div>
</template>
<script>
	import { showable } from '@js/lib/mixins'
	export default {
		mixins: [ showable ],
		data() {
			return {
				show: false,
				model: {
					category: {}
				}
			}
		}
	}
</script>