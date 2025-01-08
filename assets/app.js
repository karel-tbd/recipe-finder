import './bootstrap.js';
import './styles/app.css';
import 'flowbite/dist/flowbite.turbo.js'
import 'flatpickr/dist/flatpickr.min.css'

window.document.addEventListener("turbo:render", (_event) => {
    window.initFlowbite()
})

function topFunction() {
    window.scrollTo({top: 0, behavior: 'smooth'});
}