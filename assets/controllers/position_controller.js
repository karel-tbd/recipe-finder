import {Controller} from '@hotwired/stimulus';

export default class extends Controller {

    static targets = ['meal', 'difficulty', 'country', 'mealType'];

    connect() {
        let difficultyBlock = document.getElementById('difficulty');
        let desire = document.getElementById('desire');
        let countryBlock = document.getElementById('country');
        let mealTypeBlock = document.getElementById('mealType');

        this.selectedMealType = null;
        this.selectedCountry = null;
        this.selectedDifficulty = null;
        this.selectedMeal = null;


        for (const mealType of this.mealTypeTargets) {
            mealType.addEventListener('click', async (event) => {
                let currentMealType = event.currentTarget;
                this.selectedMealType = currentMealType.getAttribute('data-position');
                console.log(this.selectedMealType);
                if (this.selectedMealType === 'dessert' || this.selectedMealType === 'breakfast') {
                    mealTypeBlock.classList.add('hidden');
                    countryBlock.classList.remove('hidden');
                } else {
                    mealTypeBlock.classList.add('hidden');
                    desire.classList.remove('hidden');
                }
            });
        }

        for (const meal of this.mealTargets) {
            meal.addEventListener('click', async (event) => {
                let currentMeal = event.currentTarget;
                this.selectedMeal = currentMeal.getAttribute('data-position');
                if (this.selectedMeal === 'back') {
                    desire.classList.add('hidden');
                    mealTypeBlock.classList.remove('hidden');
                    this.selectedMeal = null;
                } else {
                    desire.classList.add('hidden');
                    countryBlock.classList.remove('hidden');
                }
            });
        }

        for (const country of this.countryTargets) {
            country.addEventListener('click', async (event) => {
                this.selectedCountry = event.currentTarget.getAttribute('data-position');
                if (this.selectedCountry === 'back' && this.selectedMeal === null) {
                    countryBlock.classList.add('hidden');
                    mealTypeBlock.classList.remove('hidden');
                    this.selectedCountry = null;
                } else if (this.selectedCountry === 'back' && this.selectedMeal !== null) {
                    desire.classList.remove('hidden');
                    countryBlock.classList.add('hidden');
                    this.selectedCountry = null;
                } else {
                    countryBlock.classList.add('hidden');
                    difficultyBlock.classList.remove('hidden');
                }
            });
        }

        for (const time of this.difficultyTargets) {
            time.addEventListener('click', async (event) => {
                this.selectedDifficulty = event.currentTarget.getAttribute('data-position');
                if (this.selectedDifficulty === 'back') {
                    difficultyBlock.classList.add('hidden');
                    countryBlock.classList.remove('hidden');
                    this.selectedDifficulty = null;
                } else {
                    difficultyBlock.classList.add('hidden');
                }
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
