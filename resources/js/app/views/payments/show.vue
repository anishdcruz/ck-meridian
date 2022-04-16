<template>
	<div class="content-inner" v-if="show">
		<x-panel padding margin>
			<div slot="title">
				<router-link to="/payments">{{$t('payment')}}</router-link> / {{model.name}}
				<small>({{model.number}})</small>
			</div>
			<div slot="extra">
				<router-link :to="`/payments`" class="btn btn-default btn-sm">
					<small class="icon icon-arrow-left-c"></small>
				</router-link>
				<x-button icon="email" size="sm" @click="toggleModal"></x-button>
				<x-dropdown size="sm" dir="right" icon="more">
					<x-dropdown-menu slot="menu">
						<template  v-if="model.available_amount > 0">
							<x-dropdown-item  @click.native="toggleRefundModal">
								{{$t('refund')}}
							</x-dropdown-item>

							<div class="dropdown-divide"></div>
						</template>

						<a target="_blank" :href="`/api/downloads/payments/${model.id}?mode=preview`"
							class="dropdown-link">
						  <div class="dropdown-team-item">
						    <span class="dropdown-team-name">{{$t('view_pdf')}}</span>
						  </div>
						</a>

						<a target="_blank" :href="`/api/downloads/payments/${model.id}`"
							class="dropdown-link">
						  <div class="dropdown-team-item">
						    <span class="dropdown-team-name">{{$t('download_pdf')}}</span>
						  </div>
						</a>

						<x-dropdown-item divide>
							<a @click.stop="removeDB('payments', model.id)">{{$t('delete')}}</a>
						</x-dropdown-item>
					</x-dropdown-menu>
				</x-dropdown>
			</div>
			<x-row line>
				<x-group col="8" label="number">
					<p>{{model.number}}</p>
				</x-group>
				<x-group col="8" label="contact">
					<router-link :to="`/contacts/${model.contact_id}`">
					    {{model.contact.name}}
					    <span v-if="model.contact.organization">
					        - {{model.contact.organization}}<br>
					    </span>
					</router-link>

				</x-group>
				<x-group col="8" label="invoice">
					<router-link :to="`/invoices/${model.invoice_id}`">
					    {{model.invoice.number}}
					</router-link>
				</x-group>
		</x-row>

		<x-row line>
			<x-group col="8" label="payment_date">
				<p>{{model.payment_date | toDate}}</p>
			</x-group>
			<x-group col="8" label="charge_id">
				<p>{{model.charge_id || '-'}}</p>
			</x-group>
			<x-group col="8" label="status">
				<p v-if="model.status">
					<span :class="`status status-${model.status}`">
						<span class="status-text">{{model.status}}</span>
					</span>
				</p>
				<p v-else>-</p>
			</x-group>
		</x-row>
		<x-row line>
			<x-group col="8" label="amount_received">
				<p>{{model.amount_received | formatMoney}}</p>
			</x-group>
			<x-group col="8" label="transaction_fees">
				<p>{{model.transaction_fees | formatMoney}}</p>
			</x-group>
			<x-group col="8" label="net_amount">
				<p>{{model.net_amount | formatMoney}}</p>
			</x-group>
			<x-group col="8" label="amount_refunded">
				<p>{{model.amount_refunded | formatMoney}}</p>
			</x-group>
			<x-group col="8" label="available_amount">
				<p>{{model.available_amount | formatMoney}}</p>
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

		<x-mini :url="`refunds?payment_id=${this.model.id}`" title="refunds">
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
		<refund-modal v-if="showRfModal" :amount="model.available_amount"
			:id="model.id" @cancel="toggleRefundModal"></refund-modal>
			<email-modal v-if="showModal" :id="model.id" type="payments" @cancel="toggleModal" @ok="onSaved"></email-modal>
	</div>
</template>
<script>
	import { showable } from '@js/lib/mixins'
	import { state } from '@js/shared/store'
	import RefundModal from '@js/partials/RefundModal.vue'
	import EmailModal from '@js/partials/EmailModal.vue'
	export default {
		components: { RefundModal, EmailModal },
		mixins: [ showable ],
		data() {
			return {
				show: false,
				config: state.team_settings,
				model: {
					category: {}
				},
				showRfModal: false,
				showModal: false,
			}
		},
		methods: {
			toggleRefundModal() {
				this.showRfModal = !this.showRfModal
			},
			onSaved() {
				this.showModal = false
				const id = Math.random().toString(36).substring(7)
				this.$router.push(`?id=${id}`)
			},
			toggleModal() {
				this.showModal = !this.showModal
			},
		}
	}
</script>