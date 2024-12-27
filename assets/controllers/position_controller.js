import {Controller} from '@hotwired/stimulus';

export default class extends Controller {

    static targets = ['meal', 'difficulty', 'country', 'mealType'];

    connect() {
        let difficultyBlock = document.getElementById('difficulty');
        let desire = document.getElementById('desire');
        let countryBlock = document.getElementById('country');
        let mealTypeBlock = document.getElementById('mealType');
        let w = window.innerWidth - 300;
        let h = window.innerHeight - 200;
        const forbiddenArea = {x1: w / 3, y1: h / 3, x2: (w / 3) * 2, y2: (h / 3) * 2};
        const placedElements = [];

        this.selectedMealType;
        this.selectedCountry;
        this.selectedDifficulty;
        this.selectedMeal;

        const getRandomPosition = () => {
            let x, y, attempts = 0;

            do {
                x = Math.floor(Math.random() * w);
                y = Math.floor(Math.random() * h) + 120;

                if (
                    x > forbiddenArea.x1 && x < forbiddenArea.x2 &&
                    y > forbiddenArea.y1 && y < forbiddenArea.y2
                ) {
                    continue;
                }

                const isOverlapping = placedElements.some(el => {
                    return Math.abs(el.x - x) < 100 && Math.abs(el.y - y) < 100; // 100px marge
                });

                if (!isOverlapping) {
                    placedElements.push({x, y});
                    break;
                }
            } while (attempts++ < 100);

            return {x, y};
        };

        const positionElement = (element) => {
            const {x, y} = getRandomPosition();
            element.style.left = x + 'px';
            element.style.top = y + 'px';
        };

        for (const mealType of this.mealTypeTargets) {
            positionElement(mealType);
            mealType.addEventListener('click', async (event) => {
                let currentMealType = event.currentTarget;
                this.selectedMealType = currentMealType.getAttribute('data-position');
                mealTypeBlock.classList.add('hidden');
                desire.classList.remove('hidden');
            });
        }

        for (const meal of this.mealTargets) {
            positionElement(meal);
            meal.addEventListener('click', async (event) => {
                let currentMeal = event.currentTarget;
                this.selectedMeal = currentMeal.getAttribute('data-position');
                desire.classList.add('hidden');
                countryBlock.classList.remove('hidden');
            });
        }

        for (const country of this.countryTargets) {
            positionElement(country);
            country.addEventListener('click', async (event) => {
                this.selectedCountry = event.currentTarget.getAttribute('data-position');
                countryBlock.classList.add('hidden');
                difficultyBlock.classList.remove('hidden');
            });
        }

        for (const time of this.difficultyTargets) {
            positionElement(time);
            time.addEventListener('click', async (event) => {
                this.selectedDifficulty = event.currentTarget.getAttribute('data-position');
                difficultyBlock.classList.add('hidden');

                await fetch('/recipe/find', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        meal: this.selectedMeal,
                        country: this.selectedCountry,
                        difficulty: this.selectedDifficulty,
                        mealType: this.selectedMealType

                    }),
                }).then(async (response) => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    const data = await response.json();
                    if (data.redirectUrl) {
                        window.location.href = data.redirectUrl;
                    }
                });
            });
        }
    }
}
