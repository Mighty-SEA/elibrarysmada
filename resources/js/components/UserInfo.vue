<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/composables/useInitials';
import type { User } from '@/types';
import { computed } from 'vue';

interface Props {
    user: User;
    showEmail?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    showEmail: false,
});

const { getInitials } = useInitials();

// Compute whether we should show the avatar image
const showAvatar = computed(() => {
    // Periksa foto_profil terlebih dahulu, lalu fallback ke avatar jika ada
    return (props.user.foto_profil && props.user.foto_profil !== '') || 
           (props.user.avatar && props.user.avatar !== '');
});

// Mendapatkan URL avatar yang sesuai
const avatarUrl = computed(() => {
    if (props.user.foto_profil && props.user.foto_profil !== '') {
        return '/storage/' + props.user.foto_profil;
    }
    return props.user.avatar;
});
</script>

<template>
    <Avatar class="h-8 w-8 overflow-hidden rounded-lg">
        <AvatarImage v-if="showAvatar" :src="avatarUrl" :alt="user.name" />
        <AvatarFallback class="rounded-lg text-black dark:text-white">
            {{ getInitials(user.name) }}
        </AvatarFallback>
    </Avatar>

    <div class="grid flex-1 text-left text-sm leading-tight">
        <span class="truncate font-medium">{{ user.name }}</span>
        <span v-if="showEmail" class="truncate text-xs text-muted-foreground">{{ user.email }}</span>
    </div>
</template>
