<template>
	<div class="content-inner" v-if="show">
		<x-panel padding margin>
			<div slot="title">
				{{$t(mode)}} {{$t('recurring_invoice')}}
			</div>
			<x-row line>
				<x-form-group col="8" v-model="form.title" :label="$t('title')" :errors="errors.title"></x-form-group>
				<x-form-group col="16" :label="$t('contact')" :errors="errors.contact_id">
					<x-quick-contact slot="quick" :source="form.contact"
						:title="$t('contact')" url="contacts"
						@new="value => { form.contact_id = value.id; form.contact = value }"
						></x-quick-contact>
					<x-typeahead-table :value="form.contact" url="contacts" name="name"
						:columns="['number', 'name', 'organization']"
					  @input="value => { form.contact_id = value.id; form.contact = value }">
					</x-typeahead-table>
				</x-form-group>
			</x-row>
			<x-row line>
				<x-form-group col="8" type="date" v-model="form.start_date" :label="$t('start_date')" :errors="errors.start_date"></x-form-group>
				<x-form-group source="switch" col="8" v-model="form.never_expiry" :label="$t('never_expiry')" :errors="errors.never_expiry"></x-form-group>
				<x-form-group v-if="!form.never_expiry" col="8" type="date"
					v-model="form.end_date" :label="$t('end_date')" :errors="errors.end_date"></x-form-group>
			</x-row>
			<x-row line>
			    <x-col span="8">
			        <div class="form-group">
			            <label class="form-label">{{$t('frequency')}}</label>
			            <select class="form-input" v-model="form.frequency">
			                <option value="weekly">{{$t('weekly')}}</option>
			                <option value="monthly">{{$t('monthly')}}</option>
			            </select>
			            <small class="error-control" v-if="errors.frequency">
			                {{errors.frequency[0]}}
			            </small>
			        </div>
			    </x-col>
			    <x-col span="8">
			        <div class="form-group">
			            <label class="form-label">{{$t('send_at')}}</label>
			            <select class="form-input" v-model="form.send_at">
			                <option value="1:00">1:00 {{$t('am')}}</option>
			                <option value="1:30">1:30 {{$t('am')}}</option>
			                <option value="2:00">2:00 {{$t('am')}}</option>
			                <option value="2:30">2:30 {{$t('am')}}</option>
			                <option value="3:00">3:00 {{$t('am')}}</option>
			                <option value="3:30">3:30 {{$t('am')}}</option>
			                <option value="4:00">4:00 {{$t('am')}}</option>
			                <option value="4:30">4:30 {{$t('am')}}</option>
			                <option value="5:00">5:00 {{$t('am')}}</option>
			                <option value="5:30">5:30 {{$t('am')}}</option>
			                <option value="6:00">6:00 {{$t('am')}}</option>
			                <option value="6:30">6:30 {{$t('am')}}</option>
			                <option value="7:00">7:00 {{$t('am')}}</option>
			                <option value="7:30">7:30 {{$t('am')}}</option>
			                <option value="8:00">8:00 {{$t('am')}}</option>
			                <option value="8:30">8:30 {{$t('am')}}</option>
			                <option value="9:00">9:00 {{$t('am')}}</option>
			                <option value="9:30">9:30 {{$t('am')}}</option>
			                <option value="10:00">10:00 {{$t('am')}}</option>
			                <option value="10:30">10:30 {{$t('am')}}</option>
			                <option value="11:00">11:00 {{$t('am')}}</option>
			                <option value="11:30">11:30 {{$t('am')}}</option>
			                <option value="12:00">12:00 {{$t('pm')}}</option>
			                <option value="12:30">12:30 {{$t('pm')}}</option>
			                <option value="13:00">1:00 {{$t('pm')}}</option>
			                <option value="13:30">1:30 {{$t('pm')}}</option>
			                <option value="14:00">2:00 {{$t('pm')}}</option>
			                <option value="14:30">2:30 {{$t('pm')}}</option>
			                <option value="15:00">3:00 {{$t('pm')}}</option>
			                <option value="15:30">3:30 {{$t('pm')}}</option>
			                <option value="16:00">4:00 {{$t('pm')}}</option>
			                <option value="16:30">4:30 {{$t('pm')}}</option>
			                <option value="17:00">5:00 {{$t('pm')}}</option>
			                <option value="17:30">5:30 {{$t('pm')}}</option>
			                <option value="18:00">6:00 {{$t('pm')}}</option>
			                <option value="18:30">6:30 {{$t('pm')}}</option>
			                <option value="19:00">7:00 {{$t('pm')}}</option>
			                <option value="19:30">7:30 {{$t('pm')}}</option>
			                <option value="20:00">8:00 {{$t('pm')}}</option>
			                <option value="20:30">8:30 {{$t('pm')}}</option>
			                <option value="21:00">9:00 {{$t('pm')}}</option>
			                <option value="21:30">9:30 {{$t('pm')}}</option>
			                <option value="22:00">10:00 {{$t('pm')}}</option>
			                <option value="22:30">10:30 {{$t('pm')}}</option>
			                <option value="23:00">11:00 {{$t('pm')}}</option>
			                <option value="23:30">11:30 {{$t('pm')}}</option>
			            </select>
			            <small class="error-control" v-if="errors.send_at">
			                {{errors.send_at[0]}}
			            </small>
			        </div>
			    </x-col>
					<x-form-group col="8" v-model="form.due_after" :label="$t('due_date_after_days')" :errors="errors.due_after"></x-form-group>
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
				  		        {{Number(line.qty) * Number(line.unit_price) | formatMoney(false)}}
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
			              <span class="form-total">{{subTotal | formatMoney(false)}}</span>
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
		              <span class="form-total">-{{form.discount | formatMoney(false)}}</span>
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
		              <span class="form-total">{{subTotalAfterDiscount | formatMoney(false)}}</span>
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
			                  <span class="form-total">{{form.tax_total | formatMoney(false)}}</span>
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
			                  <span class="form-total">{{form.tax_2_total | formatMoney(false)}}</span>
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
			              <strong class="form-total">{{grandTotal | formatMoney}}</strong>
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
				redirect: 'recurring-invoices',
				form: {
					terms: {},
					contact: {},
					lines: []
				},
				config: state.team_settings
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