<template>
	<div class="content-inner" v-if="show">
		<x-panel padding>
			<div slot="title">
				<router-link to="/plans">{{$t('plan')}}</router-link> / {{model.name}}
			</div>
			<div slot="extra">
				<router-link :to="`/plans`" class="btn btn-default btn-sm">
					<small class="icon icon-arrow-left-c"></small>
				</router-link>
				<router-link :to="`/plans/${model.id}/edit`" class="btn btn-default btn-sm">
					<small class="icon icon-edit"></small>
				</router-link>
				<x-button size="sm" type="error" icon="trash-b" @click="removeDB('plans', model.id)"></x-button>
			</div>
			<x-row line>
				<x-group col="8" label="id">
					<pre>{{model.id}}</pre>
				</x-group>
				<x-group col="8" label="name">
					<pre>{{model.name}}</pre>
				</x-group>
				<x-group col="8" label="slug">
					<pre>{{model.slug}}</pre>
				</x-group>
				<x-group col="8" label="gateway_id">
					<pre>{{model.gateway_id}}</pre>
				</x-group>
				<x-group col="8" label="time_period">
					<pre>{{model.time_period}}</pre>
				</x-group>
				<x-group col="8" label="price">
					<pre>{{model.price}}</pre>
				</x-group>
				<x-group col="8" label="active">
					<pre>{{model.active ? $t('yes') : $t('no')}}</pre>
				</x-group>
			</x-row>
			<x-row line>
				<x-group col="8" label="features">
					<ul>
						<li v-for="item in model.list">
							<pre>{{item}}</pre>
						</li>
					</ul>
				</x-group>
				<x-group col="8" label="limits">
					<ul>
						<li v-for="item in model.limits">
							<pre>{{$t(item.name)}} - {{item.max}}</pre>
						</li>
					</ul>
				</x-group>
				<x-group col="8" label="note">
					<pre>{{model.note || '-'}}</pre>
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