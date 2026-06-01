<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Edit, Plus, Trash2 } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import type { ReservationTable } from '@/types/reservation';

type Props = {
    tables: ReservationTable[];
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
                <p class="text-sm text-muted-foreground">Управление столами клуба</p>
            </div>
            <Button as-child>
                <Link href="/admin/tables/create">
                    <Plus class="h-4 w-4" />
                    Добавить стол
                </Link>
            </Button>
        </div>

        <div v-if="tables.length === 0" class="flex flex-col items-center justify-center rounded-xl border border-dashed border-sidebar-border/70 py-20 text-center dark:border-sidebar-border">
            <p class="text-muted-foreground">Столов пока нет</p>
            <Button class="mt-4" as-child>
                <Link href="/admin/tables/create">Добавить первый стол</Link>
            </Button>
        </div>

        <div v-else class="overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
            <table class="w-full text-sm">
                <thead class="border-b border-sidebar-border/70 bg-muted/40 dark:border-sidebar-border">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-muted-foreground">Название</th>
                        <th class="px-4 py-3 text-left font-medium text-muted-foreground">Описание</th>
                        <th class="px-4 py-3 text-left font-medium text-muted-foreground">Статус</th>
                        <th class="px-4 py-3 text-right font-medium text-muted-foreground">Действия</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sidebar-border/70 dark:divide-sidebar-border">
                    <tr v-for="table in tables" :key="table.id" class="hover:bg-muted/20">
                        <td class="px-4 py-3 font-medium">{{ table.name }}</td>
                        <td class="max-w-xs px-4 py-3 text-muted-foreground">
                            <span class="line-clamp-1">{{ table.description || '—' }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <Badge :variant="table.status === 'ready' ? 'default' : 'secondary'">
                                {{ table.status === 'ready' ? 'Готов' : 'Не готов' }}
                            </Badge>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <Button variant="outline" size="icon" as-child>
                                    <Link :href="`/admin/tables/${table.id}/edit`">
                                        <Edit class="h-4 w-4" />
                                    </Link>
                                </Button>
                                <Button
                                    variant="outline"
                                    size="icon"
                                    class="text-destructive hover:bg-destructive hover:text-destructive-foreground"
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
    </div>
</template>
