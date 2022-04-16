<template>
	<div class="content-inner" v-if="show">
		<x-panel>
			<div slot="title">{{$t('overview')}}</div>
			<div slot="extra">
				<x-button size="sm" type="primary" v-if="showEdit"
					@click="toggleFieldModal">{{$t('add_card')}}</x-button>
					<x-button size="sm" type="success" v-if="showEdit"
					@click="saveDashboard">{{$t('save_layout')}}</x-button>
				<x-button size="sm" type="secondary" @click="toggleEdit">{{$t('edit')}}</x-button>
			</div>
		</x-panel>
		<div class="metric-grid">
						<grid-layout
			         :layout.sync="metrics"
			         :col-num="3"
			         :row-height="30"
			         :is-draggable="showEdit"
			         :is-resizable="showEdit"
			         :is-mirrored="false"
			         :vertical-compact="true"
			         :margin="[15, 15]"
			         :use-css-transforms="true"
						   >
						   <grid-item v-for="metric in metrics" :key="metric.id"
                  :x="metric.x"
                  :y="metric.y"
                  :w="metric.w"
                  :h="metric.h"
                  :i="metric.i"
                  @moved="movedEvent">
                  <div class="metric-card-drag">
                  	<value-card v-if="metric.metric_type === 'value'"
  				          	:edit="showEdit"
  				          	:metric="metric"
  				          	@edit="openEditModal(metric)"
  				          	@remove="removeMetric(metric)"></value-card>
  				          <chart-card v-else
  				          	:edit="showEdit"
  				          	:metric="metric"
  				          	@edit="openEditModal(metric)"
  				          	@remove="removeMetric(metric)"></chart-card>
                  </div>
						    </grid-item>
						  </grid-layout>
		</div>
		<metrics-modal :other-value-types="otherValueTypes"
			:chart-fields="chartTypes" :editing="editing"
			:resources="resources" v-if="showModal" @cancel="toggleFieldModal"
	  	@add="handleAddCard"></metrics-modal>
	</div>
</template>
<script>
	import { indexable } from '@js/lib/mixins'
	import MetricsModal from '@js/partials/MetricsModal.vue'
	import ChartCard from '@js/partials/ChartCard.vue'
	import ValueCard from '@js/partials/ValueCard.vue'
	import VueGridLayout from 'vue-grid-layout'

	export default {
		mixins: [ indexable ],
		components: {
			MetricsModal, ChartCard, ValueCard,
			GridLayout: VueGridLayout.GridLayout,
      		GridItem: VueGridLayout.GridItem
		},
		data() {
			return {
				editing: null,
				showEdit: false,
				showModal: false,
				show: false,
				metrics: [],
				resources: [
					'users',
					'subscriptions',
					'teams',
					'plans',
					'faqs',
					'admins',
					'currencies'
				],
				otherValueTypes: {
					'users': ['id'],
					'subscriptions': ['id'],
					'teams': ['id'],
					'plans': ['id'],
					'faqs': ['id'],
					'admins': ['id'],
					'currencies': ['id']
				},
				chartTypes: {
					'users': {
						'y_axis': ['id'],
						'x_axis': {
							'number': 'string',
							'name': 'string',
							'email': 'string',
							'card_brand': 'string',
							'trial_ends_at': 'datetime',
							'created_at': 'datetime'
						}
					},
					'subscriptions': {
						'y_axis': ['id'],
						'x_axis': {
							'name': 'string',
							'stripe_plan': 'string',
							'created_at': 'datetime'
						}
					},
					'teams': {
						'y_axis': ['id'],
						'x_axis': {
							'name': 'string',
							'timezone': 'string',
							'date_format': 'string',
							'currency_id': 'string',
							'lang_id': 'string',
							'created_at': 'datetime'
						}
					},
					'plans': {
						'y_axis': ['id'],
						'x_axis': {
							'name': 'string',
							'created_at': 'datetime'
						}
					},
					'faqs': {
						'y_axis': ['id'],
						'x_axis': {
							'created_at': 'datetime'
						}
					},
					'admins': {
						'y_axis': ['id'],
						'x_axis': {
							'created_at': 'datetime'
						}
					},
					'currencies': {
						'y_axis': ['id'],
						'x_axis': {
							'name': 'string',
							'code': 'string',
							'created_at': 'datetime'
						}
					}
				}
			}
		},
		methods: {
			saveDashboard() {
				let updates = this.metrics.map((metric) => {
					return {
						id: metric.id,
						x: metric.x,
						y: metric.y,
						h: metric.h,
						w: metric.w
					}
				})

				this.$http.put('/api/user_metrics', {metrics: updates})
			    .then((res) => {
			        if(res.data.saved) {
                	const id = Math.random().toString(36).substring(7)
                	this.showEdit = false
									this.$router.push(`/?id=${id}`)
			            this.$message.success(this.$t('success_saved'))
			        }
			    })
			},
			movedEvent(i, newX, newY) {
				// console.log(i, newX, newY)
			},
			removeMetric(item) {
				const r = confirm(this.$t('are_you_sure'))
				if(r != true) { return }

				this.$bar.start()
				this.$http.delete(`/api/user_metrics/${item.id}`)
				    .then((res) => {
				        if(res.data.deleted) {
                  	const id = Math.random().toString(36).substring(7)
                  	this.showEdit = false
										this.$router.push(`/?id=${id}`)
				            this.$message.success(this.$t('success_delete'))
				        }
				    })
				    .catch((error) => {
				    	this.$bar.finish()
				      if(error && error.response.status === 422) {
				        this.$message.error(error.response.data.message)
				      }
				    })
			},
			handleAddCard() {

			},
			toggleEdit() {
				this.showEdit = !this.showEdit
			},
			toggleFieldModal() {
				this.showModal = !this.showModal
			},
			setData(res) {
				this.$set(this.$data, 'metrics', res.data.metrics)
				this.$bar.finish()
				this.show = true
			},
			openEditModal(metric) {
				this.$set(this.$data, 'editing', metric)
				this.toggleFieldModal()
			}
		}
	}
</script>