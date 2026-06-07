<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Edit, Plus, Trash2 } from 'lucide-vue-next';
import Pagination from '@/components/Pagination.vue';
import { Button } from '@/components/ui/button';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import type { Event } from '@/types/event';
import type { Paginated } from '@/types/pagination';

type Props = {
    events: Paginated<Event>;
};

defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Админ', href: '/admin' },
            { title: 'События', href: '/admin/events' },
        ],
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
</script>

<template>
    <Head title="События" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold">События</h1>
                <p class="text-sm text-muted-foreground">Управление событиями клуба</p>
            </div>
            <Button as-child>
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
                            <th class="col-th text-left">Начало</th>
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
                            <td class="col-td text-muted-foreground">{{ formatDate(event.starts_at) }}</td>
                            <td class="col-td text-muted-foreground">{{ formatDate(event.ends_at) }}</td>
                            <td class="col-td">
                                <div v-if="event.participants.length > 0" class="flex flex-wrap gap-1">
                                    <Popover v-for="p in event.participants" :key="p.id">
                                        <PopoverTrigger as-child>
                                            <button class="inline-block cursor-pointer rounded bg-muted px-1.5 py-0.5 text-xs text-muted-foreground hover:bg-muted/70">
                                                {{ p.name }}
                                            </button>
                                        </PopoverTrigger>
                                        <PopoverContent class="w-56 space-y-1.5 p-3 text-sm">
                                            <p class="font-medium">{{ p.name }}</p>
                                            <p v-if="p.phone" class="text-muted-foreground">{{ p.phone }}</p>
                                            <p v-if="p.contacts" class="text-muted-foreground">{{ p.contacts }}</p>
                                            <p v-if="!p.phone && !p.contacts" class="text-muted-foreground">Контакты не указаны</p>
                                        </PopoverContent>
                                    </Popover>
                                </div>
                                <span v-else class="text-muted-foreground">—</span>
                            </td>
                            <td class="col-td">
                                <div class="flex justify-end gap-2">
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
