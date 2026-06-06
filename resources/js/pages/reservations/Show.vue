<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { CalendarDays, Edit, Table2, Trash2, Users } from 'lucide-vue-next';
import { computed } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Separator } from '@/components/ui/separator';
import type { Reservation } from '@/types/reservation';

type Props = {
    reservation: Reservation;
    authUserId: number;
};

const props = defineProps<Props>();

const isMine = computed(() => props.reservation.users.some((u) => u.id === props.authUserId));

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Резервирования', href: '/reservations' },
            { title: 'Подробнее', href: '#' },
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

function deleteReservation(): void {
    if (confirm('Удалить резервирование?')) {
        router.delete(`/reservations/${props.reservation.id}`);
    }
}
</script>

<template>
    <Head title="Резервирование" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold">Резервирование #{{ reservation.id }}</h1>
            <div v-if="isMine" class="flex items-center gap-2">
                <Button variant="outline" size="sm" as-child>
                    <Link :href="`/reservations/${reservation.id}/edit`">
                        <Edit class="h-4 w-4" />
                        Редактировать
                    </Link>
                </Button>
                <Button variant="outline" size="sm" class="btn-danger" @click="deleteReservation">
                    <Trash2 class="h-4 w-4" />
                    Удалить
                </Button>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <Card>
                <CardHeader>
                    <CardTitle class="text-base">Детали бронирования</CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div class="flex items-center gap-3">
                        <CalendarDays class="h-4 w-4 text-muted-foreground" />
                        <div>
                            <p class="text-xs text-muted-foreground">Дата</p>
                            <p class="font-medium">
                                {{ formatDate(reservation.date) }}
                            </p>
                        </div>
                    </div>

                    <Separator />

                    <div v-if="reservation.table" class="flex items-center gap-3">
                        <Table2 class="h-4 w-4 text-muted-foreground" />
                        <div>
                            <p class="text-xs text-muted-foreground">Стол</p>
                            <div class="flex items-center gap-2">
                                <p class="font-medium">
                                    {{ reservation.table.name }}
                                </p>
                                <Badge variant="outline" class="text-xs">
                                    {{ reservation.table.status === 'ready' ? 'Готов' : 'Не готов' }}
                                </Badge>
                            </div>
                        </div>
                    </div>

                    <div v-if="reservation.comment" class="pt-1">
                        <p class="text-xs text-muted-foreground">Комментарий</p>
                        <p class="mt-1 text-sm">{{ reservation.comment }}</p>
                    </div>
                </CardContent>
            </Card>

            <Card v-if="reservation.users.length > 0">
                <CardHeader>
                    <CardTitle class="flex items-center gap-2 text-base">
                        <Users class="h-4 w-4" />
                        Участники ({{ reservation.users.length }})
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <ul class="space-y-3">
                        <li v-for="user in reservation.users" :key="user.id" class="flex items-center gap-3">
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-full text-sm font-medium"
                                :class="user.id === authUserId ? 'bg-primary text-primary-foreground' : 'bg-muted'"
                            >
                                {{ user.name.charAt(0).toUpperCase() }}
                            </div>
                            <div>
                                <p class="text-sm font-medium">
                                    {{ user.name }}
                                    <span v-if="user.id === authUserId" class="text-xs text-muted-foreground"
                                        >(вы)</span
                                    >
                                </p>
                                <p class="text-xs text-muted-foreground">
                                    {{ user.email }}
                                </p>
                            </div>
                        </li>
                    </ul>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
