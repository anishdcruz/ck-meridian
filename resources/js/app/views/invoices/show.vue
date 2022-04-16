<template>
	<div class="content-inner" v-if="show">
		<x-panel margin>
			<div slot="title">
				<router-link to="/invoices">{{$t('invoices')}}</router-link> / {{model.number}}
			</div>
			<div slot="extra">
				<router-link :to="`/invoices`" class="btn btn-default btn-sm">
					<small class="icon icon-arrow-left-c"></small>
				</router-link>
				<x-button icon="email" size="sm" @click="toggleModal"></x-button>
				<x-button v-if="model.balance_due > 0" type="primary" size="sm" @click="togglePaymentModal">{{$t('receive_payment')}}</x-button>
				<x-dropdown size="sm" dir="right" icon="more">
					<x-dropdown-menu slot="menu">
						<template v-if="model.all_statuses.length > 0">
							<x-dropdown-item v-for="(stage, i) in model.all_statuses"
								:key="stage.id"
								@click.native="markAs(stage.id)">
								{{$t('mark_as')}} {{stage.name}}
							</x-dropdown-item>

							<div class="dropdown-divide"></div>
						</template>

						<a target="_blank" :href="`/api/downloads/invoices/${model.id}?mode=preview`"
							class="dropdown-link">
						  <div class="dropdown-team-item">
						    <span class="dropdown-team-name">{{$t('view_pdf')}}</span>
						  </div>
						</a>

						<a target="_blank" :href="`/api/downloads/invoices/${model.id}`"
							class="dropdown-link">
						  <div class="dropdown-team-item">
						    <span class="dropdown-team-name">{{$t('download_pdf')}}</span>
						  </div>
						</a>

						<a target="_blank" :href="`/api/downloads/delivery-notes/${model.id}?mode=preview`"
							class="dropdown-link">
						  <div class="dropdown-team-item">
						    <span class="dropdown-team-name">{{$t('view_delivery_note')}}</span>
						  </div>
						</a>
						<x-dropdown-link divide :to="`/invoices/${model.id}/edit`">
							{{$t('edit')}}
						</x-dropdown-link>

						<x-dropdown-link :to="`/invoices/${model.id}/clone`">
							{{$t('clone')}}
						</x-dropdown-link>

						<x-dropdown-item divide>
							<a @click.stop="removeDB('invoices', model.id)">{{$t('delete')}}</a>
						</x-dropdown-item>
					</x-dropdown-menu>
				</x-dropdown>
			</div>
			<div>
				<div class="document">
				    <div class="document-heading">
				    	<x-row>
				    	    <x-col span="8">
				    	        <p><strong>{{$t('to')}}:</strong></p>
				    	        <div>
				    	            <router-link :to="`/contacts/${model.contact_id}`">
				    	                {{model.contact.name}}
				    	            </router-link><br>
				    	            <span v-if="model.contact.organization">
				    	                {{model.contact.organization}}<br>
				    	            </span>
				    	            <pre>{{model.contact.primary_address}}</pre>
				    	            <span v-if="Number(config.tax_enable)">
				    	                <span>{{config.registration_label}}: </span>
				    	                {{model.contact.tax_id}}
				    	            </span>
				    	        </div>
				    	    </x-col>
				    	    <x-col offset="6" span="10">
				    	        <table class="document-summary">
				    	            <tbody>
				    	                <tr>
				    	                    <td>{{$t('number')}}:</td>
				    	                    <td>{{model.number}}</td>
				    	                </tr>
				    	                <tr>
				    	                    <td>{{$t('issue_date')}}:</td>
				    	                    <td>{{model.issue_date | toDate}}</td>
				    	                </tr>
				    	                <tr>
				    	                    <td>{{$t('due_date')}}:</td>
				    	                    <td>{{model.due_date | toDate}}</td>
				    	                </tr>
				    	                <tr v-if="model.reference">
				    	                    <td>{{$t('reference')}}:</td>
				    	                    <td>{{model.reference}}</td>
				    	                </tr>
				    	                <tr v-if="model.quotation">
				    	                    <td>{{$t('quotation')}}:</td>
				    	                    <td>
				    	                    	<router-link :to="`/quotations/${model.quotation_id}`">{{model.quotation.number}}</router-link>
				    	                    </td>
				    	                </tr>
				    	                <tr>
				    	                    <td>{{$t('grand_total')}}:</td>
				    	                    <td>{{model.grand_total | formatMoney}}</td>
				    	                </tr>
				    	                <tr>
				    	                	<td>{{$t('status')}}:</td>
				    	                	<td>
				    	                		<span :class="`status status-${model.status.color}`">
				    	                			<span class="status-text">{{model.status.name}}</span>
				    	                		</span>
				    	                	</td>
				    	                </tr>
				    	            </tbody>
				    	        </table>
				    	    </x-col>
				    	</x-row>
				    </div>
				    <div class="document-body">
				    	<table class="document-table">
				    	    <thead>
				    	        <tr>
				    	            <x-td type="th" size="3">{{$t('code')}}</x-td>
				    	            <x-td align="center" type="th" size="10">{{$t('description')}}</x-td>
				    	            <x-td align="center" type="th" size="1">{{$t('uom')}}</x-td>
				    	            <x-td align="center" type="th" size="3">{{$t('unit_price')}}</x-td>
				    	            <x-td align="center" type="th" size="1">{{$t('qty')}}</x-td>
				    	            <x-td align="center" type="th" size="4">{{$t('total')}}</x-td>
				    	        </tr>
				    	    </thead>
				    	    <tbody>
				    	        <tr v-for="item in model.lines">
				    	            <x-td>{{item.item.code}}</x-td>
				    	            <x-td>
				    	                <router-link :to="`/items/${item.item_id}`">
				    	                    <pre>{{item.item.description}}</pre>
				    	                </router-link>
				    	            </x-td>
				    	            <x-td align="center">{{item.item.uom && item.item.uom.name}}</x-td>
				    	            <x-td align="right">
				    	                {{item.unit_price | formatMoney(false)}}
				    	            </x-td>
				    	            <x-td align="center">{{item.qty}}</x-td>
				    	            <x-td align="right">
				    	                {{item.unit_price * item.qty |  formatMoney(false)}}
				    	            </x-td>
				    	        </tr>
				    	    </tbody>
				    	    <tfoot>
				    	        <tr>
				    	            <x-td colspan="3"></x-td>
				    	            <x-td align="right" colspan="2">{{$t('sub_total')}}:</x-td>
				    	            <x-td align="right">{{model.sub_total | formatMoney(false)}}</x-td>
				    	        </tr>
				    	        <tr v-if="Number(config.enable_discount)">
				    	            <x-td colspan="3"></x-td>
				    	            <x-td align="right" colspan="2">{{$t('discount')}}:</x-td>
				    	            <x-td align="right">-{{model.discount | formatMoney(false)}}</x-td>
				    	        </tr>
				    	        <template v-if="Number(config.tax_enable)">
				    	            <tr>
				    	                <x-td colspan="3"></x-td>
				    	                <x-td align="right" colspan="2">
				    	                    {{config.tax_label}} @ {{model.tax}} %
				    	                </x-td>
				    	                <x-td align="right">{{model.tax_total | formatMoney(false)}}</x-td>
				    	            </tr>
				    	            <tr v-if="Number(config.tax_2_enable)">
				    	                <x-td colspan="3"></x-td>
				    	                <x-td align="right" colspan="2">
				    	                    {{config.tax_2_label}} @ {{model.tax_2}} %
				    	                </x-td>
				    	                <x-td align="right">{{model.tax_2_total | formatMoney(false)}}</x-td>
				    	            </tr>
				    	        </template>
				    	        <tr>
				    	            <x-td colspan="3"></x-td>
				    	            <x-td align="right" colspan="2">
				    	                <strong>{{$t('grand_total')}}:</strong>
				    	            </x-td>
				    	            <x-td align="right">
				    	                <strong>{{model.grand_total | formatMoney}}</strong>
				    	            </x-td>
				    	        </tr>
				    	        <tr v-if="model.amount_paid > 0">
				    	            <x-td colspan="3"></x-td>
				    	            <x-td align="right" colspan="2">
				    	                <span>{{$t('amount_paid')}}:</span>
				    	            </x-td>
				    	            <x-td align="right">
				    	                <span>{{model.amount_paid | formatMoney}}</span>
				    	            </x-td>
				    	        </tr>
				    	    </tfoot>
				    	  </table>
				    </div>
				    <div class="document-footer">
				    	<x-row>
				    	    <x-col span="16">
				    	        <strong>{{$t('terms')}}</strong>
				    	        <pre>{{model.term.description}}</pre>
				    	    </x-col>
				    	</x-row>
				    </div>
				</div>
			</div>
		</x-panel>
		<x-mini :url="`payments?invoice_id=${this.model.id}`" title="payments">
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
		<email-modal v-if="showModal" :id="model.id" type="invoices" @cancel="toggleModal" @ok="onSaved"></email-modal>
		<receive-payment-modal v-if="showPaymentModal" :amount="model.balance_due"
			:id="model.id" @ok="onSaved" @cancel="togglePaymentModal"></receive-payment-modal>
	</div>
