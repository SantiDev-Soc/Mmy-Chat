export default function contactSearch() {
    return {
        query: '', results: [], open: false, activeIndex: -1,
        search() {
            if (this.query.length < 2) {
                this.results = [];
                this.activeIndex = -1;
                return;
            }

            axios.get('/contacts/search', {
                params: {q: this.query}
            })
                .then(response => {
                    this.results = response.data;
                    this.activeIndex = 0;
                })
                .catch(error => {
                    console.error('Error al buscar contactos:', error);
                });
        },

        moveDown() {
            if (this.activeIndex < this.results.length - 1) {
                this.activeIndex++;
                this.scrollToActive();
            }
        },

        moveUp() {
            if (this.activeIndex > 0) {
                this.activeIndex--;
                this.scrollToActive();
            }
        },

        confirmSelection() {
            if (this.results[this.activeIndex]) {
                this.select(this.results[this.activeIndex]);
            }
        },

        scrollToActive() {
            this.$nextTick(() => {
                const list = this.$root.querySelector('ul');
                const activeItem = list?.children[this.activeIndex];
                if (activeItem) {
                    activeItem.scrollIntoView({ block: 'nearest' });
                }
            });
        },

        select(contact) {
            this.query = contact.name;
            this.open = false;
            console.log('Contact selected:', contact);
            window.location.href = `/chat/${contact.id}`;
        }
    }
}
