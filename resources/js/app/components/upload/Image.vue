<template>
  <div class="upload">
  	<div class="upload-inner">
      <div v-if="showPreview" class="upload-image-preview">
        <div class="upload-image-close" @click.prevent="close">
          <span class="icon icon-close"></span>
        </div>
        <img :src="`/storage/${preview}`" class="upload-image-img">
      </div>
      <div v-else>
        <div class="upload-icon">
          <span class="icon icon-images"></span>
        </div>
        <p class="upload-text">{{$t('drag_and_drop')}}</p>
        <div class="upload-browse">
          <x-button size="sm" @click="onBrowse">{{$t('browse')}}</x-button>
          <input v-show="false" ref="input"
            type="file" @change="handleChange">
        </div>
      </div>
    </div>
  </div>
</template>
<script>
  export default {
  	name: 'XImageUpload',
    model: {
      prop: 'value',
      event: 'input'
    },
    props: {
      value: [String, Number],
      url: [String]
    },
  	data() {
  		return {
        show: false,
  		uploadedFile: null,
        uploading: false,
        errors: {}
  		}
  	},
    computed: {
      showPreview() {
        return this.value ? true : false
      },
      preview () {
        return this.value
      }
    },
  	methods: {
      onBrowse() {
         this.$refs.input.click()
      },
  		close() {
  			this.$emit('input', null)
        	this.show = false
  		},
  		handleChange(e) {
  			this.upload(e.target.files[0])
  		},
  		upload(file) {
				if (!file || !file.type.match(/image.*/)) return
				const fileReader = new FileReader()

				fileReader.onload = (event) => {
				    this.uploadedFile = event.target.result
            this.show = true
            this.save(file)
				}

				fileReader.readAsDataURL(file)
  		},
      save(file) {
        this.uploading = false
        let fd = new FormData()
        fd.append('image_upload', file, file.name)
        this.$http.post(this.url, fd)
          .then((res) => {
            this.uploading = false
            this.$emit('input', res.data.image)
            this.$message.success('Successfully upload image!')
          })
          .catch((error) => {
            this.uploading = false
            if(error.response.status === 422) {
                this.errors = error.response.data.errors
            }
            this.$message.error(error.response.data.message)
          })
      }
  	}
  }
</script>