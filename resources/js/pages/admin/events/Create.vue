<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { adminBreadcrumbs } from '@/breadcrumbs/admin';
import EventFormFields from '@/components/feed/EventFormFields.vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';

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
            <EventFormFields
                :title="form.title"
                :short-description="form.short_description"
                :description="form.description"
                :starts-at="form.starts_at"
                :ends-at="form.ends_at"
                :errors="form.errors"
                @update:title="form.title = $event"
                @update:short-description="form.short_description = $event"
                @update:description="form.description = $event"
                @update:starts-at="form.starts_at = $event"
                @update:ends-at="form.ends_at = $event"
            />

            <div class="flex items-center gap-3">
                <Button type="submit" :disabled="form.processing">Создать</Button>
                <Button variant="outline" type="button" as="a" href="/admin/events">Отмена</Button>
            </div>
        </form>
    </div>
</template>
