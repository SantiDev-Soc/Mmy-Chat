export const iniciadoresSockets = {

    iniciarEscuchaGlobal() {
        // si no hay id no hago nada
        if (!this.userId) {return;}

        const canal = `user.${this.userId}`;

        window.Echo.private(canal)
            .listen('.message.sent', (e) => this.procesarNotificacionGlobal(e))
            .listen('.chat.cleared', (e) => this.procesarLimpiezaRemota(e))
            .error(err => console.error("Error Auth Global:", err));
    },

    conectarChatEspecifico(idContacto) {
        // si no hay contacto salgo
        if (!idContacto) {return;}

        if (this.canalActivo) {
            window.Echo.leave(this.canalActivo);
        }

        // convierto los uuids a string y normalizo a lower
        const idMio = String(this.userId).toLowerCase();
        const idOtro = String(idContacto).toLowerCase();

        const partes = [idMio, idOtro].sort();

        const canal = `conversation.${partes[0]}.${partes[1]}`;
        this.canalActivo = canal;

        window.Echo.private(canal)
            .listen('.message.sent', (e) => {
                // compruebo si la función existe antes de llamarla
                if (this.procesarMensajeEnChat) {
                    this.procesarMensajeEnChat(e);
                } else {
                    console.error("error: this.procesarMensajeEnChat no está definido. Revisar websockets/index.js");
                }
            })
            .listen('.message.read', (e) => {
                if (this.procesarConfirmacionLectura) {
                    this.procesarConfirmacionLectura(e);
                }
            });
    }
};
