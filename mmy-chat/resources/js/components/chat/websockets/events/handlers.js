export const logicaGlobal = {

    procesarNotificacionGlobal(evento) {

        const emisor = String(evento.sender_id || evento.senderId || evento.payload?.sender_id).toLowerCase();
        const idMensaje = evento.id || evento.payload?.id;

        const chatAbiertoId = this.contactoSeleccionado.id
            ? String(this.contactoSeleccionado.id).toLowerCase() : null;

        if (chatAbiertoId === emisor) {
            this.notificarLectura(idMensaje);

        } else {
            this.incrementarContador(emisor);
            this.subirChatAlInicio(emisor);
        }
    },

    procesarLimpiezaRemota(evento) {

        const idObjetivo = String(evento.contactId).toLowerCase();
        this.conversaciones = this.conversaciones.filter(c =>
            String(c.id || c.participant_id).toLowerCase() !== idObjetivo
        );

        if (this.contadores[idObjetivo]) {
            const nuevos = {...this.contadores};
            delete nuevos[idObjetivo];
            this.contadores = nuevos;
        }

        const chatAbiertoId = this.contactoSeleccionado.id
            ? String(this.contactoSeleccionado.id).toLowerCase() : null;

        if (chatAbiertoId === idObjetivo) {
            this.mensajes = [];
            this.contactoSeleccionado = {};
        }
    },

    incrementarContador(emisor) {
        const actual = this.contadores[emisor] || 0;
        this.contadores = {...this.contadores, [emisor]: actual + 1};
    },

    subirChatAlInicio(idContacto) {
        const indx = this.conversaciones.findIndex(c =>
            String(c.participant_id || c.id).toLowerCase() === idContacto
        );

        if (indx > -1) {
            const chat = this.conversaciones.splice(indx, 1)[0];
            this.conversaciones.unshift(chat);

        } else {
            this.conversaciones.unshift({
                id: idContacto,
                participant_id: idContacto,
                name: "Nuevo Mensaje...",
                created_at: new Date().toISOString()
            });

            if (this.obtenerNombreContacto) {
                this.obtenerNombreContacto(idContacto);
            }
        }
    }
};
