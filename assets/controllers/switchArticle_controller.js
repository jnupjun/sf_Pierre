import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        console.error(this.element);
        // QUERYSELECTOR > getElementBy..., il est largement supérieur car il sélectionne tous type d'éléments html
        // il fonctionne comme les sélecteurs css
        this.element.querySelector('input[type="checkbox"]')
            .addEventListener('change', async (e) => { // not 'onClick' because we want the user to be able to click on the label too
                // console.error(e.currentTarget); // e.currentTarget concerns the direct html element in a pass of the loop
                const id = e.currentTarget.dataset.articleId;
                console.error(id);

                const response = await fetch(`/admin/articles/${id}/switch`); // await cause we want a response from the server
                //console.log(response);
                // console.log(await response.json()) // this log avoid entering condition below because it altered the nature of the response type ?

                if (response.ok) { // if server http response code is 200
                    const data = await response.json();

                    console.error(data.enable);
                    const label = this.element.querySelector('label');
                    label.textContent = data.enable ? 'Actif' : 'Inactif';

                    if (data.enable) {
                        label.classList.replace('text-danger', 'text-success');
                    } else {
                        label.classList.replace('text-success', 'text-danger');
                    }
                } else {
                    if (!document.querySelector('.alert.alert-danger')) {
                        const alert = document.createElement('div');
                        alert.classList.add('alert', 'alert-danger');
                        alert.textContent = "Une erreur est survenue, veuillez réessayer plus tard.";
                        document.querySelector('main').prepend(alert);
                        window.scrollTo(0, 0);
                        setTimeout(() => {
                            alert.remove()
                        }, 3000);
                    }
                }
            });
        // this.element.textContent = 'Hello Stimulus! Edit me in assets/controllers/hello_controller.js';k
    }
}