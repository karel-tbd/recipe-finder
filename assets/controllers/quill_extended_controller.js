import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        this.element.addEventListener('quill:connect', this._onConnect);
    }

    disconnect() {
        this.element.removeEventListener('quill:connect', this._onConnect);
    }

    _onConnect(event) {
        // The quill has been created
        console.log(event.detail); // You can access the quill instance using the event detail

        let quill = event.detail;

        quill.on('text-change', function () {
            console.log('hallo')
            document.getElementById('recipe_add_instructions').dispatchEvent(new Event('input'));

            console.log(document.getElementById('recipe_add_instructions').value)
        })
    }
}