<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import type { WorkshopPhoto } from '@/types/workshop';

defineProps<{
    photos: WorkshopPhoto[];
}>();
</script>

<template>
    <Head title="Мастерская — 3D-печать и покрас миниатюр" />

    <div class="min-h-screen bg-white text-[#2a2448] dark:bg-[#0d0b17] dark:text-[#e8e6f0]">
        <!-- Header -->
        <header class="mx-auto flex max-w-5xl items-center justify-between px-6 py-5">
            <Link href="/" class="flex items-center gap-2.5">
                <div class="flex size-9 items-center justify-center rounded-lg bg-[#453c77] dark:bg-[#e8e6f0]">
                    <AppLogoIcon class="size-5 text-white dark:text-[#453c77]" />
                </div>
                <span class="text-lg font-semibold tracking-tight">Кочующий стол</span>
            </Link>
            <Link href="/" class="link-nav">← На главную</Link>
        </header>

        <!-- Hero -->
        <section class="mx-auto max-w-5xl px-6 py-16">
            <h1 class="mb-4 text-4xl font-bold lg:text-5xl">Мастерская</h1>
            <p class="max-w-2xl text-lg text-[#6b6585] dark:text-[#9b98af]">
                3D-печать и профессиональный покрас миниатюр. Создаём коллекционных персонажей, игровые фигурки и
                декоративные элементы — от единичных работ до тиражей.
            </p>
        </section>

        <!-- Services & Pricing -->
        <section class="bg-[#f7f6fc] px-6 py-16 dark:bg-[#120f20]">
            <div class="mx-auto max-w-5xl">
                <h2 class="mb-10 text-2xl font-bold">Услуги и цены</h2>
                <div class="grid gap-6 sm:grid-cols-3">
                    <div class="card-service">
                        <div class="icon-service">
                            <svg
                                class="size-5 text-[#453c77] dark:text-[#c4bfdf]"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z"
                                />
                            </svg>
                        </div>
                        <h3 class="mb-2 font-semibold">3D-печать</h3>
                        <p class="mb-4 text-sm text-[#6b6585] dark:text-[#9b98af]">
                            Печать миниатюр по вашим файлам или нашим моделям. Высокая детализация, смола или FDM.
                        </p>
                        <p class="text-xl font-bold">от 300 ₽</p>
                        <p class="text-xs text-[#6b6585] dark:text-[#9b98af]">за фигурку</p>
                    </div>
                    <div class="card-service">
                        <div class="icon-service">
                            <svg
                                class="size-5 text-[#453c77] dark:text-[#c4bfdf]"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.078-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.42 3.42a15.995 15.995 0 004.764-4.648l3.876-5.814a1.151 1.151 0 00-1.597-1.597L14.146 6.32a15.996 15.996 0 00-4.649 4.763m3.42 3.42a6.776 6.776 0 00-3.42-3.42"
                                />
                            </svg>
                        </div>
                        <h3 class="mb-2 font-semibold">Базовый покрас</h3>
                        <p class="mb-4 text-sm text-[#6b6585] dark:text-[#9b98af]">
                            Качественная покраска для игровых столов — быстро и аккуратно. Подходит для больших отрядов.
                        </p>
                        <p class="text-xl font-bold">от 500 ₽</p>
                        <p class="text-xs text-[#6b6585] dark:text-[#9b98af]">за фигурку</p>
                    </div>
                    <div class="relative rounded-xl border-2 border-[#453c77] p-6 dark:border-[#a096d0]">
                        <span class="badge-popular">Популярно</span>
                        <div class="icon-service">
                            <svg
                                class="size-5 text-[#453c77] dark:text-[#c4bfdf]"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"
                                />
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z"
                                />
                            </svg>
                        </div>
                        <h3 class="mb-2 font-semibold">Коллекционный покрас</h3>
                        <p class="mb-4 text-sm text-[#6b6585] dark:text-[#9b98af]">
                            Детализированная ручная роспись с эффектами NMM, OSL, тонирование и постобработка.
                        </p>
                        <p class="text-xl font-bold">от 1 500 ₽</p>
                        <p class="text-xs text-[#6b6585] dark:text-[#9b98af]">за фигурку</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Gallery -->
        <section class="px-6 py-16">
            <div class="mx-auto max-w-5xl">
                <h2 class="mb-10 text-2xl font-bold">Работы</h2>

                <div
                    v-if="photos.length === 0"
                    class="flex flex-col items-center justify-center rounded-xl border border-dashed border-[#dddaf0] py-16 dark:border-[#352f5a]"
                >
                    <p class="text-[#6b6585] dark:text-[#9b98af]">Скоро здесь появятся работы</p>
                </div>

                <div
                    v-else
                    class="flex [scroll-snap-type:x_mandatory] [scrollbar-width:thin] gap-4 overflow-x-auto pb-4"
                >
                    <div v-for="photo in photos" :key="photo.id" class="w-64 shrink-0 [scroll-snap-align:start]">
                        <img :src="photo.url" :alt="photo.original_name" class="h-64 w-full rounded-xl object-cover" />
                    </div>
                </div>
            </div>
        </section>

        <!-- Contacts -->
        <section class="bg-[#f7f6fc] px-6 py-16 dark:bg-[#120f20]">
            <div class="mx-auto max-w-5xl">
                <h2 class="mb-3 text-2xl font-bold">Связаться с нами</h2>
                <p class="mb-10 text-[#6b6585] dark:text-[#9b98af]">Обсудим детали заказа — напишите или позвоните</p>
                <div class="grid gap-4 sm:grid-cols-3">
                    <a href="https://t.me/example" target="_blank" rel="noopener noreferrer" class="card-contact">
                        <div class="icon-contact">
                            <svg
                                class="size-5 text-[#453c77] dark:text-[#c4bfdf]"
                                fill="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.96 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"
                                />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-[#6b6585] dark:text-[#9b98af]">Telegram</p>
                            <p class="font-medium">@example</p>
                        </div>
                    </a>

                    <a href="tel:+70000000000" class="card-contact">
                        <div class="icon-contact">
                            <svg
                                class="size-5 text-[#453c77] dark:text-[#c4bfdf]"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"
                                />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-[#6b6585] dark:text-[#9b98af]">Телефон</p>
                            <p class="font-medium">+7 (000) 000-00-00</p>
                        </div>
                    </a>

                    <a href="mailto:workshop@example.com" class="card-contact">
                        <div class="icon-contact">
                            <svg
                                class="size-5 text-[#453c77] dark:text-[#c4bfdf]"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.8"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"
                                />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-[#6b6585] dark:text-[#9b98af]">Email</p>
                            <p class="font-medium">workshop@example.com</p>
                        </div>
                    </a>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="border-t border-[#dddaf0] px-6 py-8 dark:border-[#352f5a]">
            <div class="mx-auto flex max-w-5xl items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="flex size-7 items-center justify-center rounded-md bg-[#453c77] dark:bg-[#e8e6f0]">
                        <AppLogoIcon class="size-4 text-white dark:text-[#453c77]" />
                    </div>
                    <span class="text-sm font-medium">Кочующий стол</span>
                </div>
                <p class="text-sm text-[#6b6585] dark:text-[#9b98af]">© 2025 Все права защищены</p>
            </div>
        </footer>
    </div>
</template>
