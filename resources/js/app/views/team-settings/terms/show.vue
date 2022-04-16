<template>
	<div class="content-inner" v-if="show">
		<x-panel margin padding>
			<div slot="title">
				<router-link to="/team-settings/terms">{{$t('term')}}</router-link> / {{model.label}}
			</div>
			<div slot="extra">
				<router-link :to="`/team-settings/terms`" class="btn btn-default btn-sm">
					<small class="icon icon-arrow-left-c"></small>
				</router-link>
				<router-link :to="`/team-settings/terms/${model.id}/edit`" class="btn btn-default btn-sm">
					<small class="icon icon-edit"></small>
				</router-link>
				<x-button size="sm" type="error" icon="trash-b" @click="removeDB('team-settings/terms', model.id)"></x-button>
			</div>
				<x-row line>
					<x-group col="8" label="label">
						<pre>{{model.label}}</pre>
					</x-group>
					<x-group col="16" label="description">
						<pre>{{model.description}}</pre>
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
					permissions: []
				}
			}
		}
	}
</script>