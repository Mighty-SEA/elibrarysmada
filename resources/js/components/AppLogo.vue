<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
</script>

<template>
    <div class="flex aspect-square size-8 items-center justify-center text-blue-600">
        <AppLogoIcon class="size-6 fill-current" />
    </div>
    <div class="ml-1 grid flex-1 text-left">
        <span class="mb-0.5 truncate font-bold text-blue-700 text-sm leading-tight">E-LIBRARY</span>
        <span class="text-xxs font-semibold text-blue-500 tracking-wider uppercase border-t border-blue-200 pt-0.5 mt-0.5 truncate">SMA PEMUDA BANJARAN</span>
    </div>
</template>

<style scoped>
.text-xxs {
    font-size: 0.6rem;
    line-height: 0.75rem;
}
</style>
