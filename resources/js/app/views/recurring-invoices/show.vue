<template>
	<div class="content-inner" v-if="show">
		<x-panel padding margin>
			<div slot="title">
				<router-link to="/recurring-invoices">{{$t('recurring_invoices')}}</router-link> / {{model.title}}
			</div>
			<div slot="extra">
				<router-link :to="`/recurring-invoices`" class="btn btn-default btn-sm">
					<small class="icon icon-arrow-left-c"></small>
				</router-link>
				<x-dropdown size="sm" dir="right" icon="more">
					<x-dropdown-menu slot="menu">
						<x-dropdown-link :to="`/recurring-invoices/${model.id}/edit`">
							{{$t('edit')}}
						</x-dropdown-link>

						<x-dropdown-link :to="`/recurring-invoices/${model.id}/clone`">
							{{$t('clone')}}
						</x-dropdown-link>

						<x-dropdown-item divide>
							<a @click.stop="removeDB('recurring-invoices', model.id)">{{$t('delete')}}</a>
						</x-dropdown-item>
					</x-dropdown-menu>
				</x-dropdown>
			</div>
			<div>
				<x-row line>
					<x-group col="8" label="start_date">
						<p>{{model.start_date | toDate}}</p>
					</x-group>
					<x-group col="8" label="end_date">
						<p v-if="model.end_date">{{model.end_date | toDate}}</p>
						<p v-else>-</p>
					</x-group>
					<x-group col="8" label="never_expiry">
						<p v-if="model.never_expiry">{{$t('yes')}}</p>
						<p v-else>{{$t('no')}}</p>
					</x-group>
					<x-group col="8" label="next_date">
						<p v-if="model.next_date">{{model.next_date | toDate}}</p>
						<p v-else>-</p>
					</x-group>
					<x-group col="8" label="last_generated_date">
						<p v-if="model.last_generated_date">{{model.last_generated_date | toDate}}</p>
						<p v-else>-</p>
					</x-group>
				</x-row>
				<x-row line>
					<x-group col="8" label="frequency">
						<pre>{{$t(model.frequency)}}</pre>
					</x-group>
					<x-group col="8" label="send_on">
						<pre>{{getSendOn(model)}}</pre>
					</x-group>
					<x-group col="8" label="send_at">
						<pre>{{getTime(model.send_at)}}</pre>
					</x-group>
				</x-row>
				<x-row line>
					<x-group col="8" label="due_date_after_days">
						<pre>{{model.due_after}} {{$t('days')}}</pre>
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
				    	                    <td>-</td>
				    	                </tr>
				    	                <tr>
				    	                    <td>{{$t('issue_date')}}:</td>
				    	                    <td>-</td>
				    	                </tr>
				    	                <tr>
				    	                    <td>{{$t('due_date')}}:</td>
				    	                    <td>-</td>
				    	                </tr>
				    	                <tr>
				    	                    <td>{{$t('grand_total')}}:</td>
				    	                    <td>{{model.grand_total | formatMoney}}</td>
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
	</div>
</template>
<script>
	import { showable } from '@js/lib/mixins'
	import { state } from '@js/shared/store'

	export default {
		mixins: [ showable ],
		data() {
			return {
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
			onSaved() {
				this.showModal = false
				const id = Math.random().toString(36).substring(7)
				this.$router.push(`?id=${id}`)
			},
			toggleModal() {
				this.showModal = !this.showModal
			},
			getSendOn(item) {
				console.log(item.send_on, item.send_at)
			    if(item.frequency === 'weekly') {
			        let weeks = {
			            '1': this.$t('monday'),
			            '2': this.$t('tuesday'),
			            '3': this.$t('wednesday'),
			            '4': this.$t('thursday'),
			            '5': this.$t('friday'),
			            '6': this.$t('saturday'),
			            '7': this.$t('sunday'),
			        }

			        return weeks[item.send_on]
			    } else if(item.frequency === 'monthly') {
			        let list = {
			            '1': '1st',
			            '2': '2nd',
			            '3': '3rd',
			            '4': '4th',
			            '5': '5th',
			            '6': '6th',
			            '7': '7th',
			            '8': '8th',
			            '9': '9th',
			            '10': '10th',
			            '11': '11th',
			            '12': '12th',
			            '13': '13th',
			            '14': '14th',
			            '15': '15th',
			            '16': '16th',
			            '17': '17th',
			            '18': '18th',
			            '19': '19th',
			            '20': '20th',
			            '21': '21st',
			            '22': '22nd',
			            '23': '23rd',
			            '24': '24th',
			            '25': '25th',
			            '26': '26th',
			            '27': '27th',
			            '28': '28th',
			            '29': '29th',
			            '30': '30th',
			            '31': '31st'
			        }
			        return list[item.send_on]
			    } else {
			        return '-'
			    }
			},
			getTime(time) {
			    let times = {

			        '1:00': '1:00 AM',
			        '1:30': '1:30 AM',
			        '2:00': '2:00 AM',
			        '2:30': '2:30 AM',
			        '3:00': '3:00 AM',
			        '3:30': '3:30 AM',
			        '4:00': '4:00 AM',
			        '4:30': '4:30 AM',
			        '5:00': '5:00 AM',
			        '5:30': '5:30 AM',
			        '6:00': '6:00 AM',
			        '6:30': '6:30 AM',
			        '7:00': '7:00 AM',
			        '7:30': '7:30 AM',
			        '8:00': '8:00 AM',
			        '8:30': '8:30 AM',
			        '9:00': '9:00 AM',
			        '9:30': '9:30 AM',
			        '10:00': '10:00 AM',
			        '10:30': '10:30 AM',
			        '11:00': '11:00 AM',
			        '11:30': '11:30 AM',
			        '12:00': '12:00 PM',
			        '12:30': '12:30 PM',
			        '13:00': '1:00 PM',
			        '13:30': '1:30 PM',
			        '14:00': '2:00 PM',
			        '14:30': '2:30 PM',
			        '15:00': '3:00 PM',
			        '15:30': '3:30 PM',
			        '16:00': '4:00 PM',
			        '16:30': '4:30 PM',
			        '17:00': '5:00 PM',
			        '17:30': '5:30 PM',
			        '18:00': '6:00 PM',
			        '18:30': '6:30 PM',
			        '19:00': '7:00 PM',
			        '19:30': '7:30 PM',
			        '20:00': '8:00 PM',
			        '20:30': '8:30 PM',
			        '21:00': '9:00 PM',
			        '21:30': '9:30 PM',
			        '22:00': '10:00 PM',
			        '22:30': '10:30 PM',
			        '23:00': '11:00 PM',
			        '23:30': '11:30 PM'
			    }

			    return times[time]
			},
		}
	}
</script>