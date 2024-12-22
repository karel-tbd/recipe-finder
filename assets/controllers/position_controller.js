import {Controller} from '@hotwired/stimulus';


export default class extends Controller {

    static targets = ['meal', 'difficulty', 'country'];

    connect() {
        let difficultyBlock = document.getElementById('difficulty');
        let desire = document.getElementById('desire');
        let countryBlock = document.getElementById('country');
        let w = window.innerWidth - 200;
        let h = window.innerHeight - 200;
        this.selectedMealType;
        this.selectedCountry;
        this.selectedDifficulty;

        for (const meal of this.mealTargets) {
            meal.style.left = Math.floor((Math.random() * w) + 1) + 'px';
            meal.style.top = Math.floor((Math.random() * h) + 100) + 'px';

            meal.addEventListener('click', async (event) => {
                let currentMeal = event.currentTarget
                this.selectedMealType = currentMeal.getAttribute('data-position');
                desire.classList.add('hidden');
                countryBlock.classList.remove('hidden');
            })
        }

        for (const country of this.countryTargets) {
            country.style.left = Math.floor((Math.random() * w) + 1) + 'px';
            country.style.top = Math.floor((Math.random() * h) + 100) + 'px';

            country.addEventListener('click', async (event) => {

                this.selectedCountry = event.currentTarget.getAttribute('data-position');
                countryBlock.classList.add('hidden');
                difficultyBlock.classList.remove('hidden');
            })
        }

        for (const time of this.difficultyTargets) {
            time.style.left = Math.floor((Math.random() * w) + 1) + 'px';
            time.style.top = Math.floor((Math.random() * h) + 100) + 'px';

            time.addEventListener('click', async (event) => {
                this.selectedDifficulty = event.currentTarget.getAttribute('data-position');
                difficultyBlock.classList.add('hidden');

                await fetch('/recipe/find', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        mealType: this.selectedMealType,
                        country: this.selectedCountry,
                        difficulty: this.selectedDifficulty,
                    }),
                }).then(async (response) => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    const data = await response.json();
                    if (data.redirectUrl) {
                        window.location.href = data.redirectUrl;
                    }
                })
            })
        }
    }

}
