import {Controller} from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    connect() {
        this.element.addEventListener('keydown', this.preventEnterKey.bind(this));
    }

    preventEnterKey(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            
            document.activeElement.blur();
        }
    }
}
