
export const apiMensajes = {

    async obtenerMensajes(idContacto) {
        try {
            const url = `${this.api}/api/messages/${idContacto}?user_id=${this.userId}`;
            const resp = await fetch(url, { headers: {'Accept': 'application/json'} });
            const json = await resp.json();
            return json.success ? json.message : [];
        } catch (e) {
            console.error("Falló al traer mensajes:", e);
            return [];
        }
    },

    async enviarMensaje() {
        if (!this.nuevoMensaje.trim()) return;
        if (!this.userId || !this.contactoSeleccionado.id) return;

        const payload = {
            sender_id: this.userId,
            receiver_id: this.contactoSeleccionado.id,
            content: this.nuevoMensaje
        };

        try {
            const res = await fetch(`${this.api}/api/messages`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();

            if (data.success) {
                this.mensajes.push(data.message);
                this.nuevoMensaje = '';
                if (this.irAlUltimoMensaje) this.irAlUltimoMensaje();
            }
        } catch (e) {
            console.error("Error en el envío:", e);
        }
    },

    async notificarLectura(idMensaje) {
        if(!idMensaje) return;
        try {
            await fetch(`${this.api}/api/messages/read`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ message_id: idMensaje, reader_id: this.userId })
            });
        } catch (e) { console.warn("Error de lectura:", e); }
    },

    async limpiarChat(chat) {
        if (!confirm('Limpiar este chat? Desaparecerá de tu lista.')) return;
        const idContacto = String(chat.participant_id || chat.id);

        try {
            const res = await fetch(`${this.api}/api/conversations/${idContacto}?user_id=${this.userId}`, {
                method: 'DELETE',
                headers: {'Accept': 'application/json'}
            });

            if (res.ok) {
                this.conversaciones = this.conversaciones.filter(c =>
                    String(c.participant_id || c.id) !== idContacto
                );
                if (this.contactoSeleccionado.id && String(this.contactoSeleccionado.id) === idContacto) {
                    this.contactoSeleccionado = {};
                    this.mensajes = [];
                }
            }
        } catch (e) { console.error("Error limpiando chat:", e); }
    }
};
