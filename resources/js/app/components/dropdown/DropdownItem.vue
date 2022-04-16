<template>
	<div>
		<div class="dropdown-divide" v-if="divide"></div>
		<div class="dropdown-item">
      <i :class="`dropdown-team-icon icon icon-${icon}`"
                      v-if="icon"></i>
			<slot></slot>
		</div>
	</div>
</template>
<script>
  export default {
  	name: 'XDropdownItem',
  	props: {
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