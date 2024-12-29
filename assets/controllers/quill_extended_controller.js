import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['quillEditor'];

    connect() {
        this.quill = new Quill(this.quillEditorTarget);


        this.quill.root.innerHTML = this.quillEditorTarget.dataset.value || '';

        this.quill.on('text-change', () => {
            this.syncValue();
        });
    }

    syncValue() {
        const hiddenInput = this.quillEditorTarget.previousElementSibling;
        hiddenInput.value = this.quill.root.innerHTML;
    }
}
