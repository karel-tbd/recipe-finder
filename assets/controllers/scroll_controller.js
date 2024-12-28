import {Controller} from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    connect() {
        this.page = 2;
        this.recipeContainer = document.getElementById('recipeBody');
        this.loading = document.getElementById('loading');

        window.addEventListener('scroll', this.loadMore.bind(this));
    }

    loadMore() {
        if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 100) {
            this.loading.style.display = 'block';

            fetch(`?page=${this.page++}`, {
                headers: {'X-Requested-With': 'XMLHttpRequest'},
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text();
                })
                .then(html => {
                    this.recipeContainer.insertAdjacentHTML('beforeend', html);
                    this.loading.style.display = 'none';
                })
                .catch(error => {
                    console.error('Error loading more recipes:', error);
                    this.loading.style.display = 'none';
                });
        }
    }
}