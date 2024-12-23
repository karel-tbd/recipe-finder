import {Controller} from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['fileInput', 'image'];

    connect() {
        this.fileInputTarget.addEventListener('change', this.previewImage.bind(this));
    }

    previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();

            reader.onload = (e) => {
                this.imageTarget.src = e.target.result;
                this.imageTarget.style.display = 'block'; // Maak de afbeelding zichtbaar
            };

            reader.readAsDataURL(file);
        } else {
            this.imageTarget.style.display = 'none'; // Verberg de afbeelding als geen bestand is geselecteerd
        }
    }
}
