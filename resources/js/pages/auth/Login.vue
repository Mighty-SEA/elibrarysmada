<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <AuthBase>
        <Head title="Log in" />

        <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="flex flex-col gap-6 bg-white/90 rounded-2xl shadow-xl px-8 py-10 border border-blue-100">
            <div class="flex flex-col items-center mb-6">
                <img src="/android-chrome-512x512.png" alt="Logo" class="h-16 w-16 mb-2" />
                <div class="text-2xl font-bold text-blue-700 tracking-wide mb-1">E - LIBRARY</div>
                <div class="text-xs font-semibold text-blue-500 tracking-wider uppercase border-t border-blue-200 pt-0.5 mt-0.5">SMA PEMUDA BANJARAN</div>
            </div>
            <div class="grid gap-4">
                <div class="grid gap-2">
                    <Label for="email">Email address</Label>
                    <Input
                        id="email"
                        type="email"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="email"
                        v-model="form.email"
                        placeholder="email@example.com"
                        class="rounded-lg border-blue-200 focus:border-blue-500 focus:ring-blue-500 bg-blue-50/40"
                    />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Password</Label>
                    <Input
                        id="password"
                        type="password"
                        required
                        :tabindex="2"
                        autocomplete="current-password"
                        v-model="form.password"
                        placeholder="Password"
                        class="rounded-lg border-blue-200 focus:border-blue-500 focus:ring-blue-500 bg-blue-50/40"
                    />
                    <InputError :message="form.errors.password" />
                </div>

                <div class="flex items-center justify-between">
                    <Label for="remember" class="flex items-center space-x-3">
                        <Checkbox id="remember" v-model="form.remember" :tabindex="3" />
                        <span>Remember me</span>
                    </Label>
                </div>

                <Button type="submit" class="mt-4 w-full bg-gradient-to-r from-blue-500 via-blue-600 to-indigo-600 text-white font-semibold rounded-lg shadow-lg hover:from-blue-600 hover:to-indigo-700 transition-all duration-200 py-2.5 text-base flex items-center justify-center gap-2" :tabindex="4" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Log in
                </Button>
            </div>
        </form>
    </AuthBase>
</template>
