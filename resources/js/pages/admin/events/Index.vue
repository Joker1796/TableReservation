<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Check, Edit, Plus, Trash2 } from 'lucide-vue-next';
import { adminBreadcrumbs } from '@/breadcrumbs/admin';
import Pagination from '@/components/Pagination.vue';
import ParticipantBadge from '@/components/ParticipantBadge.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import type { Event } from '@/types/event';
import type { Paginated } from '@/types/pagination';

type Props = {
    events: Paginated<Event>;
};

defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: adminBreadcrumbs.events.index,
    },
});

function formatDate(iso: string | null): string {
    if (!iso) {
        return '—';
    }

    return new Date(iso).toLocaleDateString('ru-RU', { day: 'numeric', month: 'short', year: 'numeric' });
}

function deleteEvent(id: number): void {
    if (confirm('Удалить событие?')) {
        router.delete(`/admin/events/${id}`);
    }
}

function approveEvent(id: number): void {
    router.put(`/admin/events/${id}/approve`);
}
</script>

<template>
    <Head title="События" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold">События</h1>
                <p class="text-sm text-muted-foreground">Управление событиями клуба</p>
            </div>
            <Button as-child class="sm:shrink-0">
                <Link href="/admin/events/create">
                    <Plus class="h-4 w-4" />
                    Добавить
                </Link>
            </Button>
        </div>

        <div v-if="events.data.length === 0" class="empty-state">
            <p class="text-muted-foreground">Событий пока нет</p>
            <Button class="mt-4" as-child>
                <Link href="/admin/events/create">Добавить первое событие</Link>
            </Button>
        </div>

        <div v-else class="flex flex-col gap-4">
            <div class="panel-table">
                <table>
                    <thead>
                        <tr>
                            <th class="col-th text-left">Название</th>
                            <th class="col-th text-left">Статус / Начало</th>
                            <th class="col-th text-left">Конец</th>
                            <th class="col-th text-left">Участники</th>
                            <th class="col-th text-right">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="event in events.data" :key="event.id">
                            <td class="col-td font-medium">
                                <span class="line-clamp-1">{{ event.title }}</span>
                            </td>
                            <td class="col-td">
                                <Badge v-if="event.is_suggestion" variant="secondary">Предложено</Badge>
                                <span v-else class="text-muted-foreground">{{ formatDate(event.starts_at) }}</span>
                            </td>
                            <td class="col-td text-muted-foreground">{{ formatDate(event.ends_at) }}</td>
                            <td class="col-td">
                                <div v-if="event.participants.length > 0" class="flex flex-wrap gap-1">
                                    <ParticipantBadge v-for="p in event.participants" :key="p.id" :participant="p" />
                                </div>
                                <span v-else class="text-muted-foreground">—</span>
                            </td>
                            <td class="col-td">
                                <div class="flex justify-end gap-2">
                                    <Button
                                        v-if="event.is_suggestion"
                                        variant="outline"
                                        size="sm"
                                        class="gap-1 text-xs"
                                        @click="approveEvent(event.id)"
                                    >
                                        <Check class="h-3 w-3" />
                                        Одобрить
                                    </Button>
                                    <Button variant="outline" size="icon" as-child>
                                        <Link :href="`/admin/events/${event.id}/edit`">
                                            <Edit class="h-4 w-4" />
                                        </Link>
                                    </Button>
                                    <Button variant="outline" size="icon" class="btn-danger" @click="deleteEvent(event.id)">
                                        <Trash2 class="h-4 w-4" />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <Pagination :links="events.links" />
        </div>
    </div>
</template>
