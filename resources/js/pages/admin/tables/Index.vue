<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Edit, Plus, Trash2 } from 'lucide-vue-next';
import Pagination from '@/components/Pagination.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import type { Paginated } from '@/types/pagination';
import type { ReservationTable } from '@/types/reservation';

type Props = {
    tables: Paginated<ReservationTable>;
};

defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Админ', href: '/admin/tables' },
            { title: 'Столы', href: '/admin/tables' },
        ],
    },
});

function deleteTable(id: number): void {
    if (confirm('Удалить стол?')) {
        router.delete(`/admin/tables/${id}`);
    }
}
</script>

<template>
    <Head title="Управление столами" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold">Столы</h1>
                <p class="text-sm text-muted-foreground">
                    Управление столами клуба
                </p>
            </div>
            <Button as-child>
                <Link href="/admin/tables/create">
                    <Plus class="h-4 w-4" />
                    Добавить стол
                </Link>
            </Button>
        </div>

        <div v-if="tables.data.length === 0" class="empty-state">
            <p class="text-muted-foreground">Столов пока нет</p>
            <Button class="mt-4" as-child>
                <Link href="/admin/tables/create">Добавить первый стол</Link>
            </Button>
        </div>

        <div v-else class="flex flex-col gap-4">
            <div class="panel-table">
                <table>
                    <thead>
                        <tr>
                            <th class="col-th text-left">Название</th>
                            <th class="col-th text-left">Описание</th>
                            <th class="col-th text-left">Статус</th>
                            <th class="col-th text-right">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="table in tables.data" :key="table.id">
                            <td class="col-td font-medium">{{ table.name }}</td>
                            <td class="col-td max-w-xs text-muted-foreground">
                                <span class="line-clamp-1">{{
                                    table.description || '—'
                                }}</span>
                            </td>
                            <td class="col-td">
                                <Badge
                                    :variant="
                                        table.status === 'ready'
                                            ? 'default'
                                            : 'secondary'
                                    "
                                >
                                    {{
                                        table.status === 'ready'
                                            ? 'Готов'
                                            : 'Не готов'
                                    }}
                                </Badge>
                            </td>
                            <td class="col-td">
                                <div class="flex justify-end gap-2">
                                    <Button
                                        variant="outline"
                                        size="icon"
                                        as-child
                                    >
                                        <Link
                                            :href="`/admin/tables/${table.id}/edit`"
                                        >
                                            <Edit class="h-4 w-4" />
                                        </Link>
                                    </Button>
                                    <Button
                                        variant="outline"
                                        size="icon"
                                        class="btn-danger"
                                        @click="deleteTable(table.id)"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <Pagination :links="tables.links" />
        </div>
    </div>
</template>
