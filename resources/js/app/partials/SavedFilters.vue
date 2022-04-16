<template>
	<x-dropdown width="400" size="sm" dir="right" icon="more">
	  <x-button slot="button" slot-scope="{ toggle }"
	    @click.stop="loadFilters(toggle)" icon="android-bookmark">{{$t('saved_filters')}}</x-button>
	  <x-dropdown-menu style="height: 400px;overflow-y: scroll;" slot="menu">
			<div class="dropdown-title">
				<strong>{{$t('saved_filters')}}</strong>
			</div>
			<div class="dropdown-divide"></div>
	    <template v-if="filters.length > 0">
	      <a  v-for="(f, index) in filters" class="dropdown-link" @click="handleClick(f)">
	        <div class="dropdown-team-item">
	        	<i :class="['dropdown-team-icon icon', f.shared_with === 'team' ? 'icon-ios-people' : 'icon-ios-person']"></i>
	          	<span class="dropdown-team-name">{{f.name}}</span>
	        </div>
	      </a>
	    </template>
	    <template v-else>
	    	<p class="dropdown-title">{{$t('no_filters_found')}}</p>
	    </template>
	  </x-dropdown-menu>
	</x-dropdown>
</template>
<script>
	export default {
		props: {
			resource: String
		},
		data() {
			return {
				filters: []
			}
		},
		methods: {
			handleClick(filter) {
				this.$emit('filter', filter)
			},
			loadFilters(toggle) {
				this.$http.get('/api/filters', {
					params: {
						resource: this.resource
					}
				})
				.then((res) => {
					this.$set(this.$data, 'filters', res.data.collection)
					toggle()
				})
			}
		}
	}
</script>