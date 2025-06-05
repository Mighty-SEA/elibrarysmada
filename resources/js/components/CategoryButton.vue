<script setup lang="ts">
defineProps<{
  name: string;
  active: boolean;
}>();

defineEmits<{
  (e: 'select'): void;
}>();
</script>

<template>
  <button 
    @click="$emit('select')"
    class="px-4 py-2 rounded-full text-sm font-medium transition-colors"
    :class="active 
      ? 'bg-blue-600 text-white' 
      : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
  >
    {{ name }}
  </button>
</template> 