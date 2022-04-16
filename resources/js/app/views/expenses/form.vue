<template>
	<x-form padding :saving="isSaving" @save="save" @cancel="cancel" v-if="show">
		<div slot="title">{{$t(mode)}} {{$t('expense')}}</div>
		<x-row line>
			<x-form-group col="16" :label="$t('vendor')" :errors="errors.vendor_id">
					<x-typeahead-table :value="form.vendor" url="vendors" name="name"
						:columns="['number', 'name', 'organization']"
					  @input="value => { form.vendor_id = value.id; form.vendor = value }">
					</x-typeahead-table>
				</x-form-group>
			<x-form-group col="8" :label="$t('category')" :errors="errors.category_id">
				<x-quick-category slot="quick" :source="form.category"
					:title="$t('category')" url="team-settings/expense_categories"
					@new="value => { form.category_id = value.id; form.category = value }"
					></x-quick-category>
				<x-tag-input :value="form.category" resource="expense_categories" column="name" name="name"
				    @input="value => { form.category_id = value.id; form.category = value }">
				</x-tag-input>
			</x-form-group>
		</x-row>
		<x-row line>
			<x-form-group col="8" type="date" v-model="form.payment_date" :label="$t('payment_date')" :errors="errors.payment_date"></x-form-group>
			<x-form-group col="8" :label="$t('payment_method')" :errors="errors.method">
				<select class="form-input" v-model="form.method">
					<option v-for="method in options.methods"
						:value="method">
						{{$t(method)}}
					</option>
				</select>
			</x-form-group>
			<x-form-group col="8" v-model="form.reference" :label="$t('reference')" :errors="errors.reference"></x-form-group>
		</x-row>
		<x-row line>
			<x-table class="table-form">
					  <x-thead>
					    <x-tr>
					      <x-td type="th" size="13"></x-td>
					      <x-td type="th" size="2"></x-td>
					      <x-td type="th" size="3"></x-td>
					      <x-td type="th" size="2"></x-td>
					      <x-td type="th" size="4"></x-td>
					    </x-tr>
					  </x-thead>
			  <tfoot>
		      <tr>
	          <x-td align="right" colspan="4">
	              <span class="form-total">
	                  {{$t('sub_total')}}
	                  <input type="text"
	                  		class="form-input form-input-sm"
	                      style="width: 55px;"
	                      v-model="form.sub_total">
	                  :
	              </span>
	          </x-td>
	          <x-td align="right">
	              <span class="form-total">{{form.sub_total | formatMoney(false)}}</span>
	              <small class="error-control" v-if="errors.sub_total">
	                  {{errors.sub_total[0]}}
	              </small>
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
			<x-form-group col="8" source="textarea" v-model="form.note" :label="$t('note')" :errors="errors.note"></x-form-group>
		</x-row>
	</x-form>
</template>
<script>
	import { formable } from '@js/lib/mixins'
	import { state } from '@js/shared/store'
	export default {
		mixins: [ formable ],
		data() {
			return {
				redirect: 'expenses',
				config: state.team_settings,
				options: {
					methods: [
						'cheque',
						'cash',
						'bank_transfer'
					]
				}
			}
		},
		computed: {
				grandTotal() {
			    let total = 0

			    if(Number(this.config.tax_enable)) {
			        if(Number(this.config.tax_2_enable)) {
			            const bothTax = this.form.tax_total + this.form.tax_2_total
			            total = Number(this.form.sub_total) + bothTax
			        } else {
			            total = Number(this.form.sub_total) + this.form.tax_1_total
			        }
			    }else {
			        total = Number(this.form.sub_total)
			    }

			    return total
				}
		},
		watch: {
		    'form.tax': 'calculateTax',
		    'form.tax_2': 'calculateTax',
		    'form.sub_total': 'calculateTax'
		},
		methods: {
			calculateTax() {
			    this.form.tax_total = this.form.sub_total * (this.form.tax) / 100
			    if(Number(this.config.tax_2_enable)) {
			        this.form.tax_2_total = this.form.sub_total * (this.form.tax_2) / 100
			    }
			},
		}
	}
</script>