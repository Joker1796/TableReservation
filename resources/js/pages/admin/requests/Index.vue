<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { Check, Trash2, X } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import type { ReservationRequest, ReservationTable } from '@/types/reservation';

type Props = {
    requests: ReservationRequest[];
    tables: ReservationTable[];
};

defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Админ', href: '/admin/tables' },
            { title: 'Заявки', href: '/admin/requests' },
        ],
    },
});

const statusLabel: Record<number, string> = { 0: 'Новая', 1: 'Одобрена', 2: 'Отклонена' };
const statusVariant: Record<number, 'default' | 'secondary' | 'destructive' | 'outline'> = {
    0: 'secondary',
    1: 'default',
    2: 'destructive',
};

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
            <Badge variant="secondary">Всего: {{ requests.length }}</Badge>
            <Badge variant="secondary">Новых: {{ requests.filter(r => r.status === 0).length }}</Badge>
            <Badge variant="default">Одобрено: {{ requests.filter(r => r.status === 1).length }}</Badge>
            <Badge variant="destructive">Отклонено: {{ requests.filter(r => r.status === 2).length }}</Badge>
        </div>

        <div v-if="requests.length === 0" class="flex items-center justify-center rounded-xl border border-dashed border-sidebar-border/70 py-20 text-center dark:border-sidebar-border">
            <p class="text-muted-foreground">Заявок пока нет</p>
        </div>

        <div v-else class="overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
            <table class="w-full text-sm">
                <thead class="border-b border-sidebar-border/70 bg-muted/40 dark:border-sidebar-border">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-muted-foreground">Пользователь</th>
                        <th class="px-4 py-3 text-left font-medium text-muted-foreground">Дата</th>
                        <th class="px-4 py-3 text-left font-medium text-muted-foreground">Часы</th>
                        <th class="px-4 py-3 text-left font-medium text-muted-foreground">Стол</th>
                        <th class="px-4 py-3 text-left font-medium text-muted-foreground">Комментарий</th>
                        <th class="px-4 py-3 text-left font-medium text-muted-foreground">Статус</th>
                        <th class="px-4 py-3 text-right font-medium text-muted-foreground">Действия</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sidebar-border/70 dark:divide-sidebar-border">
                    <tr v-for="req in requests" :key="req.id" class="hover:bg-muted/20">
                        <td class="px-4 py-3">
                            <div v-if="req.author">
                                <p class="font-medium">{{ req.author.name }}</p>
                                <p class="text-xs text-muted-foreground">{{ req.author.email }}</p>
                            </div>
                            <span v-else class="text-muted-foreground">—</span>
                        </td>
                        <td class="px-4 py-3 font-medium">{{ formatDate(req.date) }}</td>
                        <td class="px-4 py-3 text-muted-foreground">
                            {{ req.hours ? `${req.hours} ч.` : '—' }}
                        </td>
                        <td class="px-4 py-3">
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
                                    <SelectItem
                                        v-for="table in tables"
                                        :key="table.id"
                                        :value="String(table.id)"
                                    >
                                        {{ table.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </td>
                        <td class="max-w-[180px] px-4 py-3 text-muted-foreground">
                            <span class="line-clamp-2">{{ req.comment || '—' }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <Badge :variant="statusVariant[req.status]">
                                {{ statusLabel[req.status] }}
                            </Badge>
                        </td>
                        <td class="px-4 py-3">
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
                                    class="text-destructive hover:bg-destructive hover:text-destructive-foreground"
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
    </div>
</template>
