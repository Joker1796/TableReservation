<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import DateTimePicker from '@/components/DateTimePicker.vue';
import InputError from '@/components/InputError.vue';
import RichTextEditor from '@/components/RichTextEditor.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

withDefaults(defineProps<{ open?: boolean }>(), { open: undefined });
const emit = defineEmits<{ 'update:open': [value: boolean] }>();

const form = useForm({
    title: '',
    content: '',
    published_at: '',
});

function submit(): void {
    form.post('/feed/posts', {
        onSuccess: () => {
            emit('update:open', false);
            form.reset();
        },
    });
}

function onOpenChange(value: boolean): void {
    emit('update:open', value);

    if (!value) {
        form.reset();
        form.clearErrors();
    }
}
</script>

<template>
    <Dialog :open="open" @update:open="onOpenChange">
        <DialogTrigger v-if="$slots.default" as-child>
            <slot />
        </DialogTrigger>
        <DialogContent class="sm:max-w-2xl">
            <DialogHeader>
                <DialogTitle>Новая публикация</DialogTitle>
            </DialogHeader>
            <form class="space-y-4" @submit.prevent="submit">
                <div class="grid gap-2">
                    <Label for="post-title">Заголовок <span class="text-destructive">*</span></Label>
                    <Input id="post-title" v-model="form.title" placeholder="Заголовок публикации" required maxlength="255" />
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

                <DialogFooter>
                    <Button type="button" variant="outline" @click="onOpenChange(false)">Отмена</Button>
                    <Button type="submit" :disabled="form.processing">Создать</Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
