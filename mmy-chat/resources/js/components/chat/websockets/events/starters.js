export const iniciadoresSockets = {

    iniciarEscuchaGlobal() {

        const canal = `user.${this.userId}`;
        window.Echo.private(canal)
            .listen('.message.sent', (e) => this.procesarNotificacionGlobal(e))
            .listen('.chat.cleared', (e) => this.procesarLimpiezaRemota(e))
            .error(err => console.error(
                    "Error Auth Global:", err
                )
            );
    },

    conectarChatEspecifico(idContacto) {

        if (this.canalActivo) {
            window.Echo.leave(this.canalActivo);
        }

        const partes = [
            String(this.userId),
            String(idContacto)
        ].sort();

        const canal = `conversation.${partes[0]}.${partes[1]}`;
        this.canalActivo = canal;

        window.Echo.private(canal)
            .listen('.message.sent', (e) => this.procesarMensajeEnChat(e))
            .listen('.message.read', (e) => this.procesarConfirmacionLectura(e));
    }
};
