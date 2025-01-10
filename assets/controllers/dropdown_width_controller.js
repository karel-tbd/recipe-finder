import {Controller} from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    connect() {
        let button = document.getElementById('multiLevelDropdownButton');

        if (window.innerWidth < 1024) {
            this.element.id = 'multi-dropdown'
            button.setAttribute('data-dropdown-toggle', 'multi-dropdown')
        }
    }
}
