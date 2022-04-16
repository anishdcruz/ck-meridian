<template>
	<button @click.stop="onClick" type="submit" class="btn btn-primary" :disabled="isDisabled">
	  <slot></slot>
	</button>
</template>
<script>
	export default {
		data() {
			return {
				isDisabled: false
			}
		},
		methods: {
			onClick() {
				setTimeout(() => {
					this.isDisabled = true
				}, 0)
			}
		}
	}
</script>