import {Controller} from '@hotwired/stimulus';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    connect() {
        let button1 = document.getElementById('nextButton1');
        let button2 = document.getElementById('nextButton2');

        let generalInfo = document.getElementById('addGeneralInfo');
        let ingredients = document.getElementById('addIngredient');
        let intruction = document.getElementById('addInstruction');

        button1.addEventListener('click', () => {
            generalInfo.classList.add('hidden');
            ingredients.classList.remove('hidden');
        })
        button2.addEventListener('click', () => {
            ingredients.classList.add('hidden');
            intruction.classList.remove('hidden');
        })
        console.log('connected to controller');
    }
}
