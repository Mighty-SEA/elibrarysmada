import mitt from 'mitt';

export const emitter = mitt();

export function useEventBus() {
  return {
    emit: emitter.emit,
    on: emitter.on,
    off: emitter.off,
  };
} 