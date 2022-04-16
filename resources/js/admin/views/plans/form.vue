<template>
	<x-form padding :saving="isSaving" @save="save" @cancel="cancel" v-if="show">
		<div slot="title">{{$t(mode)}} {{$t('plan')}}</div>
		<x-row line>
			<x-form-group col="8" v-model="form.name" :label="$t('name')" :errors="errors.name"></x-form-group>
			<x-form-group col="8" v-model="form.gateway_id" :label="$t('gateway_id')" :errors="errors.gateway_id"></x-form-group>
			<x-col span="8">
			    <div class="form-group">
			        <label class="form-label">{{$t('time_period')}}</label>
			        <select class="form-input" v-model="form.time_period">
			            <option value="monthly">{{$t('monthly')}}</option>
			            <option value="yearly">{{$t('yearly')}}</option>
			        </select>
			        <small class="error-control" v-if="errors.time_period">
			            {{errors.time_period[0]}}
			        </small>
			    </div>
			</x-col>
			<x-form-group col="8" v-model="form.price" :label="$t('price')" :errors="errors.price"></x-form-group>
			<x-form-group col="8" source="switch" v-model="form.active" :label="$t('active')" :errors="errors.active"></x-form-group>
		</x-row>
		<x-row line>
			<x-form-group col="8" source="textarea" v-model="form.note" :label="$t('note')" :errors="errors.note"></x-form-group>
		</x-row>
		<x-row line>
			<x-col span="8">
				<div class="form-group">
				    <label class="form-label">{{$t('features')}}</label>
					<x-table class="table-form">
						<x-thead></x-thead>
						<x-tbody>
							<template v-for="(item, index) in form.list">
								<x-tr>
									<x-td align="right">
									    <input type="text" class="form-input"  v-model="form.list[index]">
									    <small class="error-control" v-if="errors[`list.${index}`]">
									        {{errors[`list.${index}`][0]}}
									    </small>
									</x-td>
									<x-td>
										<span class="form-list-remove">
										  <i class="icon icon-trash-a" @click="form.list.splice(index, 1)"></i>
										</span>
									</x-td>
								</x-tr>
							</template>
						</x-tbody>
					</x-table>
				    <x-button size="sm" icon="plus" @click="form.list.push('')">
				        {{$t('add_new_line')}}
				    </x-button>
				</div>
			</x-col>
			<x-col span="8">
				<div class="form-group">
				    <label class="form-label">{{$t('limits')}}</label>
				    <x-table class="table-form">
				    	<x-thead>
				    		<x-tr>
				    			<x-td type="th" size="18">{{$t('feature')}}</x-td>
				    			<x-td type="th" size="6">{{$t('max_allowed')}}</x-td>
				    		</x-tr>
				    	</x-thead>
				    	<x-tbody>
				    		<template v-for="(limit, index) in form.limits">
				    			<x-tr>
				    				<x-td>
				    				    <span class="form-total">{{$t(limit.name)}}</span>
				    				</x-td>
				    				<x-td>
				    				    <input type="number" class="form-input"  v-model="limit.max">
				    				    <small class="error-control" v-if="errors[`limits.${index}`]">
				    				        {{errors[`limits.${index}`][0]}}
				    				    </small>
				    				</x-td>
				    			</x-tr>
				    		</template>
				    	</x-tbody>
				    </x-table>
				</div>
			</x-col>
		</x-row>
	</x-form>
</template>
<script>
	import { formable } from '@js/lib/mixins'
	export default {
		mixins: [ formable ],
		data() {
			return {
				redirect: 'plans'
			}
		}
	}
</script>