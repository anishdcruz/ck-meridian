<template>
	<div>
		<div class="dropdown-divide" v-if="divide"></div>
		<router-link class="dropdown-link" :to="to">
      <i :class="`dropdown-team-icon icon icon-${icon}`"
                      v-if="icon"></i>
			<slot></slot>
		</router-link>
	</div>
</template>
<script>
  export default {
  	name: 'XDropdownLink',
  	props: {
      to: String,
  		divide: {
  			type: Boolean,
  			default: false
  		},
      icon: {
        type: String,
        default: null
      }
  	}
  }
</script>