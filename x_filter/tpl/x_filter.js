document
    .getElementById('catmenu')
    .addEventListener('change', function(event) {
        this.form.submit()
        event.preventDefault()
    });

document
    .querySelectorAll('.xf__title')
    .forEach(function(item) {
        item.addEventListener('click', function(event) {
            this.classList.toggle('expanded')
            event.preventDefault()
        })
    });
