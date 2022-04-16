<template>
	<div class="content-inner" v-if="show">
		<x-panel padding margin>
			<div slot="title">
				<router-link to="/teams">{{$t('team')}}</router-link> / {{model.name}}
			</div>
			<div slot="extra">
				<router-link :to="`/teams`" class="btn btn-default btn-sm">
					<small class="icon icon-arrow-left-c"></small>
				</router-link>
				<x-button size="sm" type="error" icon="trash-b" @click="removeDB('teams', model.id)"></x-button>
			</div>
			<x-row line>
				<x-group col="8" label="name">
					<pre>{{model.name}}</pre>
				</x-group>
				<x-group col="8" label="owner">
					<router-link :to="`/users/${model.owner_id}`">{{model.owner.name}}</router-link>
				</x-group>
				<x-group col="8" label="is_setup">
					<pre>{{model.is_setup ? $t('yes') : $t('no')}}</pre>
				</x-group>
				<x-group col="8" label="initialized_at">
					<pre>{{model.initialized_at}}</pre>
				</x-group>
			</x-row>
			<x-row line>
				<x-group col="8" label="currency_id">
					<pre>{{model.currency_id}}</pre>
				</x-group>
				<x-group col="8" label="lang_id">
					<pre>{{model.lang_id}}</pre>
				</x-group>
				<x-group col="8" label="timezone">
					<pre>{{model.timezone}}</pre>
				</x-group>
				<x-group col="8" label="date_format">
					<pre>{{model.date_format}}</pre>
				</x-group>
			</x-row>
			<x-row line>
				<x-group col="8" label="stripe_user_id">
					<pre>{{model.stripe_user_id}}</pre>
				</x-group>
				<x-group col="8" label="livemode">
					<pre>{{model.livemode ? $t('yes') : $t('no')}}</pre>
				</x-group>
				<x-group col="8" label="stripe_connected_at">
					<pre v-if="model.stripe_connected_at">{{model.stripe_connected_at | toDate}}</pre>
					<pre v-else>-</pre>
				</x-group>
			</x-row>
			<x-row line>
				<x-group col="8" label="created_at">
					<pre>{{model.created_at | toDate}}</pre>
				</x-group>
			</x-row>
		</x-panel>
		<x-panel>
			<div slot="title">
				{{$t('members')}}
			</div>
			<div>
				<x-table class="table">
					<x-tbody>
						<template v-for="user in model.members">
							<x-tr>
								<x-td>{{user.name}}</x-td>
								<x-td>{{user.email}}</x-td>
							</x-tr>
						</template>
					</x-tbody>
				</x-table>
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