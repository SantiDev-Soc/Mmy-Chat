export const estadoInicial = (idUsuario) => ({
    busqueda: '',
    resultadosBusqueda: [],
    chatAbierto: false,
    contactoSeleccionado: {},
    conversaciones: [],
    mensajes: [],
    nuevoMensaje: '',
    userId: idUsuario,
    canalActivo: null,
    contadores: {},

    api: (import.meta.env.VITE_CHAT_API_URL || 'https://localhost').replace(/\/$/, ''),
});
