<template>
	<transition name="fade">
  	<div class="switch" :tabindex="disabled ? -1 : tabindex" :class="[{'switch-checked': checkValue, 'switch-disabled': disabled}]"
      @click="toggleSwitch" @keydown.space.prevent="toggleSwitch">
    </div>
  </transition>
</template>
<script>
  export default {
  	name: 'XSwitch',
  	model: {
  	  prop: 'value',
  	  event: 'input'
  	},
  	props: {
  	  value: [Boolean, Number, String],
  	  disabled: {
  	    type: Boolean,
  	    default: false
  	  },
      tabindex: {
        default: 0
      }
  	},
    computed: {
      checkValue() {
        return Number(this.value)
      }
    },
  	methods: {
  	  toggleSwitch(e) {
  	  	if(this.disabled) return
          if(this.checkValue === 1) {
            return this.$emit('input', 0)
          }
          this.$emit('input', 1)
  	  }
  	}
  }
</script>