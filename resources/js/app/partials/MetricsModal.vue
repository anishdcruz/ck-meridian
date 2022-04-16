<template>
	<x-modal width="450" @cancel="$emit('cancel')">
		<div slot="title">
			<span>{{$t('new_card')}}</span>
		</div>
		<div>
			<x-row>
				<x-col span="24">
						<div class="form-group">
							<label class="form-label">
							  {{$t('card_label')}}
							</label>
							<x-input :value="form.label" v-model="form.card_label"></x-input>
						</div>
					</x-col>
			</x-row>
			<x-row>
				<x-form-group col="12" :label="$t('resource')">
					<select class="form-input" v-model="form.resource">
						<option :value="resource"
							v-for="resource in resources">{{$t(resource)}}</option>
					</select>
				</x-form-group>
				<x-form-group col="12" :label="$t('metric_type')">
					<select class="form-input" v-model="form.metric_type">
						<option :value="metric_type"
							v-for="metric_type in metrics">{{$t(metric_type)}}</option>
					</select>
				</x-form-group>
			</x-row>
			<x-row v-if="form.metric_type === 'value'">
				<x-form-group col="12" :label="$t('value_type')">
					<select class="form-input" v-model="form.value_type">
						<option :value="value_type"
							v-for="value_type in value_types">{{$t(value_type)}}</option>
					</select>
				</x-form-group>
				<x-form-group v-if="form.value_type !== 'count'" col="12" :label="$t('value_field')">
					<select class="form-input" v-model="form.value_field">
						<option :value="value_field"
							v-for="value_field in getOtherValueTypes(form.resource)">{{$t(value_field)}}</option>
					</select>
				</x-form-group>
			</x-row>
			<template v-if="form.metric_type === 'chart'">
				<x-row>
					<x-form-group col="12" :label="$t('chart_type')">
						<select class="form-input" v-model="form.chart_type">
							<option :value="chart_type"
								v-for="chart_type in chart_types">{{$t(chart_type)}}</option>
						</select>
					</x-form-group>
				</x-row>
				<x-row>
					<x-form-group col="12" :label="$t('y_axis_type')">
						<select class="form-input" v-model="form.y_axis_type">
							<option :value="y_axis_type"
								v-for="y_axis_type in y_axis_types">{{$t(y_axis_type)}}</option>
						</select>
					</x-form-group>
					<x-form-group v-if="form.y_axis_type !== 'count'" col="12" :label="$t('y_axis_field')">
						<select class="form-input" v-model="form.y_axis_field">
							<option :value="y_axis_field"
								v-for="y_axis_field in getYAxisField(form.resource)">{{$t(y_axis_field)}}</option>
						</select>
					</x-form-group>
				</x-row>
				<x-row>
					<x-form-group col="12" :label="$t('x_axis_field')">
						<select class="form-input" v-model="form.x_axis_field">
							<option :value="x_axis_field"
								v-for="(type, x_axis_field) in getXAxisField(form.resource)">{{$t(x_axis_field)}}</option>
						</select>
					</x-form-group>
					<x-form-group col="12" :label="$t('x_axis_type')" v-if="getXAxisType === 'datetime'">
						<select class="form-input" v-model="form.x_axis_type">
							<option :value="x_axis_type"
								v-for="x_axis_type in x_axis_types">{{$t(x_axis_type)}}</option>
						</select>
					</x-form-group>
				</x-row>
				<x-row v-if="form.chart_type !== 'doughnut' && form.chart_type !== 'pie'">
					<x-form-group col="12" :label="$t('color')">
						<select class="form-input" v-model="form.color">
							<option :value="rgb"
								v-for="(rgb, color) in colors">{{$t(color)}}</option>
						</select>
					</x-form-group>
				</x-row>
			</template>
			<x-row>
				<x-form-group col="24" :label="$t('filter')" :errors="errors.filter_id">
					<x-tag-input :value="form.filter" :params="{resource: form.resource}" resource="filters" column="name" name="name"
					    @input="value => { form.filter_id = value.id; form.filter = value }">
					</x-tag-input>
				</x-form-group>
			</x-row>
		</div>
		<template slot="footer">
			<div></div>
			<div>
				<x-button @click="$emit('cancel')" :disabled="loading">{{$t('cancel')}}</x-button>
				<x-button type="primary" :disabled="loading" @click="onSave">
					{{editing ? this.$t('save_card') : this.$t('add_card')}}
				</x-button>
			</div>
		</template>
	</x-modal>
</template>
<script>
	export default {
		props: {
			resources: {
				required: true,
				type: Array
			},
			otherValueTypes: {
				required: true,
				type: Object
			},
			chartFields: {
				required: true,
				type: Object
			},
			editing: {
				default: null
			}
		},
		data() {
			return {
				metrics: ['value', 'chart'],
				value_types: ['count', 'sum', 'avg'],
				chart_types: ['bar', 'line', 'pie', 'doughnut'],
				y_axis_types: ['count', 'sum', 'avg'],
				x_axis_types: ['yearly', 'monthly', 'weekly', 'daily', null],
				colors: {
					'pink': '255, 99, 132',
					'blue': '54, 162, 235',
					'yellow': '255, 206, 86',
					'cyan': '75, 192, 192',
					'purple': '153, 102, 255',
					'orange': '255, 159, 64'
				},
				form: {
					filter: {},
					filter_id: {},
					resource: this.resources[0],
					card_label: this.$t('card_label'),
					metric_type: 'chart',
					value_type: 'count',
					value_field: null,
					chart_type: 'line',
					color: '75, 192, 192',
					y_axis_type: 'sum',
					y_axis_field: null,
					x_axis_type: 'monthly',
					x_axis_field: null
				},
				loading: false,
				errors: {},
			}
		},
		computed: {
			getOtherValueTypes() {
				return (type) => {
					return this.otherValueTypes[type]
				}
			},
			getYAxisField() {
				return (type) => {
					return this.chartFields[type].y_axis
				}
			},
			getXAxisField() {
				return (type) => {
					return this.chartFields[type].x_axis
				}
			},
			getXAxisType() {
				let types = this.getXAxisField(this.form.resource)
				let type = types[this.form.x_axis_field]
				return type
			}
		},
		mounted() {
			if(this.editing) {
				this.$set(this.$data, 'form', this.editing)
			}
		},
		methods: {
			onSave() {
				if(this.editing) {
					this.saveMetric()
				} else {
					this.addMetric()
				}
			},
			addMetric() {
				this.loading = true
				this.$http.post('/api/user_metrics', this.form)
					.then((res) => {
		            if(res.data.saved) {
		            	const id = Math.random().toString(36).substring(7)
									this.$router.push(`/?id=${id}`)
		              this.$message.success(`${this.$t('saved_success')}`)
		              this.$emit('cancel')
		            }
					})
					.catch(error => {
		              this.loading = false
		              if(error.response.status === 422) {
		              	this.errors = error.response.data.errors
		              }
	         		 })
			},

			saveMetric() {
				this.loading = true
				this.$http.put(`/api/user_metrics/${this.editing.id}`, this.form)
					.then((res) => {
		            if(res.data.saved) {
		            	const id = Math.random().toString(36).substring(7)
						this.$router.push(`/?id=${id}`)
		              	this.$message.success(`${this.$t('saved_success')}`)
		              	this.$emit('cancel')
		            }
					})
					.catch(error => {
		              this.loading = false
		              if(error.response.status === 422) {
		              	this.errors = error.response.data.errors
		              }
	         		 })
			}
		}
	}
</script>