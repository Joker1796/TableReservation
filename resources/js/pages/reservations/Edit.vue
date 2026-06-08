<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import DateTimePicker from '@/components/DateTimePicker.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import type { Reservation, ReservationTable } from '@/types/reservation';

type Props = {
    reservation: Reservation;
    tables: ReservationTable[];
};

const props = defineProps<Props>();

import { reservationBreadcrumbs } from '@/breadcrumbs/reservations';

defineOptions({
    layout: {
        breadcrumbs: reservationBreadcrumbs.edit,
    },
});

const form = useForm({
    date: props.reservation.date.replace(' ', 'T').substring(0, 16),
    comment: props.reservation.comment,
    table_id: props.reservation.table_id,
});

function submit(): void {
    form.put(`/reservations/${props.reservation.id}`);
}
</script>

<template>
    <Head title="Редактировать резервирование" />

    <div class="space-y-6 p-4">
        <Heading variant="small" title="Редактировать резервирование" description="Измените данные бронирования" />

        <form class="max-w-lg space-y-5" @submit.prevent="submit">
            <div class="grid gap-2">
                <Label for="date">Дата <span class="text-destructive">*</span></Label>
                <DateTimePicker v-model="form.date" />
                <InputError :message="form.errors.date" />
            </div>

            <div class="grid gap-2">
                <Label for="table_id">Стол</Label>
                <Select
                    :model-value="form.table_id ? String(form.table_id) : undefined"
                    @update:model-value="(v) => (form.table_id = v ? Number(v) : null)"
                >
                    <SelectTrigger id="table_id">
                        <SelectValue placeholder="Выберите стол (необязательно)" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem v-for="table in tables" :key="table.id" :value="String(table.id)">
                            {{ table.name }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <InputError :message="form.errors.table_id" />
            </div>

            <div class="grid gap-2">
                <Label for="comment">Комментарий</Label>
                <Textarea id="comment" v-model="form.comment" rows="3" placeholder="Дополнительная информация..." />
                <InputError :message="form.errors.comment" />
            </div>

            <div class="flex items-center gap-3">
                <Button type="submit" :disabled="form.processing"> Сохранить изменения </Button>
                <Button variant="outline" type="button" as="a" :href="`/reservations/${reservation.id}`">
                    Отмена
                </Button>
            </div>
        </form>
    </div>
</template>
