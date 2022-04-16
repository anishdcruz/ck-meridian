<template>
	<div class="content-inner" v-if="show">
		<x-panel margin padding>
			<div slot="title">
				<router-link to="/team-settings/roles">{{$t('role')}}</router-link> / {{model.name}}
			</div>
			<div slot="extra">
				<router-link :to="`/team-settings/roles`" class="btn btn-default btn-sm">
					<small class="icon icon-arrow-left-c"></small>
				</router-link>
				<router-link :to="`/team-settings/roles/${model.id}/edit`" class="btn btn-default btn-sm">
					<small class="icon icon-edit"></small>
				</router-link>
				<x-button size="sm" type="error" icon="trash-b" @click="removeDB('team-settings/roles', model.id)"></x-button>
			</div>
				<x-row line>
					<x-group col="8" label="code">
						<p>{{model.name}}</p>
					</x-group>
					<x-group col="8" label="description">
						<pre>{{model.description}}</pre>
					</x-group>
					<x-group col="8" label="created_at">
						<pre>{{model.created_at | toDate}}</pre>
					</x-group>
			</x-row>
		</x-panel>
		<x-panel padding>
			<div slot="title">{{$t('permissions')}}</div>
			<div>
				<x-row v-for="(actions, name) in model.permissions.modules" :key="name" line>
					<x-group col="24" :label="name">
						<x-row>
							<x-col span="8" v-for="(value, key) in actions" :key="key">
								<p class="permission">
									<span class="permission-icon">
										<span v-if="value" class="permission-yes icon icon-checkmark-circled"></span>
										<span v-else class="permission-no icon icon-close-circled"></span>
									</span>
									<span class="permission-text">{{$t(key)}}</span>
								</p>
							</x-col>
						</x-row>
					</x-group>
				</x-row>
				<x-row line>
					<x-group col="24" label="Team Settings">
						<x-row>
							<x-col span="8" v-for="(value, key) in model.permissions.settings" :key="key">
								<p class="permission">
									<span class="permission-icon">
										<span v-if="value" class="permission-yes icon icon-checkmark-circled"></span>
										<span v-else class="permission-no icon icon-close-circled"></span>
									</span>
									<span class="permission-text">{{$t(key)}}</span>
								</p>
							</x-col>
						</x-row>
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
					permissions: []
				}
			}
		}
	}
</script>