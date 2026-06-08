<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import type { ReservationTable } from '@/types/reservation';

type Props = {
    table: ReservationTable;
};

const props = defineProps<Props>();

import { adminBreadcrumbs } from '@/breadcrumbs/admin';

defineOptions({
    layout: {
        breadcrumbs: adminBreadcrumbs.tables.edit,
    },
});

const form = useForm({
    name: props.table.name,
    description: props.table.description,
    status: props.table.status,
});

function submit(): void {
    form.put(`/admin/tables/${props.table.id}`);
}
</script>

<template>
    <Head title="Редактировать стол" />

    <div class="space-y-6 p-4">
        <Heading variant="small" title="Редактировать стол" :description="table.name" />

        <form class="max-w-lg space-y-5" @submit.prevent="submit">
            <div class="grid gap-2">
                <Label for="name">Название <span class="text-destructive">*</span></Label>
                <Input id="name" v-model="form.name" required maxlength="100" />
                <InputError :message="form.errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="status">Статус <span class="text-destructive">*</span></Label>
                <Select
                    :model-value="form.status"
                    @update:model-value="(v) => (form.status = v as 'ready' | 'not_ready')"
                >
                    <SelectTrigger id="status">
                        <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="ready">Готов</SelectItem>
                        <SelectItem value="not_ready">Не готов</SelectItem>
                    </SelectContent>
                </Select>
                <InputError :message="form.errors.status" />
            </div>

            <div class="grid gap-2">
                <Label for="description">Описание</Label>
                <Textarea id="description" v-model="form.description" rows="3" />
                <InputError :message="form.errors.description" />
            </div>

            <div class="flex items-center gap-3">
                <Button type="submit" :disabled="form.processing">Сохранить</Button>
                <Button variant="outline" type="button" as="a" href="/admin/tables">Отмена</Button>
            </div>
        </form>
    </div>
</template>
