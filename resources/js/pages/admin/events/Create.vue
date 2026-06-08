<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { adminBreadcrumbs } from '@/breadcrumbs/admin';
import DateTimePicker from '@/components/DateTimePicker.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';


defineOptions({
    layout: {
        breadcrumbs: adminBreadcrumbs.events.create,
    },
});

const form = useForm({
    title: '',
    short_description: '',
    description: '',
    starts_at: '',
    ends_at: '',
});

function submit(): void {
    form.post('/admin/events');
}
</script>

<template>
    <Head title="Новое событие" />

    <div class="space-y-6 p-4">
        <Heading variant="small" title="Новое событие" description="Добавьте событие клуба" />

        <form class="max-w-2xl space-y-5" @submit.prevent="submit">
            <div class="grid gap-2">
                <Label for="title">Название <span class="text-destructive">*</span></Label>
                <Input id="title" v-model="form.title" placeholder="Название события" required maxlength="255" />
                <InputError :message="form.errors.title" />
            </div>

            <div class="grid gap-2">
                <Label for="short_description">Краткое описание <span class="text-muted-foreground text-xs">(для ленты)</span></Label>
                <Textarea id="short_description" v-model="form.short_description" placeholder="Пара предложений для анонса" rows="2" maxlength="150" />
                <div class="flex justify-end text-xs text-muted-foreground">{{ form.short_description?.length ?? 0 }}/150</div>
                <InputError :message="form.errors.short_description" />
            </div>

            <div class="grid gap-2">
                <Label for="description">Описание</Label>
                <Textarea id="description" v-model="form.description" placeholder="Подробное описание события" rows="4" />
                <InputError :message="form.errors.description" />
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

            <div class="flex items-center gap-3">
                <Button type="submit" :disabled="form.processing">Создать</Button>
                <Button variant="outline" type="button" as="a" href="/admin/events">Отмена</Button>
            </div>
        </form>
    </div>
</template>
