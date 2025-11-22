export default function (loggedUserId) {
    return {
        search: '',
        results: [],
        open: false,
        activeIndex: -1,
        selectedContact: {},
        chats: [],
        messages: [],
        newMessage: '',
        currentUserId: loggedUserId,
        activeChannel: null,
        // Limpiamos la barra final si existe en el .env para evitar dobles barras
        apiUrl: (import.meta.env.VITE_CHAT_API_URL || 'https://localhost').replace(/\/$/, ''),

        init() {
            console.log('Componente iniciado. Usuario:', this.currentUserId);

            if (this.currentUserId) {
                this.loadConversations(this.currentUserId);
            } else {
                console.error("⚠️ No se detectó usuario logueado.");
            }
        },

        async fetchResults() {
            if (this.search.length < 1) {
                this.results = [];
                this.open = false;
                return;
            }
            try {
                const response = await fetch(`/contacts/search?q=${this.search}`);
                this.results = await response.json();
                this.open = this.results.length > 0;
                this.activeIndex = -1;
            } catch (e) {
                console.error("Error buscando contactos:", e);
            }
        },

        async select(contact) {
            // 1. Limpieza visual inmediata
            this.search = contact.name;
            this.selectedContact = {id: contact.id, name: contact.name};
            this.open = false;
            this.messages = []; // Borramos mensajes viejos para evitar "fantasmas"

            // 2. Gestión del Sidebar (añadir o mover chat)
            const chatIndex = this.chats.findIndex(c =>
                String(c.participant_id || c.id) === String(contact.id)
            );

            if (chatIndex === -1) {
                this.chats.unshift({
                    id: contact.id,
                    participant_id: contact.id,
                    name: contact.name,
                    created_at: new Date().toISOString()
                });
            } else {
                // Opcional: Mover al principio
                // const existingChat = this.chats.splice(chatIndex, 1)[0];
                // this.chats.unshift(existingChat);
            }

            if (!this.currentUserId) return;

            // 3. Cargar historial de mensajes (API)
            try {
                const res = await fetch(`${this.apiUrl}/api/messages/${contact.id}?user_id=${this.currentUserId}`, {
                    headers: {'Accept': 'application/json'}
                });

                // CORRECCIÓN CRÍTICA: Primero obtenemos el JSON, luego lo usamos
                const data = await res.json();

                // Ahora sí podemos debuguear
                /*
                console.group("🔍 DEBUG DATA MENSAJES");
                if (data.message && data.message.length > 0) {
                    console.log("Primer mensaje:", data.message[0]);
                }
                console.groupEnd();
                */

                if (data.success) {
                    this.messages = data.message;
                    this.scrollToBottom();
                } else {
                    this.messages = [];
                }
            } catch (e) {
                console.error("Error cargando mensajes:", e);
                this.messages = [];
            }

            // 4. Conexión WebSocket
            this.listenToConversation(contact.id);
        },

        listenToConversation(contactId) {
            if (this.activeChannel) {
                window.Echo.leave(this.activeChannel);
                this.activeChannel = null;
            }

            // Lógica de nombres de canal robusta
            const participants = [String(this.currentUserId), String(contactId)].sort();
            const channelName = `conversation.${participants[0]}.${participants[1]}`;

            this.activeChannel = channelName;
            console.log("🔌 Conectando al canal:", channelName);

            window.Echo.private(channelName)
                .listen('.message.sent', (e) => {
                    // 1. Evitar rebote propio
                    if (String(e.sender_id) === String(this.currentUserId)) return;

                    // 2. Evitar duplicados por ID
                    const existe = this.messages.some(msg => String(msg.id) === String(e.id));
                    if (existe) return;

                    console.log("✅ Mensaje recibido");
                    this.messages.push(e);
                    this.scrollToBottom();
                })
                .error((error) => {
                    console.error("❌ Error conexión Reverb:", error);
                });
        },

        async loadConversations(userId) {
            try {
                const res = await fetch(`${this.apiUrl}/api/conversations/${userId}`, {
                    headers: {'Accept': 'application/json'}
                });
                const data = await res.json();
                this.chats = data.success ? data.message : [];
            } catch (e) {
                console.error("Error cargando conversaciones:", e);
            }
        },

        async selectChat(chat) {
            let contactId = chat.participant_id || chat.id;
            // Normalizamos el objeto contact para pasarlo a select
            await this.select({
                id: contactId,
                name: chat.name
            });
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const container = document.getElementById('messages-container');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
        },

        nextResult() { if (this.activeIndex < this.results.length - 1) this.activeIndex++; },
        prevResult() { if (this.activeIndex > 0) this.activeIndex--; },
        chooseResult() { if (this.activeIndex >= 0) this.select(this.results[this.activeIndex]); },

        async sendMessage() {
            if (!this.newMessage.trim()) return;
            if (!this.currentUserId || !this.selectedContact.id) return;

            const payload = {
                sender_id: this.currentUserId,
                receiver_id: this.selectedContact.id,
                content: this.newMessage
            };

            try {
                const res = await fetch(`${this.apiUrl}/api/messages`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();

                if (data.success) {
                    this.messages.push(data.message);
                    this.newMessage = '';
                    this.scrollToBottom();
                }
            } catch (e) {
                console.error("Error enviando:", e);
            }
        }
    }
}
