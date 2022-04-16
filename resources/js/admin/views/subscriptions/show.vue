<template>
	<div class="content-inner" v-if="show">
		<x-panel padding>
			<div slot="title">
				<router-link to="/subscriptions">{{$t('subscription')}}</router-link> / {{model.name}}
			</div>
			<div slot="extra">
				<router-link :to="`/subscriptions`" class="btn btn-default btn-sm">
					<small class="icon icon-arrow-left-c"></small>
				</router-link>
				<x-button v-if="!model.ends_at" size="sm" type="error" :disabled="inProcess" @click="cancel(model.id)">{{$t('cancel_subscription')}}</x-button>
				<x-button v-else size="sm" type="primary" :disabled="inProcess" @click="restore(model.id)">{{$t('restore_subscription')}}</x-button>
			</div>
			<x-row line>
				<x-group col="8" label="name">
					<pre>{{model.name}}</pre>
				</x-group>
				<x-group col="8" label="stripe_id">
					<pre>{{model.stripe_id}}</pre>
				</x-group>
				<x-group col="8" label="stripe_plan">
					<pre>{{model.stripe_plan}}</pre>
				</x-group>
				<x-group col="8" label="quantity">
					<pre>{{model.quantity}}</pre>
				</x-group>
				<x-group col="8" label="ends_at">
					<pre>{{model.ends_at ||'-'}}</pre>
				</x-group>
				<x-group col="8" label="trial_ends_at">
					<pre>{{model.trial_ends_at ||'-'}}</pre>
				</x-group>
			</x-row>
			<x-row line>
				<x-group col="8" label="created_at">
					<pre>{{model.created_at | toDate}}</pre>
				</x-group>
			</x-row>
		</x-panel>
	</div>
</template>
<script>
	import { showable } from '@js/lib/mixins'
	export default {
		mixins: [ showable ],
		data() {
			return {
				show: false,
				model: {
					category: {}
				},
				inProcess: false
			}
		},
		methods: {
			cancel(id) {
				this.$http.delete(`/api/subscriptions/${id}`)
					.then((res) => {
						this.$message.success(this.$t('saved_success'))
						const d = Math.random().toString(36).substring(7)
						this.$router.push(`/subscriptions/${id}?d=${d}`)
					})
			},
			restore(id) {
				this.$http.put(`/api/subscriptions/${id}`)
					.then((res) => {
						this.$message.success(this.$t('saved_success'))
						const d = Math.random().toString(36).substring(7)
						this.$router.push(`/subscriptions/${id}?d=${d}`)
					})
			}
		}
	}
</script>