<template>
	<div class="content-inner" v-if="show">
		<x-panel padding margin>
			<div slot="title">{{$t('your_invoices')}}</div>
			<div>
								<x-row line>
									<x-col span="10">
										<strong>ID</strong>
									</x-col>
									<x-col span="4">
										<strong>Date</strong>
									</x-col>
									<x-col span="10">
										<strong>Amount</strong>
									</x-col>
								</x-row>
								<x-row v-for="invoice in form.invoices" :key="invoice.id" line>
									<x-col span="10">
										<span>{{invoice.id}}</span>
									</x-col>
									<x-col span="4">
										<span>{{invoice.date}}</span>
									</x-col>
									<x-col span="6">
										<span>{{invoice.total}}</span>
									</x-col>
									<x-col span="4">
										<a class="btn btn-default btn-sm"
											:href="invoice.download">Download</a>
									</x-col>
								</x-row>
			</div>
		</x-panel>
	</div>
</template>
<script>
	import { settings } from '@js/lib/mixins'
	export default {
		mixins: [ settings ],
		data() {
			return {
				form: {
				},
				options: {
					timezones: []
				}
			}
		},
		methods: {
			setData(res) {
				this.$set(this.$data, 'form', res.data.form)
				this.$set(this.$data, 'options', res.data.options)
				this.$bar.finish()
				this.show = true
			}
		}
	}
</script>