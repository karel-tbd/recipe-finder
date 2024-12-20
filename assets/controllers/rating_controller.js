import {Controller} from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static values = {
        uuid: String,
        score: Number,
    }

    static targets = ['star'];

    connect() {
        this.clicked = this.scoreValue - 1;
        this.updateStar(this.clicked);

        for (const star of this.starTargets) {
            star.addEventListener('click', async (event) => {

                let currentStar = event.currentTarget
                this.clicked = currentStar.getAttribute('data-rating');
                this.updateStar(this.clicked);

                await fetch('/recipe/rating', {
                    method: 'POST',
                    body: JSON.stringify({
                        clicked: this.clicked,
                        uuid: this.uuidValue,
                    })
                })
            })

            star.addEventListener('mouseover', (event) => {
                let currentStar = event.currentTarget
                let clicked = currentStar.getAttribute('data-rating');
                this.updateStar(clicked);
            })

            star.addEventListener('mouseout', () => {
                if (this.clicked === null) {
                    this.resetStars();
                } else {
                    this.updateStar(this.clicked);
                }
            })
        }

    }

    updateStar(clicked) {
        this.starTargets.forEach(star => {
            const currentStar = star.getAttribute('data-rating');
            if (currentStar <= clicked) {
                star.style.color = "#EF4444";
            } else {
                star.style.color = "#9CA3AF";
            }
        });
    }

    resetStars() {
        this.starTargets.forEach(star => {
            star.style.color = "#9CA3AF";
        });
    }
}
