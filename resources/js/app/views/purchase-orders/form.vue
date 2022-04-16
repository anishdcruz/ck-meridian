<template>
	<div class="content-inner" v-if="show">
		<x-panel padding margin>
			<div slot="title">
				{{$t(mode)}} {{$t('purchase_order')}}
			</div>
			<x-row line>
				<x-form-group col="8" v-model="form.number" :label="$t('number')" disabled></x-form-group>
				<x-form-group col="16" :label="$t('vendor')" :errors="errors.vendor_id">
					<x-quick-vendor slot="quick" :source="form.vendor"
						:title="$t('vendor')" url="vendors"
						@new="value => { form.vendor_id = value.id; form.vendor = value }"
						></x-quick-vendor>
					<x-typeahead-table :value="form.vendor" url="vendors" name="name"
						:columns="['number', 'name', 'organization']"
					  @input="onVendor">
					</x-typeahead-table>
				</x-form-group>
			</x-row>
			<x-row line>
				<x-form-group col="8" type="date" v-model="form.issue_date" :label="$t('issue_date')" :errors="errors.issue_date"></x-form-group>
				<x-form-group col="8" v-model="form.exchange_rate"
					:label="`1 ${form.foreign_currency.code} = ? ${c_team.currency.code}`" :errors="errors.exchange_rate"
				 	v-if="form.foreign_currency_id"></x-form-group>
				<x-form-group col="8" v-model="form.reference" :label="$t('reference')" :errors="errors.reference" optional></x-form-group>
			</x-row>
			<x-row line>
				<x-table class="table-form">
				  <x-thead>
				    <x-tr>
				      <x-td type="th" size="13">
			          <div class="flex flex-between">
			              {{$t('item_description')}}
			              <x-quick-item slot="quick" :title="$t('item')" url="items"
			              	:source="source"
			              	@new="onNewItem"
			              	@copy="onCopyItem"
			              	></x-quick-item>
			          </div>
				      </x-td>
				      <x-td type="th" size="2">{{$t('uom.name')}}</x-td>
				      <x-td type="th" size="3">{{$t('unit_price')}}</x-td>
				      <x-td type="th" size="2">{{$t('qty')}}</x-td>
				      <x-td type="th" size="4">{{$t('total')}}</x-td>
				    </x-tr>
				  </x-thead>
				  <x-tbody>
				  	<template v-for="(line, index) in form.lines">
				  	<x-tr>
				  		<x-td>
		  	  			<x-table-line :value="line.item" url="items"
		  	  				name="full_code"
  							:columns="['code', 'description', 'price']"
  							@input="onItem(line, index, $event)">
  							</x-table-line>
  							<small class="error-control" v-if="errors[`lines.${index}.item_id`]">
  							    {{errors[`lines.${index}.item_id`][0]}}
  							</small>
				  		</x-td>
				  		<x-td align="center">
				  		    <span class="form-input">{{line.item && line.item.uom && line.item.uom.name || '-'}}</span>
				  		    <small class="error-control" v-if="errors[`lines.${index}.uom`]">
				  		        {{errors[`lines.${index}.uom`][0]}}
				  		    </small>
				  		</x-td>
				  		<x-td align="right">
				  		    <input type="text" class="form-input" v-model="line.unit_price">
				  		    <small class="error-control" v-if="errors[`lines.${index}.unit_price`]">
				  		        {{errors[`lines.${index}.unit_price`][0]}}
				  		    </small>
				  		</x-td>
				  		<x-td align="center">
				  		    <input type="text" class="form-input" v-model="line.qty">
				  		    <small class="error-control" v-if="errors[`lines.${index}.qty`]">
				  		        {{errors[`lines.${index}.qty`][0]}}
				  		    </small>
				  		</x-td>
				  		<x-td align="right">
				  		    <span class="form-input">
				  		        {{Number(line.qty) * Number(line.unit_price) | toMoney(currency, false)}}
				  		    </span>
				  		</x-td>
				  		<x-td>
				  		    <span class="form-line-controls">
				  		      <i :title="$t('copy')" class="form-line-blue icon icon-ios-copy" @click="setCopy(line, index)"></i>
				  		      <i :title="$t('delete')" class="form-line-red icon icon-trash-a" @click="removeItem(line, index)"></i>
				  		    </span>
				  		</x-td>
				  	</x-tr>
				  	</template>
				  </x-tbody>
				  <tfoot>
			      <tr>
			          <x-td colspan="1">
			              <x-button size="sm" icon="plus" @click="addNewLine">
			                  {{$t('add_new_line')}}
			              </x-button>
			          </x-td>
			          <x-td align="right" colspan="3">
			              <span class="form-total">{{$t('sub_total')}}:</span>
			          </x-td>
			          <x-td align="right">
			              <span class="form-total">{{subTotal | toMoney(currency, false)}}</span>
			          </x-td>
			      </tr>
			      <tr v-if="Number(config.enable_discount)">
		          <x-td align="right" colspan="4">
		              <span class="form-total">
		                  {{$t('discount')}}
		                  <input type="text"
		                  		class="form-input form-input-sm"
		                      style="width: 55px;"
		                      v-model="form.discount">
		                  :
		              </span>
		          </x-td>
		          <x-td align="right">
		              <span class="form-total">-{{form.discount | toMoney(currency, false)}}</span>
		              <small class="error-control" v-if="errors.discount">
		                  {{errors.discount[0]}}
		              </small>
		          </x-td>
			      </tr>
			      <tr v-if="Number(config.enable_discount) && Number(config.tax_enable)">
		          <x-td align="right" colspan="4">
		              <span class="form-total">{{$t('sub_total_after_discount')}}:</span>
		          </x-td>
		          <x-td align="right">
		              <span class="form-total">{{subTotalAfterDiscount | toMoney(currency, false)}}</span>
		          </x-td>
			      </tr>
			      <template v-if="Number(config.tax_enable)">
			          <tr>
			              <x-td align="right" colspan="4">
			                  <span class="form-total">
			                      {{config.tax_label}}
			                      <input type="text"
			                      class="form-input form-input-sm"
		                      	style="width: 55px;"
		                       	v-model="form.tax">
			                      % :
			                  </span>
			                  <small class="error-control" v-if="errors.tax">
			                      {{errors.tax[0]}}
			                  </small>
			              </x-td>
			              <x-td align="right">
			                  <span class="form-total">{{form.tax_total | toMoney(currency, false)}}</span>
			                  <small class="error-control" v-if="errors.tax_total">
			                      {{errors.tax_total[0]}}
			                  </small>
			              </x-td>
			          </tr>
			          <tr v-if="Number(config.tax_2_enable)">
			              <x-td align="right" colspan="4">
			                  <span class="form-total">
			                      {{config.tax_2_label}}
			                      <input type="text"
			                      class="form-input form-input-sm"
		                      	style="width: 55px;"
			                      v-model="form.tax_2">
			                      % :
			                  </span>
			                  <small class="error-control" v-if="errors.tax_2">
			                      {{errors.tax_2[0]}}
			                  </small>
			              </x-td>
			              <x-td align="right">
			                  <span class="form-total">{{form.tax_2_total | toMoney(currency, false)}}</span>
			                  <small class="error-control" v-if="errors.tax_2_total">
			                      {{errors.tax_2_total[0]}}
			                  </small>
			              </x-td>
			          </tr>
			      </template>
			      <tr>
			          <x-td colspan="1"></x-td>
			          <x-td align="right" colspan="3">
			              <strong class="form-total">{{$t('grand_total')}}:</strong>
			          </x-td>
			          <x-td align="right">
			              <strong class="form-total">{{grandTotal | toMoney(currency) }}</strong>
			          </x-td>
			      </tr>
			    </tfoot>
				</x-table>
			</x-row>
			<x-row line>
		    <x-form-group col="8" :label="$t('term')" :errors="errors.term_id">
		    	<x-quick-term :source="form.term" slot="quick"
                	:title="$t('term')" url="team-settings/terms"
                	@new="value => { form.term_id = value.id; form.term = value }"
                	></x-quick-term>
		    	<x-typeahead-table :value="form.term" url="terms" name="label"
						:columns="['label', 'description']"
					  @input="value => { form.term_id = value.id; form.term = value }">
					</x-typeahead-table>
		    </x-form-group>
		    <x-col span="16" v-if="form.term && form.term.description">
		        <div class="form-group">
		            <label class="form-label">
		                {{$t('description')}}
		            </label>
		            <pre class="form-terms">{{form.term.description}}</pre>
		        </div>
		    </x-col>
			</x-row>
	  	<div slot="footer" class="flex flex-end">
	  		<div>
	  			<x-button @click="cancel" :disabled="isSaving">{{$t('cancel')}}</x-button>
	  			<x-button @click="save" type="primary" :loading="isSaving">{{$t('save')}}</x-button>
	  		</div>
	  	</div>
		</x-panel>
	</div>
