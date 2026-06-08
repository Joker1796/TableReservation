<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import { Bell, CalendarCheck, CalendarDays, CalendarPlus, Check, Newspaper, Table2, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import type { EventRegistration, EventSuggestion } from '@/types/event';
import type { PostSuggestion } from '@/types/feed';
import type { BookingRequest, Invite } from '@/types/reservation';

const open = ref(false);

const page = usePage<{
    pendingInvites: Invite[];
    pendingBookingRequests: BookingRequest[];
    pendingEventRegistrations: EventRegistration[];
    pendingPostSuggestions: PostSuggestion[];
    pendingEventSuggestions: EventSuggestion[];
}>();
const invites = computed<Invite[]>(() => page.props.pendingInvites ?? []);
const bookingRequests = computed<BookingRequest[]>(() => page.props.pendingBookingRequests ?? []);
const eventRegistrations = computed<EventRegistration[]>(() => page.props.pendingEventRegistrations ?? []);
const postSuggestions = computed<PostSuggestion[]>(() => page.props.pendingPostSuggestions ?? []);
const eventSuggestions = computed<EventSuggestion[]>(() => page.props.pendingEventSuggestions ?? []);
const totalCount = computed(
    () =>
        invites.value.length +
        bookingRequests.value.length +
        eventRegistrations.value.length +
        postSuggestions.value.length +
        eventSuggestions.value.length,
);

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('ru-RU', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    });
}

function accept(id: number): void {
    router.put(`/invites/${id}/accept`);
}

function reject(id: number): void {
    router.put(`/invites/${id}/reject`);
}

function openEvents(): void {
    open.value = false;
    router.post('/admin/events/registrations/seen');
}

function openSuggestions(): void {
    open.value = false;
    router.visit('/admin/posts');
}

function openEventSuggestions(): void {
    open.value = false;
    router.visit('/admin/events');
}
</script>

