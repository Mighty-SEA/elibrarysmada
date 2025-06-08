<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { BookOpen } from 'lucide-vue-next';

defineOptions({
    inheritAttrs: false,
});

interface Props {
    className?: HTMLAttributes['class'];
}

defineProps<Props>();
</script>

<template>
    <BookOpen :class="className" v-bind="$attrs" />
</template>
