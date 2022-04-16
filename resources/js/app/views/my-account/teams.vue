<template>
	<div class="content-inner" v-if="show">
		<x-panel padding margin>
			<div slot="title">{{$t('my_teams')}}</div>
			<div slot="extra">
				<x-button v-if="form.subscription.isSubscribed" size="sm" type="primary" icon="plus" @click="toggleTeamModal">{{$t('create_team')}}</x-button>
			</div>
			<div v-if="form.subscription.isSubscribed">
				<div v-if="form.subscription.hasTeams">
					<x-row line>
						<x-col span="6">
							<strong>{{$t(['team'])}}</strong>
						</x-col>
						<x-col span="14">
							<strong>{{$t('members')}}</strong>
						</x-col>
					</x-row>
					<x-row v-for="team in form.teams" :key="team.id" line>
						<x-col span="6">
							<i class="dropdown-team-icon icon icon-checkmark icon-green" v-if="isCurrentTeam(team)"></i>
							<i class="dropdown-team-icon icon icon-android-people"
	                      v-else></i>
							<span>{{team.name}}</span>
						</x-col>
						<x-col span="10">
							<span v-for="(user, i) in team.users">
								<span v-if="i !== 0"> &middot; </span>
								<span :class="`status status-${user.pivot.type === 'owner' ? 'green' : 'grey'}`">
									<span class="status-text">
							  		{{user.name}}
							  	</span>
								</span>
							</span>
						</x-col>
						<x-col span="4">
							<a class="btn btn-default btn-sm" v-if="!isCurrentTeam(team)"
								:href="`/teams/switch/${team.id}`">Switch</a>
							<a class="btn btn-secondary btn-sm" v-else
								href="/team-settings">{{$t('team_settings')}}</a>
						</x-col>
						<x-col span="4">
							<x-button size="sm" type="error" v-if="teamOwner(team)"
								@click="deleteTeam(team)" :loading="deletingTeam">{{$t('delete_team')}}</x-button>
						</x-col>
					</x-row>
				</div>
				<div v-else>
					<x-row line>
						<x-col span="24" class="text-center">
							<p>{{$t('create_a_team')}}</p>
							<a @click.stop="toggleTeamModal">{{$t('create_team_now')}}</a>
						</x-col>
					</x-row>
				</div>
			</div>
			<div v-else>
				<x-row line>
					<x-col span="24" class="text-center">
						<p>{{$t('subs_plan')}}</p>
						<router-link to="/my-account/subscription">{{$t('subs_now')}}</router-link>
					</x-col>
				</x-row>
			</div>
		</x-panel>
		<create-team-modal v-if="showCreateTeam"
			@cancel="toggleTeamModal"
			@ok="handleCreateTeam"></create-team-modal>
	</div>
</template>
<script>
	import { settings } from '@js/lib/mixins'
	import CreateTeamModal from '@js/partials/modals/CreateTeamModal.vue'
	import { state } from '@js/shared/store'
	export default {
		mixins: [ settings ],
		components: { CreateTeamModal },
		data() {
			return {
				state,
				showCreateTeam: false,
				form: {
				},
				options: {
					timezones: []
				},
				deletingTeam: false
			}
		},
		computed: {
			teamOwner() {
				return (team) => {
					return team.owner_id === this.state.user.id
				}
			},
	      isCurrentTeam() {
	        return (team) => {
	          if(!team) { return false }
	          return team.id === this.state.current_team.id
	        }
      },
    },
		methods: {
			deleteTeam(team) {
				const r = confirm(this.$t('are_you_sure'))
				if(r != true) { return }
				this.deletingTeam = true
				this.$http.delete(`/api/my-account/teams/${team.id}`, this.form)
					.then((res) => {
						this.$message.success(this.$t('saved_success'))
						window.location.replace(`/my-account/teams`)
					})
					.catch(this.catch)
					.finally(() => {
						this.deletingTeam = false
					})
			},
			handleCreateTeam(team) {
				window.location.replace(`/teams/switch/${team.id}`);
			},
			toggleTeamModal() {
				this.showCreateTeam = !this.showCreateTeam
			},
			setData(res) {
				this.$set(this.$data, 'form', res.data.form)
				this.$set(this.$data, 'options', res.data.options)
				this.$bar.finish()
				this.show = true
			},
			save() {
				this.isSaving = true
				this.errors = {}

				const { url, method } = this.getForm()

				this.$http[method](url, this.form)
					.then((res) => {
						this.$message.success(this.$t('saved_success'))
					})
					.catch(this.catch)
					.finally(() => {
						this.isSaving = false
					})
			},
		}
	}
</script>