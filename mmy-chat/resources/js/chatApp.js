export default function chatApp() {
    return {
        selectedContact: null,
        messages: [],
        newMessage: '',
        loading: false,

        selectContact(contact) {
            this.selectedContact = contact;
            this.fetchMessages(contact.id);
        },
        recentContacts: [],

        init() {
            axios.get(`https://chat-service.test/api/conversations/${CURRENT_USER_ID}`)
                .then(response => {
                    this.recentContacts = response.data.message;
                });
        },

        fetchMessages(contactId) {
            this.loading = true;
            axios.get(`https://chat-service.test/api/messages/${contactId}`)
                .then(response => {
                    this.messages = response.data.messages;
                })
                .catch(error => {
                    console.error('Error al cargar mensajes:', error);
                    this.messages = [];
                })
                .finally(() => {
                    this.loading = false;
                });
        },

        sendMessage() {
            if (!this.newMessage.trim() || !this.selectedContact) return;

            axios.post(`https://chat-service.test/api/messages`, {
                sender_id: CURRENT_USER_ID,
                receiver_id: this.selectedContact.id,
                content: this.newMessage
            })
                .then(response => {
                    this.messages.push(response.data.message);
                    this.newMessage = '';
                    this.scrollToBottom();
                });
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const container = this.$root.querySelector('#message-container');
                container.scrollTop = container.scrollHeight;
            });
        }
    }
}
