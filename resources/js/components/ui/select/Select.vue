<script setup lang="ts">
defineProps<{
  modelValue: string;
  required?: boolean;
}>();

defineEmits(['update:modelValue']);
</script>

<template>
  <div class="relative">
    <slot />
  </div>
</template> 