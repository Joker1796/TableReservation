<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import BookingRequestModal from '@/components/BookingRequestModal.vue';
import { login, register } from '@/routes';
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

    <div
        class="min-h-screen bg-white text-[#2a2448] dark:bg-[#0d0b17] dark:text-[#e8e6f0]"
    >
        <!-- Header -->
        <header
            class="mx-auto flex max-w-5xl items-center justify-between px-6 py-5"
        >
            <div class="flex items-center gap-2.5">
                <div
                    class="flex size-9 items-center justify-center rounded-lg bg-[#453c77] dark:bg-[#e8e6f0]"
                >
                    <AppLogoIcon
                        class="size-5 text-white dark:text-[#453c77]"
                    />
                </div>
                <span class="text-lg font-semibold tracking-tight"
                    >Кочующий стол</span
                >
            </div>
            <nav class="flex items-center gap-3">
                <Link
                    href="/workshop"
                    class="text-sm text-[#6b6585] hover:text-[#453c77] dark:text-[#9b98af] dark:hover:text-[#e8e6f0]"
                >
                    Мастерская
                </Link>
                <a
                    href="#pricing"
                    class="text-sm text-[#6b6585] hover:text-[#453c77] dark:text-[#9b98af] dark:hover:text-[#e8e6f0]"
                >
                    Цены
                </a>
                <Link
                    v-if="$page.props.auth.user"
                    :href="reservationsIndex().url"
                    class="rounded-md bg-[#453c77] px-4 py-1.5 text-sm text-white hover:bg-[#362f5f] dark:bg-[#e8e6f0] dark:text-[#453c77] dark:hover:bg-white"
                >
                    Личный кабинет
                </Link>
                <template v-else>
                    <Link
                        :href="login()"
                        class="text-sm text-[#6b6585] hover:text-[#453c77] dark:text-[#9b98af] dark:hover:text-[#e8e6f0]"
                    >
                        Войти
                    </Link>
                    <Link
                        v-if="canRegister"
                        :href="register()"
                        class="rounded-md bg-[#453c77] px-4 py-1.5 text-sm text-white hover:bg-[#362f5f] dark:bg-[#e8e6f0] dark:text-[#453c77] dark:hover:bg-white"
                    >
                        Регистрация
                    </Link>
                </template>
            </nav>
        </header>

        <!-- Hero -->
        <section class="mx-auto max-w-5xl px-6 py-20 text-center">
            <h1
                class="mb-5 text-5xl leading-tight font-bold tracking-tight lg:text-6xl"
            >
                Забронируйте стол<br />в лучшем клубе города
            </h1>
            <p
                class="mx-auto mb-10 max-w-xl text-lg text-[#6b6585] dark:text-[#9b98af]"
            >
                Уютная атмосфера, живая музыка и первоклассный сервис каждый
                вечер. Выберите столик заранее и проведите время с комфортом в
                компании друзей.
            </p>
            <div
                class="flex flex-col items-center gap-3 sm:flex-row sm:justify-center"
            >
                <BookingRequestModal v-if="$page.props.auth.user">
                    <button
                        class="rounded-lg bg-[#453c77] px-7 py-3 text-base font-medium text-white hover:bg-[#362f5f] dark:bg-[#e8e6f0] dark:text-[#453c77] dark:hover:bg-white"
                    >
                        Забронировать стол
                    </button>
                </BookingRequestModal>
                <Link
                    v-else
                    :href="login()"
                    class="rounded-lg bg-[#453c77] px-7 py-3 text-base font-medium text-white hover:bg-[#362f5f] dark:bg-[#e8e6f0] dark:text-[#453c77] dark:hover:bg-white"
                >
                    Забронировать стол
                </Link>
                <a
                    href="#pricing"
                    class="rounded-lg border border-[#dddaf0] px-7 py-3 text-base font-medium hover:border-[#453c77] dark:border-[#352f5a] dark:hover:border-[#9b98af]"
                >
                    Посмотреть цены
                </a>
            </div>
        </section>

        <!-- Services -->
        <section class="bg-[#f7f6fc] px-6 py-20 dark:bg-[#120f20]">
            <div class="mx-auto max-w-5xl">
                <h2 class="mb-12 text-center text-3xl font-bold">
                    Наши услуги
                </h2>
                <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
                    <div
                        class="rounded-xl border border-[#dddaf0] bg-white p-6 dark:border-[#352f5a] dark:bg-[#171428]"
                    >
                        <div
                            class="mb-4 flex size-10 items-center justify-center rounded-lg bg-[#f0eef9] dark:bg-[#1e1a35]"
                        >
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
                                    d="M12 6v6l4 2m6-2a10 10 0 11-20 0 10 10 0 0120 0z"
                                />
                            </svg>
                        </div>
                        <h3 class="mb-2 font-semibold">Онлайн-бронирование</h3>
                        <p class="text-sm text-[#6b6585] dark:text-[#9b98af]">
                            Выберите удобное время и стол за пару кликов.
                            Подтверждение приходит мгновенно на почту.
                        </p>
                    </div>
                    <div
                        class="rounded-xl border border-[#dddaf0] bg-white p-6 dark:border-[#352f5a] dark:bg-[#171428]"
                    >
                        <div
                            class="mb-4 flex size-10 items-center justify-center rounded-lg bg-[#f0eef9] dark:bg-[#1e1a35]"
                        >
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
                                    d="M17 20h5v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2h5M12 12a4 4 0 100-8 4 4 0 000 8z"
                                />
                            </svg>
                        </div>
                        <h3 class="mb-2 font-semibold">Приглашение гостей</h3>
                        <p class="text-sm text-[#6b6585] dark:text-[#9b98af]">
                            Отправьте приглашения друзьям прямо из личного
                            кабинета — они получат уведомление и смогут
                            подтвердить участие.
                        </p>
                    </div>
                    <div
                        class="rounded-xl border border-[#dddaf0] bg-white p-6 dark:border-[#352f5a] dark:bg-[#171428]"
                    >
                        <div
                            class="mb-4 flex size-10 items-center justify-center rounded-lg bg-[#f0eef9] dark:bg-[#1e1a35]"
                        >
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
                                    d="M9 17v-2a4 4 0 014-4h0a4 4 0 014 4v2M3 21h18M5 21V7a2 2 0 012-2h10a2 2 0 012 2v14"
                                />
                            </svg>
                        </div>
                        <h3 class="mb-2 font-semibold">VIP-зоны</h3>
                        <p class="text-sm text-[#6b6585] dark:text-[#9b98af]">
                            Отдельные зоны для приватных встреч и корпоративных
                            мероприятий с персональным обслуживанием.
                        </p>
                    </div>
                    <Link
                        href="/workshop"
                        class="group flex flex-col rounded-xl border border-[#dddaf0] bg-white p-6 transition-colors hover:border-[#453c77] dark:border-[#352f5a] dark:bg-[#171428] dark:hover:border-[#a096d0]"
                    >
                        <div
                            class="mb-4 flex size-10 items-center justify-center rounded-lg bg-[#f0eef9] dark:bg-[#1e1a35]"
                        >
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
                        <h3 class="mb-2 font-semibold">Мастерская</h3>
                        <p
                            class="mb-4 flex-1 text-sm text-[#6b6585] dark:text-[#9b98af]"
                        >
                            3D-печать и профессиональный покрас миниатюр.
                            Коллекционные персонажи и игровые фигурки на заказ.
                        </p>
                        <span
                            class="text-sm font-medium text-[#453c77] group-hover:underline dark:text-[#a096d0]"
                        >
                            Подробнее →
                        </span>
                    </Link>
                </div>
            </div>
        </section>

        <!-- Pricing -->
        <section id="pricing" class="px-6 py-20">
            <div class="mx-auto max-w-5xl">
                <h2 class="mb-3 text-center text-3xl font-bold">Цены</h2>
                <p class="mb-12 text-center text-[#6b6585] dark:text-[#9b98af]">
                    Прозрачное ценообразование без скрытых платежей
                </p>
                <div class="grid gap-6 sm:grid-cols-3">
                    <div
                        class="rounded-xl border border-[#dddaf0] p-6 dark:border-[#352f5a]"
                    >
                        <p
                            class="mb-1 text-sm font-medium text-[#6b6585] dark:text-[#9b98af]"
                        >
                            Стандарт
                        </p>
                        <p class="mb-4 text-3xl font-bold">1 500 ₽</p>
                        <p
                            class="mb-6 text-sm text-[#6b6585] dark:text-[#9b98af]"
                        >
                            за стол / вечер
                        </p>
                        <ul class="space-y-2 text-sm">
                            <li class="flex items-center gap-2">
                                <span class="text-green-500">✓</span> Стол на
                                2–4 человека
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-green-500">✓</span> Доступ в
                                зал с 20:00
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-green-500">✓</span> Живая
                                музыка
                            </li>
                        </ul>
                    </div>
                    <div
                        class="relative rounded-xl border-2 border-[#453c77] p-6 dark:border-[#a096d0]"
                    >
                        <span
                            class="absolute -top-3 left-1/2 -translate-x-1/2 rounded-full bg-[#453c77] px-3 py-0.5 text-xs font-medium text-white dark:bg-[#a096d0] dark:text-[#1e1a35]"
                        >
                            Популярно
                        </span>
                        <p
                            class="mb-1 text-sm font-medium text-[#6b6585] dark:text-[#9b98af]"
                        >
                            Премиум
                        </p>
                        <p class="mb-4 text-3xl font-bold">3 000 ₽</p>
                        <p
                            class="mb-6 text-sm text-[#6b6585] dark:text-[#9b98af]"
                        >
                            за стол / вечер
                        </p>
                        <ul class="space-y-2 text-sm">
                            <li class="flex items-center gap-2">
                                <span class="text-green-500">✓</span> Стол на
                                4–8 человек
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-green-500">✓</span>
                                Приоритетное место
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-green-500">✓</span>
                                Приветственные напитки
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-green-500">✓</span>
                                Персональный официант
                            </li>
                        </ul>
                    </div>
                    <div
                        class="rounded-xl border border-[#dddaf0] p-6 dark:border-[#352f5a]"
                    >
                        <p
                            class="mb-1 text-sm font-medium text-[#6b6585] dark:text-[#9b98af]"
                        >
                            VIP
                        </p>
                        <p class="mb-4 text-3xl font-bold">7 500 ₽</p>
                        <p
                            class="mb-6 text-sm text-[#6b6585] dark:text-[#9b98af]"
                        >
                            за зону / вечер
                        </p>
                        <ul class="space-y-2 text-sm">
                            <li class="flex items-center gap-2">
                                <span class="text-green-500">✓</span> Приватная
                                зона до 15 чел.
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-green-500">✓</span> Всё
                                включено из Премиум
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-green-500">✓</span> Декор по
                                запросу
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-green-500">✓</span> Отдельный
                                вход
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section
            class="bg-[#453c77] px-6 py-20 text-white dark:bg-[#2a2250] dark:text-[#e8e6f0]"
        >
            <div class="mx-auto max-w-xl text-center">
                <h2 class="mb-4 text-3xl font-bold">
                    Готовы провести незабываемый вечер?
                </h2>
                <p class="mb-8 text-[#c4bfdf] dark:text-[#9b98af]">
                    Столики расходятся быстро — особенно в выходные. Не
                    откладывайте на потом.
                </p>
                <BookingRequestModal v-if="$page.props.auth.user">
                    <button
                        class="inline-block rounded-lg bg-white px-8 py-3 text-base font-medium text-[#453c77] hover:bg-[#f0eef9] dark:bg-[#453c77] dark:text-white dark:hover:bg-[#362f5f]"
                    >
                        Забронировать стол
                    </button>
                </BookingRequestModal>
                <Link
                    v-else
                    :href="login()"
                    class="inline-block rounded-lg bg-white px-8 py-3 text-base font-medium text-[#453c77] hover:bg-[#f0eef9] dark:bg-[#453c77] dark:text-white dark:hover:bg-[#362f5f]"
                >
                    Забронировать стол
                </Link>
            </div>
        </section>

        <!-- Footer -->
        <footer
            class="border-t border-[#dddaf0] px-6 py-8 dark:border-[#352f5a]"
        >
            <div class="mx-auto flex max-w-5xl items-center justify-between">
                <div class="flex items-center gap-2">
                    <div
                        class="flex size-7 items-center justify-center rounded-md bg-[#453c77] dark:bg-[#e8e6f0]"
                    >
                        <AppLogoIcon
                            class="size-4 text-white dark:text-[#453c77]"
                        />
                    </div>
                    <span class="text-sm font-medium">Кочующий стол</span>
                </div>
                <p class="text-sm text-[#6b6585] dark:text-[#9b98af]">
                    © 2025 Все права защищены
                </p>
            </div>
        </footer>
    </div>
</template>
