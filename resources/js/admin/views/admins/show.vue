<template>
	<div class="content-inner" v-if="show">
		<x-panel padding>
			<div slot="title">
				<router-link to="/admins">{{$t('admin')}}</router-link> / {{model.name}}
			</div>
			<div slot="extra">
				<router-link :to="`/admins`" class="btn btn-default btn-sm">
					<small class="icon icon-arrow-left-c"></small>
				</router-link>
				<router-link :to="`/admins/${model.id}/edit`" class="btn btn-default btn-sm">
					<small class="icon icon-edit"></small>
				</router-link>
				<x-button size="sm" type="error" icon="trash-b" @click="removeDB('admins', model.id)"></x-button>
			</div>
			<x-row line>
				<x-group col="12" label="name">
					<pre>{{model.name}}</pre>
				</x-group>
				<x-group col="12" label="email">
					<pre>{{model.email}}</pre>
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
				}
			}
		}
	}
</script>