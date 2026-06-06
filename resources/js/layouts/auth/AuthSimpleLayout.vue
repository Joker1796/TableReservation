<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="min-h-screen bg-white text-[#1b1b18] dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
        <header class="mx-auto flex max-w-5xl items-center justify-between px-6 py-5">
            <Link :href="home()" class="flex items-center gap-2.5">
                <div class="flex size-9 items-center justify-center rounded-lg bg-[#1b1b18] dark:bg-[#EDEDEC]">
                    <AppLogoIcon class="size-5 text-white dark:text-[#1b1b18]" />
                </div>
                <span class="text-lg font-semibold tracking-tight">Кочующий стол</span>
            </Link>
        </header>

        <div class="flex items-center justify-center px-6 py-12">
            <div class="w-full max-w-sm">
                <div class="mb-8 text-center">
                    <h1 class="text-2xl font-bold tracking-tight">
                        {{ title }}
                    </h1>
                    <p class="mt-2 text-sm text-[#706f6c] dark:text-[#A1A09A]">
                        {{ description }}
                    </p>
                </div>
                <div
                    class="rounded-xl border border-[#e3e3e0] bg-white p-8 shadow-sm dark:border-[#3E3E3A] dark:bg-[#161615]"
                >
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
