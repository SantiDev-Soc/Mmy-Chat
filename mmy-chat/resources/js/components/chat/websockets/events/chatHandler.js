
export const logicaDeChat = {

    procesarMensajeEnChat(mensaje) {
        // si lo envié yo lo ignoro
        if (String(mensaje.sender_id).toLowerCase() === String(this.userId).toLowerCase()) {return;}

        // evita los messajes duplicados
        if (this.mensajes.some(m => String(m.id) === String(mensaje.id))) {return;}

        // añadir y scroll
        this.mensajes.push(mensaje);

        if (this.irAlUltimoMensaje) {this.irAlUltimoMensaje();}

        // marcar como leído
        if (this.notificarLectura) {this.notificarLectura(mensaje.id);}
    },

    procesarConfirmacionLectura(evento) {
        // confirmar si el que leyó no fui yo
        if (String(evento.readerId).toLowerCase() !== String(this.userId).toLowerCase()) {

            this.mensajes.forEach(msg => {
                if (evento.messageIds.map(String).includes(String(msg.id))) {
                    msg.read_at = new Date().toISOString();
                }
            });
        }
    }
};
