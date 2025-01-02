import {Controller} from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {

    static targets = ['amount'];

    connect() {
        let peopleDisplay = document.getElementById('peopleDisplay');

        for (const amount of this.amountTargets) {
            amount.addEventListener('click', async (event) => {
                let currentAmount = event.currentTarget;
                peopleDisplay.textContent = currentAmount.getAttribute('data-recipe-people');
            });
        }
    }
}
