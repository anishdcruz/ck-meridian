<template>
	<div class="metric-card">
		<div class="metric-card-inner">
			<div class="metric-card-title">
				<router-link :to="`/${metric.resource}?filter_id=${metric.filter_id}`">
					<span>{{metric.card_label}}</span>
				</router-link>
				<i class="metric-card-trash icon icon-edit"
				  v-if="edit && metric.filter !== null"
				  @click="$emit('edit')"></i>
				 <i class="metric-card-trash icon icon-trash-b"
				  v-if="edit"
				  @click="$emit('remove')"></i>
			</div>
			<p class="metric-card-value" v-if="metric.value_type === 'count'">
				{{metric.values}}
			</p>
			<p v-else class="metric-card-value">{{metric.values | formatMoney(false)}}</p>
		</div>
	</div>
</template>
<script>
	export default {
		props: {
			edit: {
				type: Boolean,
				default: false
			},
			metric: Object
		}
	}
</script>