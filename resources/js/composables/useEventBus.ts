import mitt from 'mitt';

const emitter = mitt();

export function useEventBus() {
  return {
    emit: emitter.emit,
    on: emitter.on,
    off: emitter.off,
  };
} 