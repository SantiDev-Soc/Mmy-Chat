export default function contactSearch(routeUrl) {
    return {
        query: '',
        results: [],
        searchContacts() {
            if (this.query.length < 2) {
                this.results = [];
                return;
            }

            fetch(`${routeUrl}?q=${encodeURIComponent(this.query)}`)
                .then(res => res.json())
                .then(data => {
                    this.results = data;
                });
        },
        selectContact(contact) {
            console.log('Contacto seleccionado:', contact);
            // Aquí puedes emitir evento, redirigir, etc.
        }
    };
}