<template>
    <DropdownMenu v-model:open="open">
        <DropdownMenuTrigger as-child>
            <Button variant="ghost" size="icon" class="relative h-9 w-9">
                <Bell class="h-5 w-5 opacity-80" />
                <span v-if="totalCount > 0" class="notification-badge">
                    {{ totalCount > 9 ? '9+' : totalCount }}
                </span>
            </Button>
        </DropdownMenuTrigger>

        <DropdownMenuContent align="end" class="w-80">
            <DropdownMenuLabel class="flex items-center gap-2">
                <Bell class="h-4 w-4" />
                Уведомления
                <Badge v-if="totalCount > 0" variant="secondary" class="ml-auto">
                    {{ totalCount }}
                </Badge>
            </DropdownMenuLabel>
            <DropdownMenuSeparator />

            <div v-if="totalCount === 0" class="px-3 py-6 text-center text-sm text-muted-foreground">
                Нет новых уведомлений
            </div>

            <!-- Приглашения -->
            <div v-if="invites.length > 0" class="max-h-80 overflow-y-auto">
                <div v-for="invite in invites" :key="invite.id" class="border-b px-3 py-3 last:border-0">
                    <p class="mb-1 text-sm">
                        <span class="font-medium">{{ invite.author?.name }}</span>
                        приглашает вас
                    </p>
                    <div v-if="invite.reservation" class="mb-2 space-y-0.5">
                        <div class="flex items-center gap-1.5 text-xs text-muted-foreground">
                            <CalendarDays class="h-3 w-3" />
                            {{ formatDate(invite.reservation.date) }}
                        </div>
                        <div
                            v-if="invite.reservation.table"
                            class="flex items-center gap-1.5 text-xs text-muted-foreground"
                        >
                            <Table2 class="h-3 w-3" />
                            {{ invite.reservation.table.name }}
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <Button size="sm" class="h-7 flex-1 gap-1 text-xs" @click="accept(invite.id)">
                            <Check class="h-3 w-3" />
                            Принять
                        </Button>
                        <Button
                            size="sm"
                            variant="outline"
                            class="btn-danger h-7 flex-1 gap-1 text-xs"
                            @click="reject(invite.id)"
                        >
                            <X class="h-3 w-3" />
                            Отклонить
                        </Button>
                    </div>
                </div>
            </div>

            <!-- Заявки на бронирование (только для админов) -->
            <template v-if="bookingRequests.length > 0">
                <DropdownMenuSeparator v-if="invites.length > 0" />
                <div class="max-h-60 overflow-y-auto">
                    <div v-for="req in bookingRequests" :key="`br-${req.id}`" class="border-b px-3 py-3 last:border-0">
                        <p class="mb-1 text-sm">
                            <span class="font-medium">{{ req.author?.name }}</span>
                            подал заявку на бронирование
                        </p>
                        <div class="mb-2 space-y-0.5">
                            <div class="flex items-center gap-1.5 text-xs text-muted-foreground">
                                <CalendarDays class="h-3 w-3" />
                                {{ formatDate(req.date) }}
                            </div>
                            <div v-if="req.table" class="flex items-center gap-1.5 text-xs text-muted-foreground">
                                <Table2 class="h-3 w-3" />
                                {{ req.table.name }}
                            </div>
                        </div>
                        <Button
                            size="sm"
                            variant="outline"
                            class="h-7 w-full text-xs"
                            @click="
                                open = false;
                                router.visit('/admin/requests');
                            "
                        >
                            Рассмотреть
                        </Button>
                    </div>
                </div>
            </template>

            <!-- Записи на события (только для админов) -->
            <template v-if="eventRegistrations.length > 0">
                <DropdownMenuSeparator v-if="invites.length > 0 || bookingRequests.length > 0" />
                <div class="max-h-60 overflow-y-auto">
                    <div v-for="reg in eventRegistrations" :key="`er-${reg.id}`" class="border-b px-3 py-3 last:border-0">
                        <p class="mb-1 text-sm">
                            <span class="font-medium">{{ reg.user_name }}</span>
                            записался на событие
                        </p>
                        <div class="mb-2 flex items-center gap-1.5 text-xs text-muted-foreground">
                            <CalendarCheck class="h-3 w-3" />
                            {{ reg.event_title }}
                        </div>
                        <Button size="sm" variant="outline" class="h-7 w-full text-xs" @click="openEvents">
                            Открыть события
                        </Button>
                    </div>
                </div>
            </template>

            <!-- Предложения новостей (только для админов) -->
            <template v-if="postSuggestions.length > 0">
                <DropdownMenuSeparator v-if="invites.length > 0 || bookingRequests.length > 0 || eventRegistrations.length > 0" />
                <div class="max-h-60 overflow-y-auto">
                    <div
                        v-for="suggestion in postSuggestions"
                        :key="`ps-${suggestion.id}`"
                        class="border-b px-3 py-3 last:border-0"
                    >
                        <p class="mb-1 text-sm">
                            <span class="font-medium">{{ suggestion.author?.name }}</span>
                            предлагает новость
                        </p>
                        <div class="mb-2 flex items-center gap-1.5 text-xs text-muted-foreground">
                            <Newspaper class="h-3 w-3 shrink-0" />
                            <span class="line-clamp-1">{{ suggestion.title }}</span>
                        </div>
                        <Button size="sm" variant="outline" class="h-7 w-full text-xs" @click="openSuggestions">
                            Рассмотреть
                        </Button>
                    </div>
                </div>
            </template>
            <!-- Предложения событий (только для админов) -->
            <template v-if="eventSuggestions.length > 0">
                <DropdownMenuSeparator v-if="invites.length > 0 || bookingRequests.length > 0 || eventRegistrations.length > 0 || postSuggestions.length > 0" />
                <div class="max-h-60 overflow-y-auto">
                    <div
                        v-for="suggestion in eventSuggestions"
                        :key="`es-${suggestion.id}`"
                        class="border-b px-3 py-3 last:border-0"
                    >
                        <p class="mb-1 text-sm">
                            <span class="font-medium">{{ suggestion.author?.name }}</span>
                            предлагает событие
                        </p>
                        <div class="mb-2 flex items-center gap-1.5 text-xs text-muted-foreground">
                            <CalendarPlus class="h-3 w-3 shrink-0" />
                            <span class="line-clamp-1">{{ suggestion.title }}</span>
                        </div>
                        <Button size="sm" variant="outline" class="h-7 w-full text-xs" @click="openEventSuggestions">
                            Рассмотреть
                        </Button>
                    </div>
                </div>
            </template>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
