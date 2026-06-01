<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { CalendarDays, Clock, Plus, Table2, Users } from 'lucide-vue-next';
import { computed } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import type { Auth } from '@/types/auth';
import type { Reservation } from '@/types/reservation';

type Props = {
    reservations: Reservation[];
    authUserId: number;
};

const props = defineProps<Props>();

const page = usePage<{ auth: Auth }>();
const isAdmin = computed(() => page.props.auth?.user?.is_admin === true);

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Резервирования', href: '/reservations' },
        ],
    },
});

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('ru-RU', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    });
}

function getInitial(name: string): string {
    return name.charAt(0).toUpperCase();
}

function isMine(reservation: Reservation): boolean {
    return reservation.users.some((u) => u.id === props.authUserId);
}

function deleteReservation(id: number): void {
    if (confirm('Удалить резервирование?')) {
        router.delete(`/reservations/${id}`);
    }
}
</script>

<template>
    <Head title="Резервирования" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold">Резервирования</h1>
                <p class="text-sm text-muted-foreground">Все активные бронирования столов</p>
            </div>
            <Button v-if="isAdmin" as-child>
                <Link href="/reservations/create">
                    <Plus class="h-4 w-4" />
                    Создать
                </Link>
            </Button>
        </div>

        <div
            v-if="reservations.length === 0"
            class="flex flex-col items-center justify-center rounded-xl border border-dashed border-sidebar-border/70 py-20 text-center dark:border-sidebar-border"
        >
            <CalendarDays class="mb-4 h-12 w-12 text-muted-foreground/50" />
            <p class="text-lg font-medium text-muted-foreground">Нет резервирований</p>
            <p class="mt-1 text-sm text-muted-foreground">Создайте первое бронирование стола</p>
            <Button v-if="isAdmin" class="mt-4" as-child>
                <Link href="/reservations/create">Создать резервирование</Link>
            </Button>
        </div>

        <div v-else class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            <Card v-for="reservation in reservations" :key="reservation.id">
                <CardHeader class="pb-3">
                    <div class="flex items-start justify-between">
                        <div>
                            <CardTitle class="text-base">
                                {{ reservation.table ? reservation.table.name : 'Стол не выбран' }}
                            </CardTitle>
                            <CardDescription class="mt-1 flex items-center gap-1">
                                <CalendarDays class="h-3.5 w-3.5" />
                                {{ formatDate(reservation.date) }}
                            </CardDescription>
                        </div>
                        <Badge v-if="reservation.table" variant="outline">
                            {{ reservation.table.status === 'ready' ? 'Готов' : 'Не готов' }}
                        </Badge>
                    </div>
                </CardHeader>
                <CardContent class="space-y-3">
                    <div v-if="reservation.hours" class="flex items-center gap-2 text-sm text-muted-foreground">
                        <Clock class="h-3.5 w-3.5" />
                        {{ reservation.hours }} ч.
                    </div>
                    <div v-if="reservation.table" class="flex items-center gap-2 text-sm text-muted-foreground">
                        <Table2 class="h-3.5 w-3.5" />
                        {{ reservation.table.description || reservation.table.name }}
                    </div>
                    <p v-if="reservation.comment" class="line-clamp-2 text-sm text-muted-foreground">
                        {{ reservation.comment }}
                    </p>

                    <div v-if="reservation.users.length > 0" class="pt-1">
                        <p class="mb-2 flex items-center gap-1 text-xs text-muted-foreground">
                            <Users class="h-3.5 w-3.5" />
                            Участники ({{ reservation.users.length }})
                        </p>
                        <div class="flex flex-wrap gap-1.5">
                            <div
                                v-for="user in reservation.users"
                                :key="user.id"
                                class="flex items-center gap-1.5 rounded-full border bg-muted/50 py-0.5 pl-0.5 pr-2.5 text-xs"
                                :title="user.email"
                            >
                                <div
                                    class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full text-[10px] font-semibold"
                                    :class="user.id === authUserId ? 'bg-primary text-primary-foreground' : 'bg-muted-foreground/20 text-foreground'"
                                >
                                    {{ getInitial(user.name) }}
                                </div>
                                <span class="max-w-[100px] truncate font-medium">{{ user.name }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <Button variant="outline" size="sm" as-child class="flex-1">
                            <Link :href="`/reservations/${reservation.id}`">Подробнее</Link>
                        </Button>
                        <template v-if="isMine(reservation)">
                            <Button variant="outline" size="sm" as-child>
                                <Link :href="`/reservations/${reservation.id}/edit`">Изменить</Link>
                            </Button>
                            <Button
                                variant="outline"
                                size="sm"
                                class="text-destructive hover:bg-destructive hover:text-destructive-foreground"
                                @click="deleteReservation(reservation.id)"
                            >
                                Удалить
                            </Button>
                        </template>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
