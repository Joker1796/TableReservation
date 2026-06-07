<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import DateTimePicker from '@/components/DateTimePicker.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import type { Event } from '@/types/event';

type Props = {
    event: Event;
};

const props = defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Админ', href: '/admin' },
            { title: 'События', href: '/admin/events' },
            { title: 'Редактировать', href: '#' },
        ],
    },
});

function toDatetimeLocal(iso: string | null): string {
    if (!iso) {
        return '';
    }

    const d = new Date(iso);

    return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}T${String(d.getHours()).padStart(2, '0')}:${String(d.getMinutes()).padStart(2, '0')}`;
}

const form = useForm({
    title: props.event.title,
    short_description: props.event.short_description ?? '',
    description: props.event.description ?? '',
    starts_at: toDatetimeLocal(props.event.starts_at),
    ends_at: toDatetimeLocal(props.event.ends_at),
});

function submit(): void {
    form.put(`/admin/events/${props.event.id}`);
}
</script>

<template>
    <Head title="Редактировать событие" />

    <div class="space-y-6 p-4">
        <Heading variant="small" title="Редактировать событие" :description="event.title" />

        <form class="max-w-2xl space-y-5" @submit.prevent="submit">
            <div class="grid gap-2">
                <Label for="title">Название <span class="text-destructive">*</span></Label>
                <Input id="title" v-model="form.title" required maxlength="255" />
                <InputError :message="form.errors.title" />
            </div>

            <div class="grid gap-2">
                <Label for="short_description">Краткое описание <span class="text-muted-foreground text-xs">(для ленты)</span></Label>
                <Textarea id="short_description" v-model="form.short_description" rows="2" maxlength="500" />
                <InputError :message="form.errors.short_description" />
            </div>

            <div class="grid gap-2">
                <Label>Начало <span class="text-destructive">*</span></Label>
                <DateTimePicker v-model="form.starts_at" />
                <InputError :message="form.errors.starts_at" />
            </div>

            <div class="grid gap-2">
                <Label>Конец</Label>
                <DateTimePicker v-model="form.ends_at" />
                <InputError :message="form.errors.ends_at" />
            </div>

            <div class="grid gap-2">
                <Label for="description">Описание</Label>
                <Textarea id="description" v-model="form.description" rows="4" />
                <InputError :message="form.errors.description" />
            </div>

            <div class="flex items-center gap-3">
                <Button type="submit" :disabled="form.processing">Сохранить</Button>
                <Button variant="outline" type="button" as="a" href="/admin/events">Отмена</Button>
            </div>
        </form>
    </div>
</template>
