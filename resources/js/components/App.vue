<template>
	<div>
		<header>
			<div class="container w-full flex justify-center mx-auto py-8">
				<h1 class="text-4xl">Events Calendar</h1>
			</div>
		</header>
		<main>
			<div class="container mx-auto py-9">
				<event-form :initial-form-data="selectedEvent" @success="refreshEvents" :key="eventFormKey" />
				<full-calendar :options="calendarOptions" />
			</div>
		</main>
	</div>
</template>
<script>
import FullCalendar from '@fullcalendar/vue'
import dayGridPlugin from '@fullcalendar/daygrid'
import listPlugin from '@fullcalendar/list'
import interactionPlugin from '@fullcalendar/interaction'
import EventForm from './EventForm'
import axios from 'axios'

export default {
	components: {
		FullCalendar,
		EventForm
	},

	data: () => ({
		calendarOptions: {
			plugins: [dayGridPlugin, listPlugin, interactionPlugin],
			initialView: 'dayGridMonth',
			headerToolbar: {
				left: 'prev,next',
				center: 'title',
				right: 'dayGridMonth,listMonth' 
			},
			events: []
		},
		selectedEvent: null,
		eventFormKey: 0
	}),

	mounted () {
		this.refreshEvents()
	},

	methods: {
		async fetchEvents () {
			try {
				const { data } = await axios.get('/api/events')
				this.calendarOptions.events = data
			} catch (error) {
				this.showError()
			}
		},

		async fetchEvent (id) {
			try {
				const { data } = await axios.get(`/api/events/${id}`)
				this.selectedEvent = this.prepareEventFormData(data)
				this.eventFormKey += 1;
			} catch (error) {
				this.showError()
			}
		},

		async deleteEvent (id) {
			try {
				const { data } = await axios.delete(`/api/events/${id}`)
				this.refreshEvents()
			} catch (error) {
				this.showError()
			}
		},

		refreshEvents () {
			this.fetchEvents()
			this.calendarOptions.eventClick = this.eventSelect
		},

		eventSelect (arg) {
			this.$swal({
				title: 'What action do you want to do with this event?',
				icon: 'question',
				showCloseButton: true,
				showDenyButton: true,
				confirmButtonText: 'Edit',
				denyButtonText: 'Delete',
			}).then((result) => {
				if (result.isConfirmed) {
					this.fetchEvent(arg.event.id)
				} else if (result.isDenied) {
					this.$swal({
						title: 'Are you sure you want to delete this event?',
						icon: 'question',
						showCloseButton: true,
						showCancelButton: true,
						confirmButtonText: 'Yes'
					}).then((result) => {
						if (result.isConfirmed) {
							this.deleteEvent(arg.event.id)
						}
					})
				}
			})
		},

		prepareEventFormData (data) {
			let result = {}
			const availableEventFields = ['id', 'title', 'dateRange', 'frequency']

			availableEventFields.forEach((key) => {
				let value = data[key] || ''

				if (key == 'dateRange') {
					value = [data.start, data.end]
				}

				result[key] = value
			})

			return result
		}
	}
}
</script>

<style lang="scss" scoped>
header > div {
	border-bottom: 1px solid #DDDDDD;
}

main > div {
	display: grid;
	grid-template-columns: 30% 1fr;
}

::v-deep .fc-event-main-frame {
	cursor: pointer;
}

::v-deep .fc-toolbar-title {
    font-size: 1.5rem;
    line-height: 2rem;
}
</style>