import './bootstrap.js';
import './styles/app.css';
import 'flowbite/dist/flowbite.turbo.js'
import 'flatpickr/dist/flatpickr.min.css'

// Flowbite fix after form submit with a 422
window.document.addEventListener("turbo:render", (_event) => {
    window.initFlowbite()
})

