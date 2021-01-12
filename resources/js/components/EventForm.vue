<template>
	<div class="event-form-container">
		<h2 class="text-2xl mb-5">{{ formTitle }}</h2>

		<form @submit.prevent="submit" @keydown="errors = {}" class="event-form">
			<div class="form-group mb-5">
				<label class="block mb-2">Title</label>
				<input v-model="formData.title" type="text">
				<span v-if="errors.title" class="error-message block text-sm mt-2">{{ errors.title }}</span>
			</div>

			<div class="form-group mb-5">
				<label class="block mb-2">Dates</label>
				<date-picker v-model="formData.dateRange" valueType="format" range />
				<span v-if="errors.dateRange" class="error-message block text-sm mt-2">{{ errors.dateRange }}</span>
			</div>

			<div class="form-group mb-5">
				<div class="mb-2">
					Days of Week <span class="text-sm">(Frequency)</span>
				</div>

				<div class="checkbox-group flex gap-4">
					<div v-for="(dayOfWeek, index) in daysOfWeek" :key="index" class="flex justify-center items-center gap-1">
						<label>{{ dayOfWeek.label }}</label>
						<input @change="errors = {}" v-model="formData.frequency" type="checkbox" :value="dayOfWeek.value">
					</div>
				</div>
				<span v-if="errors.frequency" class="error-message block text-sm mt-2">{{ errors.frequency }}</span>
			</div>

			<div class="form-actions">
				<button type="submit" class="primary">Save</button>
				<button type="button" @click="resetFields" class="secondary">Reset</button>
			</div>
		</form>
	</div>
</template>

<script>
import axios from 'axios'
import DatePicker from 'vue2-datepicker'
import 'vue2-datepicker/index.css'

export default {
	name: 'EventForm',

	components: {
		DatePicker
	},

	props: {
		initialFormData: {
            type: Object
        }
	},

	data: () => ({
		formData: {
			id: '',
			title: '',
			dateRange: '',
			frequency: []
		},
		formTitle: 'Create Event',
		errors: {},
		daysOfWeek: [
			{
				label: 'Mon',
				value: 1
			},
			{
				label: 'Tue',
				value: 2
			},
			{
				label: 'Wed',
				value: 3
			},
			{
				label: 'Thu',
				value: 4
			},
			{
				label: 'Fri',
				value: 5
			},
			{
				label: 'Sat',
				value: 6
			},
			{
				label: 'Sun',
				value: 0
			},
		]
	}),

	mounted () {
		if (this.initialFormData) {
			this.formTitle = 'Edit Event'
			this.fillFields()
		}
	},

	methods: {
		submit () {
			const isValid = this.validate()

			if (!isValid) {
				return
			}

			const payload = this.preparePayload()
			
			if (payload.id) {
				this.update(payload)
				return 
			}

			this.create(payload)
		},

		validate () {
			let errors = {}

			for (let key in this.formData) {
				switch (key) {
					case 'title':
						if (!this.formData[key]) {
							errors[key] = 'The title field is required'
						}
						break
					case 'dateRange':
						if (!this.formData[key]) {
							errors[key] = 'The dates field is required'
						}
						break
					case 'frequency':
						if (this.formData[key],length === 0) {
							errors[key] = 'The days of week field is required'
						}
				}
			}

			this.errors = errors
			console.log(this.errors)

			return Object.keys(this.errors).length === 0 && this.errors.constructor === Object
		},


		async create (payload) {
			try {
				const { data } =  await axios.post('/api/events', payload)
				
				if (!data.success) {
					alert('An error was encountered while processing your request')
				}

				this.success('You have successfully created an event!')
			} catch {}
		},

		async update (payload) {
			try {
				const { data } =  await axios.patch(`/api/events/${payload.id}`, payload)
				
				if (!data.success) {
					alert('An error was encountered while processing your request')
				}

				this.success('You have successfully updated the event!')
			} catch {}
		},

		success (message) {
			this.resetFields()
			this.$emit('success')
			this.$swal({
				icon: 'success',
				title: 'Success',
				text: message,
				timer: 1500
			})
		},

		preparePayload () {
			let payload = this.formData
			const [start, end] = this.formData.dateRange

			payload.start = start
			payload.end = end
			delete payload.dateRange

			return payload
		},

		fillFields () {
			for (let key in this.formData) {
				if (this.initialFormData[key]) {
					this.formData[key] = this.initialFormData[key]
				}
			}
		},

		resetFields () {
			for (let key in this.formData) {
				const initialValue = key == 'frequency' ? [] : ''
				this.formData[key] = initialValue
			}

			this.errors = {}
		}
	}
}
</script>

<style lang="scss" scoped>
.form-group {
	input, ::v-deep .mx-input {
		height: inherit;
		padding: 0.5rem;
		border: 1px solid #DDDDDD;
		border-radius: 0.25rem;
		font-size: inherit;
		color: inherit;
		line-height: inherit;
	}

	.error-message {
		color: red;
	}
}

.form-actions {
	button {
		padding: 0.5rem 1rem;
		background: #FFFFFF;
		border: 1px solid #000000;
		border-radius: 0.25rem;
		color: #000000;

		&.primary {
			background: #3788D8;
			border-color: #3788D8;
			color: #FFFFFF;
		}

		&.secondary {
			background: #757575;
			border-color: #757575;
			color: #FFFFFF;
		}
	}
}
</style>
