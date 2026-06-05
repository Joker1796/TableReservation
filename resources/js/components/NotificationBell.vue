<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import { Bell, CalendarDays, Check, Table2, X } from 'lucide-vue-next';
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
import type { BookingRequest, Invite } from '@/types/reservation';

const open = ref(false);

const page = usePage<{
    pendingInvites: Invite[];
    pendingBookingRequests: BookingRequest[];
}>();
const invites = computed<Invite[]>(() => page.props.pendingInvites ?? []);
const bookingRequests = computed<BookingRequest[]>(
    () => page.props.pendingBookingRequests ?? [],
);
const totalCount = computed(
    () => invites.value.length + bookingRequests.value.length,
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
</script>

<template>
    <DropdownMenu v-model:open="open">
        <DropdownMenuTrigger as-child>
            <Button variant="ghost" size="icon" class="relative h-9 w-9">
                <Bell class="h-5 w-5 opacity-80" />
                <span
                    v-if="totalCount > 0"
                    class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-destructive text-[10px] font-bold text-destructive-foreground"
                >
                    {{ totalCount > 9 ? '9+' : totalCount }}
                </span>
            </Button>
        </DropdownMenuTrigger>

        <DropdownMenuContent align="end" class="w-80">
            <DropdownMenuLabel class="flex items-center gap-2">
                <Bell class="h-4 w-4" />
                Уведомления
                <Badge
                    v-if="totalCount > 0"
                    variant="secondary"
                    class="ml-auto"
                >
                    {{ totalCount }}
                </Badge>
            </DropdownMenuLabel>
            <DropdownMenuSeparator />

            <div
                v-if="totalCount === 0"
                class="px-3 py-6 text-center text-sm text-muted-foreground"
            >
                Нет новых уведомлений
            </div>

            <!-- Приглашения -->
            <div v-if="invites.length > 0" class="max-h-80 overflow-y-auto">
                <div
                    v-for="invite in invites"
                    :key="invite.id"
                    class="border-b px-3 py-3 last:border-0"
                >
                    <p class="mb-1 text-sm">
                        <span class="font-medium">{{
                            invite.author?.name
                        }}</span>
                        приглашает вас
                    </p>
                    <div v-if="invite.reservation" class="mb-2 space-y-0.5">
                        <div
                            class="flex items-center gap-1.5 text-xs text-muted-foreground"
                        >
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
                        <Button
                            size="sm"
                            class="h-7 flex-1 gap-1 text-xs"
                            @click="accept(invite.id)"
                        >
                            <Check class="h-3 w-3" />
                            Принять
                        </Button>
                        <Button
                            size="sm"
                            variant="outline"
                            class="h-7 flex-1 gap-1 text-xs text-destructive hover:bg-destructive hover:text-destructive-foreground"
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
                    <div
                        v-for="req in bookingRequests"
                        :key="`br-${req.id}`"
                        class="border-b px-3 py-3 last:border-0"
                    >
                        <p class="mb-1 text-sm">
                            <span class="font-medium">{{
                                req.author?.name
                            }}</span>
                            подал заявку на бронирование
                        </p>
                        <div class="mb-2 space-y-0.5">
                            <div
                                class="flex items-center gap-1.5 text-xs text-muted-foreground"
                            >
                                <CalendarDays class="h-3 w-3" />
                                {{ formatDate(req.date) }}
                            </div>
                            <div
                                v-if="req.table"
                                class="flex items-center gap-1.5 text-xs text-muted-foreground"
                            >
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
        </DropdownMenuContent>
    </DropdownMenu>
</template>
