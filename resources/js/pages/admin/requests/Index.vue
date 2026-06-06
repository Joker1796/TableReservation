<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { Check, Trash2, X } from 'lucide-vue-next';
import Pagination from '@/components/Pagination.vue';
import RequestStatusBadge from '@/components/RequestStatusBadge.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import type { Paginated } from '@/types/pagination';
import type { BookingRequest, ReservationTable } from '@/types/reservation';

type Props = {
    requests: Paginated<BookingRequest>;
    tables: ReservationTable[];
};

defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Админ', href: '/admin/requests' },
            { title: 'Заявки', href: '/admin/requests' },
        ],
    },
});


function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('ru-RU', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
}

function updateStatus(id: number, status: number): void {
    useForm({ status }).put(`/admin/requests/${id}/status`);
}

function assignTable(id: number, tableId: string | undefined): void {
    useForm({ table_id: tableId ? Number(tableId) : null }).put(`/admin/requests/${id}/table`);
}

function deleteRequest(id: number): void {
    if (confirm('Удалить заявку?')) {
        router.delete(`/admin/requests/${id}`);
    }
}
</script>

<template>
    <Head title="Заявки на бронирование" />

    <div class="flex flex-col gap-6 p-4">
        <div>
            <h1 class="text-2xl font-semibold">Заявки на бронирование</h1>
            <p class="text-sm text-muted-foreground">Управление входящими заявками от пользователей</p>
        </div>

        <div class="flex flex-wrap gap-2">
            <Badge variant="secondary">Всего: {{ requests.total }}</Badge>
        </div>

        <div v-if="requests.data.length === 0" class="empty-state">
            <p class="text-muted-foreground">Заявок пока нет</p>
        </div>

        <div v-else class="flex flex-col gap-4">
            <div class="panel-table">
                <table>
                    <thead>
                        <tr>
                            <th class="col-th text-left">Пользователь</th>
                            <th class="col-th text-left">Дата</th>
                            <th class="col-th text-left">Стол</th>
                            <th class="col-th text-left">Комментарий</th>
                            <th class="col-th text-left">Статус</th>
                            <th class="col-th text-right">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="req in requests.data" :key="req.id">
                            <td class="col-td">
                                <div v-if="req.author">
                                    <p class="font-medium">
                                        {{ req.author.name }}
                                    </p>
                                    <p class="text-xs text-muted-foreground">
                                        {{ req.author.email }}
                                    </p>
                                </div>
                                <span v-else class="text-muted-foreground">—</span>
                            </td>
                            <td class="col-td font-medium">
                                {{ formatDate(req.date) }}
                            </td>
                            <td class="col-td">
                                <!-- Одобренная заявка: показываем только badge -->
                                <Badge v-if="req.status === 1 && req.table" variant="outline">
                                    {{ req.table.name }}
                                </Badge>
                                <span v-else-if="req.status === 1" class="text-muted-foreground">—</span>
                                <!-- Остальные: inline-выбор стола -->
                                <Select
                                    v-else
                                    :model-value="req.table_id ? String(req.table_id) : undefined"
                                    @update:model-value="(v) => assignTable(req.id, v != null ? String(v) : undefined)"
                                >
                                    <SelectTrigger class="h-8 w-36 text-xs">
                                        <SelectValue placeholder="Выбрать стол" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="table in tables" :key="table.id" :value="String(table.id)">
                                            {{ table.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </td>
                            <td class="col-td max-w-[180px] text-muted-foreground">
                                <span class="line-clamp-2">{{ req.comment || '—' }}</span>
                            </td>
                            <td class="col-td">
                                <RequestStatusBadge :status="(req.status as 0 | 1 | 2)" />
                            </td>
                            <td class="col-td">
                                <div class="flex justify-end gap-1">
                                    <Button
                                        v-if="req.status !== 1"
                                        variant="outline"
                                        size="icon"
                                        class="text-green-600 hover:bg-green-600 hover:text-white"
                                        title="Одобрить"
                                        @click="updateStatus(req.id, 1)"
                                    >
                                        <Check class="h-4 w-4" />
                                    </Button>
                                    <Button
                                        v-if="req.status !== 2"
                                        variant="outline"
                                        size="icon"
                                        class="btn-danger"
                                        title="Отклонить"
                                        @click="updateStatus(req.id, 2)"
                                    >
                                        <X class="h-4 w-4" />
                                    </Button>
                                    <Button
                                        variant="outline"
                                        size="icon"
                                        class="text-muted-foreground hover:text-destructive"
                                        title="Удалить"
                                        @click="deleteRequest(req.id)"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <Pagination :links="requests.links" />
        </div>
    </div>
</template>
