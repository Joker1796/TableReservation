<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { Menu } from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { login } from '@/routes';
import { edit as profileEdit } from '@/routes/profile';
import { index as reservationsIndex } from '@/routes/reservations';

defineProps<{ canRegister: boolean }>();

const page = usePage();
const user = computed(() => page.props.auth?.user);
</script>

<template>
    <header class="mx-auto flex max-w-5xl items-center justify-between px-6 py-5">
        <div class="flex flex-1 items-center gap-2.5">
            <div class="flex size-9 items-center justify-center rounded-lg bg-[#453c77] dark:bg-[#e8e6f0]">
                <AppLogoIcon class="size-5 text-white dark:text-[#453c77]" />
            </div>
            <span class="text-lg font-semibold tracking-tight">Кочующий стол</span>
        </div>

        <nav class="flex items-center gap-3">
            <Link href="/workshop" class="link-nav hidden sm:inline">Мастерская</Link>
            <a href="#pricing" class="link-nav hidden sm:inline">Цены</a>

            <template v-if="user">
                <div class="sm:hidden">
                    <DropdownMenu>
                        <DropdownMenuTrigger as-child>
                            <button class="flex items-center rounded-lg p-2 hover:bg-[#f0eef9] dark:hover:bg-[#1e1a35]">
                                <Menu class="size-5" />
                            </button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end" class="w-48">
                            <DropdownMenuItem as-child>
                                <Link :href="profileEdit().url">Профиль</Link>
                            </DropdownMenuItem>
                            <DropdownMenuItem as-child>
                                <Link href="/workshop">Мастерская</Link>
                            </DropdownMenuItem>
                            <DropdownMenuItem as-child>
                                <Link :href="reservationsIndex().url">Личный кабинет</Link>
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                </div>
                <Link :href="reservationsIndex().url" class="btn-nav hidden sm:inline">Личный кабинет</Link>
            </template>

            <template v-else>
                <Link :href="login()" class="btn-nav">Войти</Link>
            </template>
        </nav>
    </header>
</template>