</template>
<script>
	import { formable } from '@js/lib/mixins'
	import { state } from '@js/shared/store'

	export default {
		mixins: [ formable ],
		data() {
			return {
				source: null,
				sourceIndex: null,
				redirect: 'purchase-orders',
				form: {
					terms: {},
					vendor: {},
					lines: []
				},
				config: state.team_settings,
				c_team: state.current_team,
				currency: state.current_team.currency
			}
		},
		computed: {
			subTotal() {
			    const subTotal = this.form.lines.reduce((carry, item) => {
			        return carry + Number(item.qty) * Number(item.unit_price)
			    }, 0)
			    return subTotal
			},
			subTotalAfterDiscount() {
			    let total = 0

			    if(Number(this.config.enable_discount)) {
			        total = this.subTotal - this.form.discount
			    }else {
			        total = this.subTotal
			    }

			    return total
			},
			grandTotal() {
		    let total = 0

		    if(Number(this.config.tax_enable)) {
		        if(Number(this.config.tax_2_enable)) {
		            const bothTax = this.form.tax_total + this.form.tax_2_total
		            total = this.subTotalAfterDiscount + bothTax
		        } else {
		            total = this.subTotalAfterDiscount + this.form.tax_1_total
		        }
		    }else {
		        total = this.subTotalAfterDiscount
		    }

		    return total
			}
		},
		watch: {
		    'form.tax': 'calculateTax',
		    'form.tax_2': 'calculateTax',
		    'form.discount': 'calculateTax',
		    'form.lines': {
		        handler: 'calculateTax',
		        deep: true
		    }
		},
		methods: {
			setCopy(line, i) {
				this.sourceIndex = i
				this.$set(this.$data, 'source', line)
			},
			onNewItem(e) {
				const item = e
				const temp = {
				    item: item,
			        item_id: item.id,
			        unit_price: item.unit_price,
			        qty: 1
				}
				this.form.lines.push(temp)
			},
			onCopyItem(e) {
				const item = e
			    let line = {
			        item: item,
			        item_id: item.id,
			        unit_price: item.unit_price,
			        qty: 1
			    }
			    if(this.form.lines[this.sourceIndex].id) {
			    	line['id'] = this.form.lines[this.sourceIndex].id
			    }
			    this.$set(this.form.lines, this.sourceIndex, line)
			    this.$set(this.form, 'source', null)
			    this.sourceIndex = null
			},
			beforeSetData(res) {
				if(res.data.form.foreign_currency) {
					this.$set(this.$data, 'currency', res.data.form.foreign_currency)
				}
			},
			setCurrency(value) {
				if(value.currency) {
					this.$set(this.form, 'foreign_currency', value.currency)
					this.$set(this.form, 'foreign_currency_id', value.currency.id)
					this.$set(this.$data, 'currency', value.currency)
				} else {
					this.$set(this.form, 'foreign_currency', null)
					this.$set(this.form, 'foreign_currency_id', null)
					this.$set(this.$data, 'currency', this.c_team.currency)
				}
			},
			onVendor(value) {
				this.$set(this.form, 'vendor', value)
				this.$set(this.form, 'vendor_id', value.id)
				this.setCurrency(value)
			},
			calculateTax() {
			    this.form.tax_total = this.subTotalAfterDiscount * (this.form.tax) / 100
			    if(Number(this.config.tax_2_enable)) {
			        this.form.tax_2_total = this.subTotalAfterDiscount * (this.form.tax_2) / 100
			    }
			},
			onItem(row, index, e) {
			    const item = e
			    let line = {
			        item: item,
			        item_id: item.id,
			        unit_price: item.unit_price,
			        qty: 1
			    }

			    if(this.form.lines[index].id) {
			    	line['id'] = this.form.lines[index].id
			    }

			    this.$set(this.form.lines, index, line)
			},
			addNewLine() {
	          const item = {
	              item: null,
	              item_id: null,
	              unit_price: 0,
	              qty: 1,
	          }
	          this.form.lines.push(item)
	      },
	      removeItem(item, index) {
	          if(this.form.lines.length > 1) {
	              this.form.lines.splice(index, 1)
	          }
	      }
		}
	}
</script>