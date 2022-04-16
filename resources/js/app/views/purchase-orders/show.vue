<template>
	<div class="content-inner" v-if="show">
		<x-panel padding margin>
			<div slot="title">
				<router-link to="/purchase-orders">{{$t('purchase_orders')}}</router-link> / {{model.number}}
			</div>
			<div slot="extra">
				<router-link :to="`/purchase-orders`" class="btn btn-default btn-sm">
					<small class="icon icon-arrow-left-c"></small>
				</router-link>
				<x-button icon="email" size="sm" @click="toggleModal"></x-button>
				<x-button v-if="model.balance_due > 0" type="primary" size="sm" @click="togglePaymentModal">{{$t('payment_made')}}</x-button>
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

						<a target="_blank" :href="`/api/downloads/purchase-orders/${model.id}?mode=preview`"
							class="dropdown-link">
						  <div class="dropdown-team-item">
						    <span class="dropdown-team-name">{{$t('view_pdf')}}</span>
						  </div>
						</a>

						<a target="_blank" :href="`/api/downloads/purchase-orders/${model.id}`"
							class="dropdown-link">
						  <div class="dropdown-team-item">
						    <span class="dropdown-team-name">{{$t('download_pdf')}}</span>
						  </div>
						</a>
						<x-dropdown-link divide :to="`/purchase-orders/${model.id}/edit`">
							{{$t('edit')}}
						</x-dropdown-link>

						<x-dropdown-link :to="`/purchase-orders/${model.id}/clone`">
							{{$t('clone')}}
						</x-dropdown-link>

						<x-dropdown-item divide>
							<a @click.stop="removeDB('purchase-orders', model.id)">{{$t('delete')}}</a>
						</x-dropdown-item>
					</x-dropdown-menu>
				</x-dropdown>
			</div>
			<div v-if="model.foreign_currency">
				<x-row line>
					<x-group col="8" label="local_currency">
						<p>{{current_team.currency.code}}</p>
					</x-group>
					<x-group col="8" label="foreign_currency">
						<p>{{model.foreign_currency.code}}</p>
					</x-group>
					<x-group col="8" label="exchange_rate">
						<p>{{model.exchange_rate || 0}}</p>
					</x-group>
				</x-row>
				<x-row line>
					<x-group col="8" label="local_currency">
						<p>{{model.grand_total * model.exchange_rate | toMoney(current_team.currency)}}</p>
					</x-group>
					<x-group col="8" label="foreign_currency">
						<p>{{model.grand_total | toMoney(model.foreign_currency)}}</p>
					</x-group>
				</x-row>
			</div>
		</x-panel>
		<x-panel margin>
			<div>
				<div class="document">
				    <div class="document-heading">
				    	<x-row>
				    	    <x-col span="8">
				    	        <p><strong>{{$t('to')}}:</strong></p>
				    	        <div>
				    	            <router-link :to="`/vendors/${model.vendor_id}`">
				    	                {{model.vendor.name}}
				    	            </router-link><br>
				    	            <span v-if="model.vendor.organization">
				    	                {{model.vendor.organization}}<br>
				    	            </span>
				    	            <pre>{{model.vendor.primary_address}}</pre>
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
				    	                    <td>{{$t('grand_total')}}:</td>
				    	                    <td>{{model.grand_total | toMoney(currentMoney)}}</td>
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
				    	                {{item.unit_price | toMoney(currentMoney, false)}}
				    	            </x-td>
				    	            <x-td align="center">{{item.qty}}</x-td>
				    	            <x-td align="right">
				    	                {{item.unit_price * item.qty |  toMoney(currentMoney, false)}}
				    	            </x-td>
				    	        </tr>
				    	    </tbody>
				    	    <tfoot>
				    	        <tr>
				    	            <x-td colspan="3"></x-td>
				    	            <x-td align="right" colspan="2">{{$t('sub_total')}}:</x-td>
				    	            <x-td align="right">{{model.sub_total | toMoney(currentMoney, false)}}</x-td>
				    	        </tr>
				    	        <tr v-if="Number(config.enable_discount)">
				    	            <x-td colspan="3"></x-td>
				    	            <x-td align="right" colspan="2">{{$t('discount')}}:</x-td>
				    	            <x-td align="right">-{{model.discount | toMoney(currentMoney, false)}}</x-td>
				    	        </tr>
				    	        <template v-if="Number(config.tax_enable)">
				    	            <tr>
				    	                <x-td colspan="3"></x-td>
				    	                <x-td align="right" colspan="2">
				    	                    {{config.tax_label}} @ {{model.tax}} %
				    	                </x-td>
				    	                <x-td align="right">{{model.tax_total | toMoney(currentMoney, false)}}</x-td>
				    	            </tr>
				    	            <tr v-if="Number(config.tax_2_enable)">
				    	                <x-td colspan="3"></x-td>
				    	                <x-td align="right" colspan="2">
				    	                    {{config.tax_2_label}} @ {{model.tax_2}} %
				    	                </x-td>
				    	                <x-td align="right">{{model.tax_2_total | toMoney(currentMoney, false)}}</x-td>
				    	            </tr>
				    	        </template>
				    	        <tr>
				    	            <x-td colspan="3"></x-td>
				    	            <x-td align="right" colspan="2">
				    	                <strong>{{$t('grand_total')}}:</strong>
				    	            </x-td>
				    	            <x-td align="right">
				    	                <strong>{{model.grand_total | toMoney(currentMoney)}}</strong>
				    	            </x-td>
				    	        </tr>
				    	        <tr v-if="model.amount_paid > 0">
				    	            <x-td colspan="3"></x-td>
				    	            <x-td align="right" colspan="2">
				    	                <span>{{$t('amount_paid')}}:</span>
				    	            </x-td>
				    	            <x-td align="right">
				    	                <span>{{model.amount_paid | toMoney(currentMoney)}}</span>
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
		<x-mini :url="`payments-made?purchase_order_id=${this.model.id}`" title="payments_made">
			<x-tr slot="heading">
		    <x-td type="th" size="3">{{$t('number')}}</x-td>
			    <x-td type="th" size="3">{{$t('payment_date')}}</x-td>
			    <x-td type="th" size="6">{{$t('purchase_order')}}</x-td>
			    <x-td type="th" size="9">{{$t('vendor')}}</x-td>
			    <x-td type="th" size="3">{{$t('amount_paid')}}</x-td>
			</x-tr>
			<x-tr slot-scope="{ item }" @click.native="$router.push(`/payments-made/${item.id}`)">
			   <x-td>{{item.number}}</x-td>
			    <x-td>{{item.payment_date | toDate}}</x-td>
			    <x-td>{{item.purchase_order.number}}</x-td>
			    <x-td>
			    	<span>{{item.vendor.name}}</span>
			    	<span v-if="item.vendor.organization"> - {{item.vendor.organization}}</span>
			    </x-td>
			    <x-td>{{item.amount_paid | toMoney(currentMoney)}}</x-td>
			</x-tr>
		</x-mini>
		<email-modal v-if="showModal" :id="model.id" type="purchase-orders" @cancel="toggleModal" @ok="onSaved"></email-modal>
		<payment-made-modal v-if="showPaymentModal"
			:amount="getAmountDue"
			:id="model.id" @ok="onSaved" @cancel="togglePaymentModal"></payment-made-modal>
	</div>
</template>
<script>
	import { showable } from '@js/lib/mixins'
	import { state } from '@js/shared/store'

	import EmailModal from '@js/partials/EmailModal.vue'
	import PaymentMadeModal from '@js/partials/PaymentMadeModal.vue'

	export default {
		mixins: [ showable ],
		components: { EmailModal, PaymentMadeModal },
		data() {
			return {
				showPaymentModal: false,
				showModal: false,
				show: false,
				model: {
					lines: [],
					vendor: {},
					terms: {},
					status: {},
					all_statuses: [],
					foreign_currency: {}
				},
				config: state.team_settings,
				current_team: state.current_team
			}
		},
		computed: {
			currentMoney() {
				return this.model.foreign_currency || this.current_team.currency
			},
			getAmountDue() {
				return Number(this.$options.filters.formatMoneyNumber(this.model.balance_due * this.model.exchange_rate, false))
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
				this.$http.post(`/api/mark-as/purchase-orders/${this.model.id}`, {type: type})
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