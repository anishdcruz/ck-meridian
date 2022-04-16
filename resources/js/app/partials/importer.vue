<template>
	<x-modal :loading="isUploading" :footer="showFooter" @close="cancel">
		<div slot="title">
			{{isUploading ? $t('uploading') :$t('upload')}}
			<small>
				<a target="_blank" :href="`/api/csv-template/${url}`">{{$t('download_template')}}</a>
			</small>
		</div>
		<div class="upload">

			<div class="upload-inner" @dragover.prevent="onDragOver"
      	@drop.prevent="onDrop">
				<div class="upload-icon">
					<span class="icon icon-document"></span>
				</div>
				<p class="upload-text">{{$t('drag_and_drop_csv')}}</p>
				<div class="upload-browse">
					<x-button size="sm" @click="onBrowse">{{$t('browse_file')}}</x-button>
					<input v-show="false" ref="input"
        		type="file" @change="handleUpload" multiple>
				</div>
			</div>
		</div>
	</x-modal>
</template>
<script>
	import FilePreview from '@js/components/upload/FilePreview.vue'
	export default {
		components: { FilePreview },
		props: {
			url: String
		},
		data() {
			return {
				isUploading: false,
				showFooter: false,
				files: [],
				errors: {}
			}
		},
		methods: {
			cancel() {
				if(this.isUploading) return
				this.$emit('cancel')
			},
			onBrowse() {
			  this.$refs.input.click()
			},
			onDragOver() {},
			onDrop(e) {
			  const files = e.dataTransfer.files
			  this.files.push(files[0])

			  this.postUpload()
			},
			handleUpload(e) {
        const files = e.target.files
        this.files.push(files[0])
        this.postUpload()
      },
			postUpload() {
			  var fd = new FormData()
			  for(var i = 0; i< this.files.length; i++) {
			    fd.append(`images[${i}]`, this.files[i])
			  }
			  this.save(fd)
			},
			save(fd) {
			  this.isUploading = true
			  this.$http.post(`/api/csv-template/${this.url}`, fd)
			    .then((res) => {
			      if(res.data.saved) {
			      	this.$message.success(this.$t('saved_success'))
			        this.$emit('saved', res.data)
			      }
			    })
			    .catch((error) => {
			      if(error.response.status === 422) {
			          this.errors = error.response.data.errors
			      }
			      this.$message.error(error.response.data.message)
			    })
			    .finally(() => {
			    	this.isUploading = false
			    })
			},
		}
	}
</script>