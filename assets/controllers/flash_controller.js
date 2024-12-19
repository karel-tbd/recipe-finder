import {Controller} from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    connect() {
        console.log(this.element);
        setTimeout(() => {
            this.element.classList.add('fade-out');
            setTimeout(() => {
                this.element.style.display = 'none';
            }, 2000);
        }, 5000);
    }
}
