import {Controller} from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['item'];

    connect() {
        let dropdown = document.getElementById('peopleDropdown');

        for (const item of this.itemTargets) {
            item.addEventListener('click', async (event) => {
                dropdown.classList.add('hidden');
            });
        }
    }
}
