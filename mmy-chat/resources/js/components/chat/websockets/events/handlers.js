
export const logicaGlobal = {

    procesarNotificacionGlobal(evento) {

        // datos crudos del mensaje/payload
        const dataMensaje = evento.message || evento.payload || evento;

        const emisor = String(dataMensaje.sender_id || dataMensaje.senderId).toLowerCase();
        const idMensaje = dataMensaje.id;

        const chatAbiertoId = this.contactoSeleccionado.id
            ? String(this.contactoSeleccionado.id).toLowerCase() : null;

        // si tengo el chat abierto
        if (chatAbiertoId === emisor) {

            // lo marco como leído en el backend
            if (this.notificarLectura) {
                this.notificarLectura(idMensaje);
            } else {
                console.warn("notificarLectura no está disponible");
            }

            // si el canal de conversación aún no ha traído el mensaje, lo pongo yo
            const ifExiste = this.mensajes.some(m => String(m.id) === String(idMensaje));

            if (!ifExiste) {
                console.log("Inyectando el mensaje desde canal global" + ifExiste);
                this.mensajes.push(dataMensaje);

                if (this.scrollAlFondo) this.scrollAlFondo();
                else if (this.irAlUltimoMensaje) this.irAlUltimoMensaje();
            }

        }
        // si está cerrado o estoy en otro
        else {
            this.incrementarContador(emisor);
            this.subirChatAlInicio(emisor);
        }
    },

    // a lowerCase para evitar conflictos de tipos
    procesarLimpiezaRemota(evento) {
        const idObjetivo = String(evento.contactId).toLowerCase();

        // Filtrar visualmente
        this.conversaciones = this.conversaciones.filter(c =>
            String(c.id || c.participant_id).toLowerCase() !== idObjetivo
        );

        // Limpiar contador
        if (this.contadores[idObjetivo]) {
            const nuevos = {...this.contadores};
            delete nuevos[idObjetivo];
            this.contadores = nuevos;
        }

        // Si lo tenía abierto, lo cierro
        const chatAbiertoId = this.contactoSeleccionado.id
            ? String(this.contactoSeleccionado.id).toLowerCase() : null;

        if (chatAbiertoId === idObjetivo) {
            this.mensajes = [];
            this.contactoSeleccionado = {};
        }
    },

    incrementarContador(emisor) {
        const actual = this.contadores[emisor] || 0;
        // spread operator para la reactividad en Alpine
        this.contadores = {...this.contadores, [emisor]: actual + 1};
    },

    subirChatAlInicio(idContacto) {
        const indx = this.conversaciones.findIndex(c =>
            String(c.participant_id || c.id).toLowerCase() === idContacto
        );

        if (indx > -1) {
            // si existe, lo muevo al principio
            const chat = this.conversaciones.splice(indx, 1)[0];
            this.conversaciones.unshift(chat);

        } else {
            // si es nuevo, crearlo temporalmente
            this.conversaciones.unshift({
                id: idContacto,
                participant_id: idContacto,
                name: "Nuevo Mensaje...",
                created_at: new Date().toISOString()
            });

            // pedir el nombre real
            if (this.obtenerNombreContacto) {
                this.obtenerNombreContacto(idContacto);
            }
        }
    }
};
