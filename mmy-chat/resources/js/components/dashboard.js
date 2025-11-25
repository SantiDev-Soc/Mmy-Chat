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
        unreadCounts: {}, // { 'uuid': 5 }

        // Aseguramos HTTPS y quitamos barra final
        apiUrl: (import.meta.env.VITE_CHAT_API_URL || 'https://localhost').replace(/\/$/, ''),

        init() {
            console.log('🚀 Componente iniciado. Usuario:', this.currentUserId);

            if (this.currentUserId) {
                this.loadConversations(this.currentUserId);
                // Escuchar notificaciones globales (Sidebar)
                this.listenToGlobalNotifications();
            } else {
                console.error("⚠️ Error: No se detectó usuario logueado.");
            }
        },

        // --- 1. NOTIFICACIONES GLOBALES (Sidebar) ---
        listenToGlobalNotifications() {
            const myGlobalChannel = `user.${this.currentUserId}`;
            console.log("🔔 Escuchando notificaciones en:", myGlobalChannel);

            window.Echo.private(myGlobalChannel)
                .listen('.message.sent', (e) => {
                    console.log("🔔 Notificación recibida (Raw):", e);

                    // 1. NORMALIZACIÓN DE DATOS (CRÍTICO)
                    // Obtenemos el ID y lo forzamos a minúsculas y string
                    const rawSenderId = e.sender_id || e.senderId || e.payload?.sender_id;
                    const senderId = String(rawSenderId).toLowerCase();
                    const msgId = e.id || e.payload?.id;

                    // ID del contacto abierto (si lo hay), también normalizado
                    const currentOpenChatId = this.selectedContact.id
                        ? String(this.selectedContact.id).toLowerCase()
                        : null;

                    // CASO A: El chat está abierto y es el mismo usuario
                    if (currentOpenChatId === senderId) {
                        console.log("👀 Chat abierto, marcando leído...");
                        this.markAsRead(msgId);
                    }
                    // CASO B: El chat está cerrado o es otra persona
                    else {
                        // Calcular nuevo valor
                        const currentCount = this.unreadCounts[senderId] || 0;

                        // ⚡ REACTIVIDAD: Reasignar objeto completo
                        this.unreadCounts = {
                            ...this.unreadCounts,
                            [senderId]: currentCount + 1
                        };

                        console.log("🔴 Nuevo contador para", senderId, ":", this.unreadCounts[senderId]);

                        // --- GESTIÓN DEL SIDEBAR ---
                        // Buscamos normalizando IDs
                        const chatIndex = this.chats.findIndex(c =>
                            String(c.participant_id || c.id).toLowerCase() === senderId
                        );

                        if (chatIndex > -1) {
                            // A) EL CHAT YA EXISTE: Lo movemos arriba
                            console.log("🔄 Moviendo chat existente al inicio");
                            const chat = this.chats.splice(chatIndex, 1)[0];
                            this.chats.unshift(chat);
                        } else {
                            // B) EL CHAT NO EXISTE: Lo creamos nosotros manualmente
                            console.log("🆕 Contacto nuevo, inyectando en sidebar...");

                            // 1. Creamos un objeto chat temporal
                            const newChat = {
                                id: senderId,
                                participant_id: senderId,
                                name: "Nuevo Mensaje...", // Placeholder mientras carga
                                created_at: new Date().toISOString()
                            };

                            // 2. Lo metemos el primero en la lista (Visualización inmediata)
                            this.chats.unshift(newChat);

                            // 3. Pedimos el nombre real a tu API de usuarios
                            // Asegúrate de tener la ruta /user/{id} configurada en web.php
                            fetch(`/user/${senderId}`)
                                .then(res => res.json())
                                .then(user => {
                                    // Buscamos el chat que acabamos de meter para actualizarle el nombre
                                    const chatToUpdate = this.chats.find(c =>
                                        String(c.id || c.participant_id).toLowerCase() === senderId
                                    );

                                    if (chatToUpdate && user.name) {
                                        chatToUpdate.name = user.name; // ¡Nombre actualizado!
                                    }
                                })
                                .catch(err => console.error("Error recuperando nombre de usuario:", err));
                        }
                    }
                });
        },

        // --- 2. LÓGICA DEL CHAT ---
        async select(contact) {
            this.search = contact.name;
            this.selectedContact = {id: contact.id, name: contact.name};
            this.open = false;
            this.messages = []; // Limpiar UI

            // 1. Resetear contador rojo (usando ID normalizado)
            const contactIdLower = String(contact.id).toLowerCase();
            this.unreadCounts = {
                ...this.unreadCounts,
                [contactIdLower]: 0
            };

            // 2. Gestión visual del Sidebar
            const chatIndex = this.chats.findIndex(c => String(c.participant_id || c.id) === String(contact.id));
            if (chatIndex === -1) {
                this.chats.unshift({
                    id: contact.id,
                    participant_id: contact.id,
                    name: contact.name,
                    created_at: new Date().toISOString()
                });
            }

            if (!this.currentUserId) return;

            // 3. Cargar historial API
            try {
                const res = await fetch(`${this.apiUrl}/api/messages/${contact.id}?user_id=${this.currentUserId}`, {
                    headers: {'Accept': 'application/json'}
                });
                const data = await res.json();

                if (data.success) {
                    this.messages = data.message;
                    this.scrollToBottom();

                    // 4. DETECTAR NO LEÍDOS EN HISTORIAL
                    const unreadIds = this.messages
                        .filter(m =>
                            String(m.sender_id).toLowerCase() !== String(this.currentUserId).toLowerCase()
                            && !m.read_at
                        )
                        .map(m => m.id);

                    if (unreadIds.length > 0) {
                        // Opción: Enviar en lote si tu API lo permite, o uno a uno
                        unreadIds.forEach(id => this.markAsRead(id));
                    }

                } else {
                    this.messages = [];
                }
            } catch (e) {
                console.error("Error cargando mensajes:", e);
                this.messages = [];
            }

            this.listenToConversation(contact.id);
        },

        listenToConversation(contactId) {
            if (this.activeChannel) {
                window.Echo.leave(this.activeChannel);
                this.activeChannel = null;
            }

            const participants = [String(this.currentUserId), String(contactId)].sort();
            const channelName = `conversation.${participants[0]}.${participants[1]}`;
            this.activeChannel = channelName;

            console.log("🔌 Conectando a chat:", channelName);

            window.Echo.private(channelName)
                .listen('.message.sent', (e) => {
                    // Ignorar mis propios mensajes (ya los tengo)
                    if (String(e.sender_id).toLowerCase() === String(this.currentUserId).toLowerCase()) return;

                    // Evitar duplicados por ID
                    if (this.messages.some(msg => String(msg.id) === String(e.id))) return;

                    console.log("✅ Mensaje recibido en chat abierto");
                    this.messages.push(e);
                    this.scrollToBottom();

                    // MARCAR LEÍDO (Porque estoy viendo el chat)
                    this.markAsRead(e.id);
                })
                .listen('.message.read', (e) => {
                    console.log("👀 Evento Leído:", e);

                    // Si el lector NO soy yo (es el otro usuario)
                    if (String(e.readerId).toLowerCase() !== String(this.currentUserId).toLowerCase()) {

                        // Recorremos los mensajes para poner el check azul
                        this.messages.forEach(msg => {
                            // Comparamos IDs como Strings para evitar errores de tipo
                            if (e.messageIds.map(String).includes(String(msg.id))) {
                                msg.read_at = new Date().toISOString();
                            }
                        });
                    }
                })
                .error((error) => {
                    console.error("❌ Error Reverb Chat:", error);
                });
        },

        async markAsRead(messageId) {
            if(!messageId) return;
            try {
                await fetch(`${this.apiUrl}/api/messages/read`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({
                        message_id: messageId,
                        reader_id: this.currentUserId
                    })
                });
            } catch (e) { console.error("Error API Read:", e); }
        },

        // --- 3. HELPER VITAL PARA EL BLADE ---
        getUnreadCount(chat) {
            if (!chat) return 0;

            // Intentamos obtener el ID y normalizarlo
            const id1 = chat.participant_id ? String(chat.participant_id).toLowerCase() : null;
            const id2 = chat.id ? String(chat.id).toLowerCase() : null;

            // Buscamos en el objeto de contadores
            if (id1 && this.unreadCounts[id1]) return this.unreadCounts[id1];
            if (id2 && this.unreadCounts[id2]) return this.unreadCounts[id2];

            return 0;
        },

        // --- 4. UTILIDADES (Sin cambios mayores) ---
        async loadConversations(userId) {
            try {
                // 1. Pedir IDs de conversaciones al Chat-Service
                const resChat = await fetch(`${this.apiUrl}/api/conversations/${userId}`, {
                    headers: {'Accept': 'application/json'}
                });
                const dataChat = await resChat.json();

                // chatIds será un array tipo: ["uuid-1", "uuid-2", "uuid-3"]
                const chatIds = dataChat.success ? dataChat.message : [];

                if (chatIds.length === 0) {
                    this.chats = [];
                    return;
                }

                // 2. Pedir los NOMBRES a My-Chat (Tu nueva ruta batch)
                // Convertimos el array en string separado por comas
                const idsParam = chatIds.join(',');

                const resUsers = await fetch(`/users/batch?ids=${idsParam}`);
                const usersData = await resUsers.json(); // Devuelve [{id: "...", name: "Pepe"}, ...]

                // 3. Cruzar los datos
                this.chats = chatIds.map(chatId => {
                    // Buscamos el usuario que corresponde a este ID
                    const user = usersData.find(u => String(u.id) === String(chatId));

                    return {
                        id: chatId,
                        participant_id: chatId,
                        name: user ? user.name : 'Usuario Desconocido', // Fallback por si acaso
                        created_at: new Date().toISOString() // O lo que quieras poner
                    };
                });

                console.log("✅ Chats cargados y fusionados:", this.chats);

            } catch (e) {
                console.error("Error cargando conversaciones:", e);
            }
        },

        async selectChat(chat) {
            let contactId = chat.participant_id || chat.id;
            await this.select({ id: contactId, name: chat.name });
        },

        scrollToBottom() {
            this.$nextTick(() => {
                const container = document.getElementById('messages-container');
                if (container) container.scrollTop = container.scrollHeight;
            });
        },

        async sendMessage() {
            if (!this.newMessage.trim()) return;

            const payload = {
                sender_id: this.currentUserId,
                receiver_id: this.selectedContact.id,
                content: this.newMessage
            };

            try {
                const res = await fetch(`${this.apiUrl}/api/messages`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();

                if (data.success) {
                    this.messages.push(data.message);
                    this.newMessage = '';
                    this.scrollToBottom();
                }
            } catch (e) { console.error("Error envío:", e); }
        },

        // Buscador
        fetchResults() {
            if (this.search.length < 1) { this.results = []; this.open = false; return; }
            fetch(`/contacts/search?q=${this.search}`)
                .then(res => res.json())
                .then(data => { this.results = data; this.open = data.length > 0; this.activeIndex = -1; })
                .catch(e => console.error(e));
        },
        nextResult() { if (this.activeIndex < this.results.length - 1) this.activeIndex++; },
        prevResult() { if (this.activeIndex > 0) this.activeIndex--; },
        chooseResult() { if (this.activeIndex >= 0) this.select(this.results[this.activeIndex]); },


        async deleteChat(chat) {
            if (!confirm('¿Seguro que quieres borrar este chat? Se perderá el historial.')) return;

            const contactId = chat.participant_id || chat.id;

            try {
                // Llamada DELETE al backend
                const res = await fetch(`${this.apiUrl}/api/conversations/${contactId}?user_id=${this.currentUserId}`, {
                    method: 'DELETE',
                    headers: {'Accept': 'application/json'}
                });

                if (res.ok) {
                    // 1. Eliminar visualmente del array
                    this.chats = this.chats.filter(c =>
                        String(c.participant_id || c.id) !== String(contactId)
                    );

                    // 2. Si tenía ese chat abierto, lo cierro
                    if (this.selectedContact.id && String(this.selectedContact.id) === String(contactId)) {
                        this.selectedContact = {};
                        this.messages = [];
                    }
                }
            } catch (e) {
                console.error("Error borrando chat:", e);
            }
        },
    }

}
