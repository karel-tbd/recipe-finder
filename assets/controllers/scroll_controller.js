/*
import {Controller} from '@hotwired/stimulus';

/!* stimulusFetch: 'lazy' *!/
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
}*/
import {Controller} from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['loader'];

    loaderTargetConnected(element) {
        this.observer ??= new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.dispatchEvent(new CustomEvent('scroll', {detail: {entry}}));
                }
            });
        });
        this.observer?.observe(element);
    }

    loaderTargetDisconnected(element) {
        this.observer?.unobserve(element);
    }
}
