<template>
	<div class="content-inner" v-if="show">
		<x-panel padding>
			<div slot="title">
				<router-link to="/currencies">{{$t('currency')}}</router-link> / {{model.id}}
			</div>
			<div slot="extra">
				<router-link :to="`/currencies`" class="btn btn-default btn-sm">
					<small class="icon icon-arrow-left-c"></small>
				</router-link>
				<router-link :to="`/currencies/${model.id}/edit`" class="btn btn-default btn-sm">
					<small class="icon icon-edit"></small>
				</router-link>
				<x-button size="sm" type="error" icon="trash-b" @click="removeDB('currencies', model.id)"></x-button>
			</div>
			<x-row line>
				<x-group col="12" label="name">
					<pre>{{model.name}}</pre>
				</x-group>
				<x-group col="12" label="code">
					<pre>{{model.code}}</pre>
				</x-group>
				<x-group col="12" label="precision">
					<pre>{{model.precision}}</pre>
				</x-group>
				<x-group col="12" label="subunit">
					<pre>{{model.subunit}}</pre>
				</x-group>
				<x-group col="12" label="symbol">
					<pre>{{model.symbol}}</pre>
				</x-group>
				<x-group col="12" label="symbol_first">
					<pre>{{model.symbol_first}}</pre>
				</x-group>
				<x-group col="12" label="decimal_mark">
					<pre>{{model.decimal_mark}}</pre>
				</x-group>
				<x-group col="12" label="thousands_separator">
					<pre>{{model.thousands_separator}}</pre>
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