<template>
	<div class="wizard-mask">
		<div class="wizard-wrapper">
			<div class="wizard">
				<div class="wizard-header">
					<img src="../../img/meridian-logo.png" class="wizard-logo">
				</div>
				<div class="wizard-content">
					<h1>{{$t('wizard_welcome')}}</h1>
					<br>
					<p>{{$t('wizard_welcome_2')}}</p>
					<hr>
					<component :is="currentStep" @next="next"></component>
				</div>
			</div>
		</div>
	</div>
</template>
<script>
	import GeneralStep from '@js/partials/wizard/GeneralStep.vue'
	import CompanyStep from '@js/partials/wizard/CompanyStep.vue'
	import TaxStep from '@js/partials/wizard/TaxStep.vue'
	export default {
		components: { GeneralStep, CompanyStep, TaxStep },
		data() {
			return {
				currentStep: 'general-step',
				isCompleted: false
			}
		},
		methods: {
			next(step) {
				if(step === 'general') {
					this.currentStep = 'company-step'
				} else if(step === 'documents') {
					this.currentStep = 'tax-step'
				} else if(step === 'taxes?set=completed') {
					window.location = '/?setup=completed'
				}
			}
		}
	}
</script>