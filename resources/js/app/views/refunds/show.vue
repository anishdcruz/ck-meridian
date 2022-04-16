<template>
	<div class="content-inner" v-if="show">
		<x-panel padding margin>
			<div slot="title">
				<router-link to="/refunds">{{$t('refund')}}</router-link> / {{model.name}}
				<small>({{model.number}})</small>
			</div>
			<div slot="extra">
				<router-link :to="`/refunds`" class="btn btn-default btn-sm">
					<small class="icon icon-arrow-left-c"></small>
				</router-link>
				<x-button icon="email" size="sm" @click="toggleModal"></x-button>
				<x-button size="sm" type="error" icon="trash-b" @click="removeDB('refunds', model.id)"></x-button>
			</div>
			<x-row line>
				<x-group col="8" label="number">
					<p>{{model.number}}</p>
				</x-group>
				<x-group col="8" label="contact">
					<router-link :to="`/contacts/${model.contact_id}`">
					    {{model.contact.name}}
					    <span v-if="model.contact.organization">
					        - {{model.contact.organization}}<br>
					    </span>
					</router-link>

				</x-group>
				<x-group col="8" label="payment">
					<router-link :to="`/payments/${model.payment_id}`">
					    {{model.payment.number}}
					</router-link>
				</x-group>
		</x-row>
		<x-row line>
			<x-group col="8" label="amount">
				<p>{{model.amount | formatMoney}}</p>
			</x-group>
			<x-group col="8" label="reason">
				<p>{{$t(model.reason)}}</p>
			</x-group>
			<x-group col="8" label="description">
				<p>{{model.description}}</p>
			</x-group>
		</x-row>
		<x-row line>
			<x-group col="8" label="refund_date">
				<p>{{model.refund_date | toDate}}</p>
			</x-group>
			<x-group col="8" label="refund_id">
				<p>{{model.refund_id || '-'}}</p>
			</x-group>
			<x-group col="8" label="status">
				<p v-if="model.status">
					<span :class="`status status-${model.status}`">
						<span class="status-text">{{model.status}}</span>
					</span>
				</p>
				<p v-else>-</p>
			</x-group>
		</x-row>

		<x-row line>
			<x-group col="8" label="created_at">
				<pre>{{model.created_at | toDate}}</pre>
			</x-group>
		</x-row>
		</x-panel>

		<email-modal v-if="showModal" :id="model.id" type="refunds" @cancel="toggleModal" @ok="onSaved"></email-modal>
	</div>
</template>
<script>
	import { showable } from '@js/lib/mixins'
	import { state } from '@js/shared/store'

	import EmailModal from '@js/partials/EmailModal.vue'
	export default {
		mixins: [ showable ],
		components: { EmailModal },
		data() {
			return {
				showModal: false,
				show: false,
				config: state.team_settings,
				model: {
					category: {}
				}
			}
		},
		methods: {
			refreshPage() {
				this.showModal = false
				const id = Math.random().toString(36).substring(7)
				this.$router.push(`?id=${id}`)
			},
			onSaved() {
				this.showModal = false
				const id = Math.random().toString(36).substring(7)
				this.$router.push(`?id=${id}`)
			},
			toggleModal() {
				this.showModal = !this.showModal
			},
		}
	}
</script>