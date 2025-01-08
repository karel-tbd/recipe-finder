import {Controller} from '@hotwired/stimulus'
import 'quill/dist/quill.core.css'
import 'quill/dist/quill.snow.css'
import Quill from 'quill'
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['label', 'editor', 'input']

    connect() {
        if (this.hasLabelTarget && this.hasEditorTarget && this.hasInputTarget) {
            let label = this.labelTarget
            let editor = this.editorTarget
            let input = this.inputTarget
            let quill = new Quill(editor, {
                theme: 'snow',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline', 'link'],
                        [{'list': 'ordered'}, {'list': 'bullet'}],
                        [{'indent': '-1'}, {'indent': '+1'}],
                        [{'header': [1, 2, 3, 4, 5, 6, false]}],
                        ['clean']
                    ],
                    clipboard: {
                        matchers: [
                            [Node.TEXT_NODE, function (node, delta) {
                                const Delta = Quill.import('delta')
                                return delta.compose(new Delta().retain(delta.length(), {
                                    background: false,
                                    color: false,
                                    font: false,
                                    size: false,
                                    strike: false,
                                    script: false,
                                }));
                            }],
                            [Node.ELEMENT_NODE, function (node, delta) {
                                const Delta = Quill.import('delta')
                                if (node.tagName === 'IFRAME' || node.tagName === 'VIDEO' || node.tagName === 'IMG') {
                                    return new Delta();
                                }
                                return delta.compose(new Delta().retain(delta.length(), {
                                    align: false,
                                    direction: false,
                                }));
                            }]
                        ]
                    },
                },
            });

            quill.root.innerHTML = input.value
            quill.on('text-change', function () {
                input.value = quill.root.innerHTML
                input.dispatchEvent(new Event('change', {bubbles: true}))
            })
            label.addEventListener('click', function () {
                quill.focus()
            })
        }
    }
}