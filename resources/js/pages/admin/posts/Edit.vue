<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import DateTimePicker from '@/components/DateTimePicker.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import RichTextEditor from '@/components/RichTextEditor.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { Post } from '@/types/feed';

type Props = {
    post: Post;
};

const props = defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Публикации', href: '/admin/posts' },
            { title: 'Редактировать', href: '#' },
        ],
    },
});

function toDatetimeLocal(iso: string | null): string {
    if (!iso) return '';
    const d = new Date(iso);
    return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}T${String(d.getHours()).padStart(2, '0')}:${String(d.getMinutes()).padStart(2, '0')}`;
}

const form = useForm({
    title: props.post.title,
    content: props.post.content,
    published_at: toDatetimeLocal(props.post.published_at),
});

function submit(): void {
    form.put(`/admin/posts/${props.post.id}`);
}
</script>

<template>
    <Head title="Редактировать публикацию" />

    <div class="space-y-6 p-4">
        <Heading variant="small" title="Редактировать публикацию" :description="post.title" />

        <form class="max-w-2xl space-y-5" @submit.prevent="submit">
            <div class="grid gap-2">
                <Label for="title">Заголовок <span class="text-destructive">*</span></Label>
                <Input id="title" v-model="form.title" required maxlength="255" />
                <InputError :message="form.errors.title" />
            </div>

            <div class="grid gap-2">
                <Label>Дата публикации</Label>
                <DateTimePicker v-model="form.published_at" />
                <InputError :message="form.errors.published_at" />
            </div>

            <div class="grid gap-2">
                <Label>Содержание <span class="text-destructive">*</span></Label>
                <RichTextEditor v-model="form.content" />
                <InputError :message="form.errors.content" />
            </div>

            <div class="flex items-center gap-3">
                <Button type="submit" :disabled="form.processing">Сохранить</Button>
                <Button variant="outline" type="button" as="a" href="/admin/posts">Отмена</Button>
            </div>
        </form>
    </div>
</template>
