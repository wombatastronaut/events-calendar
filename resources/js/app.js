import Vue from 'vue'
import VueSweetalert2 from 'vue-sweetalert2'
import 'sweetalert2/dist/sweetalert2.min.css'
import App from './components/App.vue'

Vue.use(VueSweetalert2)

Vue.mixin({
    methods: {
        showError () {
            this.$swal({
                icon: 'info',
                title: 'Try Again',
                text: 'A problem was encountered while processing your request',
                timer: 3000
            })
        }
    }
})

const app = new Vue({
    el: '#app',
    components: { App }
});
