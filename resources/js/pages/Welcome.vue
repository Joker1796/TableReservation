<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { dashboard, login, register } from '@/routes';
import { index as reservationsIndex } from '@/routes/reservations';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);
</script>

<template>
    <Head title="Кочующий стол — бронирование столов" />

    <div class="min-h-screen bg-white text-[#1b1b18] dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
        <!-- Header -->
        <header class="mx-auto flex max-w-5xl items-center justify-between px-6 py-5">
            <div class="flex items-center gap-2.5">
                <div class="flex size-9 items-center justify-center rounded-lg bg-[#1b1b18] dark:bg-[#EDEDEC]">
                    <AppLogoIcon class="size-5 text-white dark:text-[#1b1b18]" />
                </div>
                <span class="text-lg font-semibold tracking-tight">Кочующий стол</span>
            </div>
            <nav class="flex items-center gap-3">
                <a
                    href="#pricing"
                    class="text-sm text-[#706f6c] hover:text-[#1b1b18] dark:text-[#A1A09A] dark:hover:text-[#EDEDEC]"
                >
                    Цены
                </a>
                <Link
                    v-if="$page.props.auth.user"
                    :href="dashboard()"
                    class="rounded-md bg-[#1b1b18] px-4 py-1.5 text-sm text-white hover:bg-black dark:bg-[#EDEDEC] dark:text-[#1b1b18] dark:hover:bg-white"
                >
                    Личный кабинет
                </Link>
                <template v-else>
                    <Link
                        :href="login()"
                        class="text-sm text-[#706f6c] hover:text-[#1b1b18] dark:text-[#A1A09A] dark:hover:text-[#EDEDEC]"
                    >
                        Войти
                    </Link>
                    <Link
                        v-if="canRegister"
                        :href="register()"
                        class="rounded-md bg-[#1b1b18] px-4 py-1.5 text-sm text-white hover:bg-black dark:bg-[#EDEDEC] dark:text-[#1b1b18] dark:hover:bg-white"
                    >
                        Регистрация
                    </Link>
                </template>
            </nav>
        </header>

        <!-- Hero -->
        <section class="mx-auto max-w-5xl px-6 py-20 text-center">
            <h1 class="mb-5 text-5xl font-bold leading-tight tracking-tight lg:text-6xl">
                Забронируйте стол<br />в лучшем клубе города
            </h1>
            <p class="mx-auto mb-10 max-w-xl text-lg text-[#706f6c] dark:text-[#A1A09A]">
                Уютная атмосфера, живая музыка и первоклассный сервис каждый вечер. Выберите столик заранее и проведите
                время с комфортом в компании друзей.
            </p>
            <div class="flex flex-col items-center gap-3 sm:flex-row sm:justify-center">
                <Link
                    v-if="$page.props.auth.user"
                    :href="reservationsIndex()"
                    class="rounded-lg bg-[#1b1b18] px-7 py-3 text-base font-medium text-white hover:bg-black dark:bg-[#EDEDEC] dark:text-[#1b1b18] dark:hover:bg-white"
                >
                    Забронировать стол
                </Link>
                <Link
                    v-else
                    :href="login()"
                    class="rounded-lg bg-[#1b1b18] px-7 py-3 text-base font-medium text-white hover:bg-black dark:bg-[#EDEDEC] dark:text-[#1b1b18] dark:hover:bg-white"
                >
                    Забронировать стол
                </Link>
                <a
                    href="#pricing"
                    class="rounded-lg border border-[#e3e3e0] px-7 py-3 text-base font-medium hover:border-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#A1A09A]"
                >
                    Посмотреть цены
                </a>
            </div>
        </section>

        <!-- Services -->
        <section class="bg-[#fafaf9] px-6 py-20 dark:bg-[#111110]">
            <div class="mx-auto max-w-5xl">
                <h2 class="mb-12 text-center text-3xl font-bold">Наши услуги</h2>
                <div class="grid gap-8 sm:grid-cols-3">
                    <div class="rounded-xl border border-[#e3e3e0] bg-white p-6 dark:border-[#3E3E3A] dark:bg-[#161615]">
                        <div class="mb-4 flex size-10 items-center justify-center rounded-lg bg-[#f5f5f3] dark:bg-[#1f1f1e]">
                            <svg class="size-5 text-[#1b1b18] dark:text-[#EDEDEC]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z" />
                            </svg>
                        </div>
                        <h3 class="mb-2 font-semibold">Онлайн-бронирование</h3>
                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                            Выберите удобное время и стол за пару кликов. Подтверждение приходит мгновенно на почту.
                        </p>
                    </div>
                    <div class="rounded-xl border border-[#e3e3e0] bg-white p-6 dark:border-[#3E3E3A] dark:bg-[#161615]">
                        <div class="mb-4 flex size-10 items-center justify-center rounded-lg bg-[#f5f5f3] dark:bg-[#1f1f1e]">
                            <svg class="size-5 text-[#1b1b18] dark:text-[#EDEDEC]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2h5M12 12a4 4 0 100-8 4 4 0 000 8z" />
                            </svg>
                        </div>
                        <h3 class="mb-2 font-semibold">Приглашение гостей</h3>
                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                            Отправьте приглашения друзьям прямо из личного кабинета — они получат уведомление и смогут подтвердить участие.
                        </p>
                    </div>
                    <div class="rounded-xl border border-[#e3e3e0] bg-white p-6 dark:border-[#3E3E3A] dark:bg-[#161615]">
                        <div class="mb-4 flex size-10 items-center justify-center rounded-lg bg-[#f5f5f3] dark:bg-[#1f1f1e]">
                            <svg class="size-5 text-[#1b1b18] dark:text-[#EDEDEC]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2a4 4 0 014-4h0a4 4 0 014 4v2M3 21h18M5 21V7a2 2 0 012-2h10a2 2 0 012 2v14" />
                            </svg>
                        </div>
                        <h3 class="mb-2 font-semibold">VIP-зоны</h3>
                        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                            Отдельные зоны для приватных встреч и корпоративных мероприятий с персональным обслуживанием.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing -->
        <section id="pricing" class="px-6 py-20">
            <div class="mx-auto max-w-5xl">
                <h2 class="mb-3 text-center text-3xl font-bold">Цены</h2>
                <p class="mb-12 text-center text-[#706f6c] dark:text-[#A1A09A]">
                    Прозрачное ценообразование без скрытых платежей
                </p>
                <div class="grid gap-6 sm:grid-cols-3">
                    <div class="rounded-xl border border-[#e3e3e0] p-6 dark:border-[#3E3E3A]">
                        <p class="mb-1 text-sm font-medium text-[#706f6c] dark:text-[#A1A09A]">Стандарт</p>
                        <p class="mb-4 text-3xl font-bold">1 500 ₽</p>
                        <p class="mb-6 text-sm text-[#706f6c] dark:text-[#A1A09A]">за стол / вечер</p>
                        <ul class="space-y-2 text-sm">
                            <li class="flex items-center gap-2">
                                <span class="text-green-500">✓</span> Стол на 2–4 человека
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-green-500">✓</span> Доступ в зал с 20:00
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-green-500">✓</span> Живая музыка
                            </li>
                        </ul>
                    </div>
                    <div class="relative rounded-xl border-2 border-[#1b1b18] p-6 dark:border-[#EDEDEC]">
                        <span class="absolute -top-3 left-1/2 -translate-x-1/2 rounded-full bg-[#1b1b18] px-3 py-0.5 text-xs font-medium text-white dark:bg-[#EDEDEC] dark:text-[#1b1b18]">
                            Популярно
                        </span>
                        <p class="mb-1 text-sm font-medium text-[#706f6c] dark:text-[#A1A09A]">Премиум</p>
                        <p class="mb-4 text-3xl font-bold">3 000 ₽</p>
                        <p class="mb-6 text-sm text-[#706f6c] dark:text-[#A1A09A]">за стол / вечер</p>
                        <ul class="space-y-2 text-sm">
                            <li class="flex items-center gap-2">
                                <span class="text-green-500">✓</span> Стол на 4–8 человек
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-green-500">✓</span> Приоритетное место
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-green-500">✓</span> Приветственные напитки
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-green-500">✓</span> Персональный официант
                            </li>
                        </ul>
                    </div>
                    <div class="rounded-xl border border-[#e3e3e0] p-6 dark:border-[#3E3E3A]">
                        <p class="mb-1 text-sm font-medium text-[#706f6c] dark:text-[#A1A09A]">VIP</p>
                        <p class="mb-4 text-3xl font-bold">7 500 ₽</p>
                        <p class="mb-6 text-sm text-[#706f6c] dark:text-[#A1A09A]">за зону / вечер</p>
                        <ul class="space-y-2 text-sm">
                            <li class="flex items-center gap-2">
                                <span class="text-green-500">✓</span> Приватная зона до 15 чел.
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-green-500">✓</span> Всё включено из Премиум
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-green-500">✓</span> Декор по запросу
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-green-500">✓</span> Отдельный вход
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="bg-[#1b1b18] px-6 py-20 text-white dark:bg-[#EDEDEC] dark:text-[#1b1b18]">
            <div class="mx-auto max-w-xl text-center">
                <h2 class="mb-4 text-3xl font-bold">Готовы провести незабываемый вечер?</h2>
                <p class="mb-8 text-[#A1A09A] dark:text-[#706f6c]">
                    Столики расходятся быстро — особенно в выходные. Не откладывайте на потом.
                </p>
                <Link
                    v-if="$page.props.auth.user"
                    :href="reservationsIndex()"
                    class="inline-block rounded-lg bg-white px-8 py-3 text-base font-medium text-[#1b1b18] hover:bg-[#f5f5f3] dark:bg-[#1b1b18] dark:text-white dark:hover:bg-black"
                >
                    Забронировать стол
                </Link>
                <Link
                    v-else
                    :href="login()"
                    class="inline-block rounded-lg bg-white px-8 py-3 text-base font-medium text-[#1b1b18] hover:bg-[#f5f5f3] dark:bg-[#1b1b18] dark:text-white dark:hover:bg-black"
                >
                    Забронировать стол
                </Link>
            </div>
        </section>

        <!-- Footer -->
        <footer class="border-t border-[#e3e3e0] px-6 py-8 dark:border-[#3E3E3A]">
            <div class="mx-auto flex max-w-5xl items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="flex size-7 items-center justify-center rounded-md bg-[#1b1b18] dark:bg-[#EDEDEC]">
                        <AppLogoIcon class="size-4 text-white dark:text-[#1b1b18]" />
                    </div>
                    <span class="text-sm font-medium">Кочующий стол</span>
                </div>
                <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">© 2025 Все права защищены</p>
            </div>
        </footer>
    </div>
</template>
