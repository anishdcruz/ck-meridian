import axios from 'axios'

const http = axios.create({
	baseURL: `${window.ck_init.admin_prefix || '/'}`
})

export default http