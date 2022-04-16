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
					'contacts',
					'items',
					'quotations',
					'invoices',
					'payments',
					'refunds',
					'recurring-invoices',
					'vendors',
					'purchase-orders',
					'payments-made',
					'expenses'
				],
				otherValueTypes: {
					'contacts': ['total_revenue'],
					'items': ['unit_price'],
					'quotations': ['sub_total', 'discount', 'grand_total'],
					'invoices': ['sub_total', 'discount', 'grand_total', 'amount_paid'],
					'payments': ['amount_received', 'transaction_fees', 'net_amount', 'amount_refunded'],
					'refunds': ['amount'],
					'recurring-invoices': ['sub_total', 'discount', 'grand_total'],
					'vendors': ['total_paid'],
					'purchase-orders': ['sub_total', 'discount', 'grand_total', 'amount_paid'],
					'payments-made': ['amount_received', 'transaction_fees', 'net_amount'],
					'expenses': ['sub_total', 'grand_total']
				},
				chartTypes: {
					'contacts': {
						'y_axis': ['total_revenue'],
						'x_axis': {
							'number': 'string',
							'name': 'string',
							'organization': 'string',
							'created_at': 'datetime',
							'category.name': 'string'
						}
					},
					'items': {
						'y_axis': ['unit_price'],
						'x_axis': {
							'code': 'string',
							'created_at': 'datetime',
							'category.name': 'string'
						}
					},
					'quotations': {
						'y_axis': ['sub_total', 'discount', 'grand_total'],
						'x_axis': {
							'issue_date': 'datetime',
							'expiry_date': 'datetime',
							'created_at': 'datetime',
							'status.name': 'string'
						}
					},
					'invoices': {
						'y_axis': ['sub_total', 'discount', 'grand_total', 'amount_paid'],
						'x_axis': {
							'issue_date': 'datetime',
							'due_date': 'datetime',
							'created_at': 'datetime',
							'paid_at': 'datetime',
							'status.name': 'string'
						}
					},
					'recurring-invoices': {
						'y_axis': ['sub_total', 'discount', 'grand_total'],
						'x_axis': {
							'created_at': 'datetime'
						}
					},
					'payments': {
						'y_axis': ['amount_received', 'transaction_fees', 'net_amount', 'amount_refunded'],
						'x_axis': {
							'payment_date': 'datetime',
							'created_at': 'datetime',
							'method': 'string'
						}
					},
					'refunds': {
						'y_axis': ['amount'],
						'x_axis': {
							'refund_date': 'datetime',
							'created_at': 'created_at'
						}
					},
					'vendors': {
						'y_axis': ['total_paid'],
						'x_axis': {
							'number': 'string',
							'name': 'string',
							'organization': 'string',
							'created_at': 'datetime',
							'category.name': 'string'
						}
					},
					'purchase-orders': {
						'y_axis': ['sub_total', 'discount', 'grand_total', 'amount_paid'],
						'x_axis': {
							'issue_date': 'datetime',
							'due_date': 'datetime',
							'created_at': 'datetime',
							'paid_at': 'datetime',
							'status.name': 'string'
						}
					},
					'payments-made': {
						'y_axis': ['amount_received', 'transaction_fees', 'net_amount'],
						'x_axis': {
							'payment_date': 'datetime',
							'created_at': 'datetime',
							'method': 'string'
						}
					},
					'expenses': {
						'y_axis': ['sub_total', 'grand_total'],
						'x_axis': {
							'payment_date': 'datetime',
							'created_at': 'created_at',
							'category.name': 'string'
						}
					},
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