</template>
<script>
	import { showable } from '@js/lib/mixins'
	import { state } from '@js/shared/store'

	import ReceivePaymentModal from '@js/partials/ReceivePaymentModal.vue'
	import EmailModal from '@js/partials/EmailModal.vue'
	export default {
		mixins: [ showable ],
		components: { EmailModal, ReceivePaymentModal },
		data() {
			return {
				showPaymentModal: false,
				showModal: false,
				show: false,
				model: {
					lines: [],
					contact: {},
					terms: {},
					status: {},
					all_statuses: []
				},
				config: state.team_settings
			}
		},
		methods: {
			togglePaymentModal() {
				this.showPaymentModal = !this.showPaymentModal
			},
			onSaved() {
				this.showModal = false
				this.showPaymentModal = false
				const id = Math.random().toString(36).substring(7)
				this.$router.push(`?id=${id}`)
			},
			toggleModal() {
				this.showModal = !this.showModal
			},
			markAs(type) {
				this.$bar.start()
				this.$http.post(`/api/mark-as/invoices/${this.model.id}`, {type: type})
					.then((res) => {
						if(res.data.saved) {
							this.$set(this.model, 'status', res.data.status)
						}
					})
					.finally((res) => {
						this.$bar.finish()
					})
			}
		}
	}
</script>