<template>
	<div class="content-inner" v-if="show">
		<x-panel padding margin>
			<div slot="title">
				<router-link to="/users">{{$t('user')}}</router-link> / {{model.name}}
			</div>
			<div slot="extra">
				<router-link :to="`/users`" class="btn btn-default btn-sm">
					<small class="icon icon-arrow-left-c"></small>
				</router-link>
				<x-button size="sm" type="error" icon="trash-b" @click="removeDB('users', model.id)"></x-button>
			</div>
			<x-row line>
				<x-group col="8" label="name">
					<pre>{{model.name}}</pre>
				</x-group>
				<x-group col="8" label="email">
					<pre>{{model.email}}</pre>
				</x-group>
				<x-group col="8" label="email_verified_at">
					<pre v-if="model.email_verified_at">{{model.email_verified_at | toDate}}</pre>
					<pre v-else>{{$t('not_verified')}}</pre>
				</x-group>
			</x-row>
			<x-row line>
				<x-group col="8" label="stripe_id">
					<pre>{{model.stripe_id || '-'}}</pre>
				</x-group>
				<x-group col="8" label="stripe_id">
					<pre>{{model.card_brand || '-'}}</pre>
				</x-group>
				<x-group col="8" label="stripe_id">
					<pre>{{model.card_last_four || '-'}}</pre>
				</x-group>
				<x-group col="8" label="stripe_id">
					<pre>{{model.trial_ends_at || '-'}}</pre>
				</x-group>
			</x-row>
			<x-row line>
				<x-group col="8" label="extra_billing_info">
					<pre>{{model.extra_billing_info || '-'}}</pre>
				</x-group>
				<x-group col="8" label="lang_id">
					<pre>{{model.lang_id || '-'}}</pre>
				</x-group>
			</x-row>
			<x-row line>
				<x-group col="8" label="created_at">
					<pre>{{model.created_at | toDate}}</pre>
				</x-group>
			</x-row>
		</x-panel>
		<x-panel padding margin v-if="model.subscriptions.length > 0">
			<div slot="title">
				{{$t('subscriptions')}}
			</div>
			<div v-for="sub in model.subscriptions">
				<x-row line>
					<x-group col="8" label="name">
						<pre>{{sub.name}}</pre>
					</x-group>
					<x-group col="8" label="stripe_id">
						<pre>{{sub.stripe_id}}</pre>
					</x-group>
					<x-group col="8" label="stripe_plan">
						<pre>{{sub.stripe_plan}}</pre>
					</x-group>
					<x-group col="8" label="ends_at">
						<pre>{{sub.ends_at}}</pre>
					</x-group>
					<x-group col="8" label="trial_ends_at">
						<pre>{{sub.trial_ends_at}}</pre>
					</x-group>
					<x-group col="8" label="updated_at">
						<pre>{{sub.updated_at}}</pre>
					</x-group>
				</x-row>
			</div>
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
				}
			}
		}
	}
</script>