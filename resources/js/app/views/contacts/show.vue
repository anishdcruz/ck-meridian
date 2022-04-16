a<template>
	<div class="content-inner" v-if="show">
		<x-panel padding margin>
			<div slot="title">
				<router-link to="/contacts">{{$t('contact')}}</router-link> / {{model.name}}
				<small>({{model.number}})</small>
			</div>
			<div slot="extra">
				<router-link :to="`/contacts`" class="btn btn-default btn-sm">
					<small class="icon icon-arrow-left-c"></small>
				</router-link>
				<router-link :to="`/contacts/${model.id}/edit`" class="btn btn-default btn-sm">
					<small class="icon icon-edit"></small>
				</router-link>
				<x-button size="sm" type="error" icon="trash-b" @click="removeDB('contacts', model.id)"></x-button>
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
			<x-group col="8" :label="config.registration_label" v-if="Number(config.tax_enable)">
				<pre>{{model.tax_id || '-'}}</pre>
			</x-group>
		</x-row>
		<x-row line>
			<x-group col="8" label="created_at">
				<pre>{{model.created_at | toDate}}</pre>
			</x-group>
		</x-row>
		</x-panel>
		<x-mini :url="`quotations?contact_id=${this.model.id}`" title="quotations">
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
		<x-mini :url="`invoices?contact_id=${this.model.id}`" title="invoices">
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
		<x-mini :url="`payments?contact_id=${this.model.id}`" title="payments">
			<x-tr slot="heading">
		    <x-td type="th" size="3">{{$t('number')}}</x-td>
			    <x-td type="th" size="3">{{$t('payment_date')}}</x-td>
			    <x-td type="th" size="3">{{$t('invoice')}}</x-td>
			    <x-td type="th" size="9">{{$t('contact')}}</x-td>
			    <x-td type="th" size="3">{{$t('amount_received')}}</x-td>
			    <x-td type="th" size="3">{{$t('status')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/payments/${item.id}`)">
			   <x-td>{{item.number}}</x-td>
			    <x-td>{{item.payment_date | toDate}}</x-td>
			    <x-td>{{item.invoice.number}}</x-td>
			    <x-td>
			    	<span>{{item.contact.name}}</span>
			    	<span v-if="item.contact.organization"> - {{item.contact.organization}}</span>
			    </x-td>
			    <x-td>{{item.amount_received | formatMoney}}</x-td>
			    <x-td>
			    	<span :class="`status status-${item.status}`">
			    		<span class="status-text">{{item.status}}</span>
			    	</span>
			    </x-td>
			</x-tr>
		</x-mini>
		<x-mini :url="`refunds?contact_id=${this.model.id}`" title="refunds">
			<x-tr slot="heading">
		    <x-td type="th" size="3">{{$t('number')}}</x-td>
			    <x-td type="th" size="3">{{$t('refund_date')}}</x-td>
			    <x-td type="th" size="3">{{$t('payment')}}</x-td>
			    <x-td type="th" size="9">{{$t('contact')}}</x-td>
			    <x-td type="th" size="3">{{$t('amount')}}</x-td>
			    <x-td type="th" size="3">{{$t('status')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/refunds/${item.id}`)">
			   <x-td>{{item.number}}</x-td>
			    <x-td>{{item.refund_date | toDate}}</x-td>
			    <x-td>{{item.payment.number}}</x-td>
			    <x-td>
			    	<span>{{item.contact.name}}</span>
			    	<span v-if="item.contact.organization"> - {{item.contact.organization}}</span>
			    </x-td>
			    <x-td>{{item.amount | formatMoney}}</x-td>
			    <x-td>
			    	<span :class="`status status-${item.status}`">
			    		<span class="status-text">{{item.status}}</span>
			    	</span>
			    </x-td>
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