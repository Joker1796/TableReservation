<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import EventFormFields from '@/components/feed/EventFormFields.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';

withDefaults(defineProps<{ open?: boolean }>(), { open: undefined });
const emit = defineEmits<{ 'update:open': [value: boolean] }>();

const form = useForm({
    title: '',
    short_description: '',
    description: '',
    starts_at: '',
    ends_at: '',
});

function submit(): void {
    form.post('/feed/events', {
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
                <DialogTitle>Новое событие</DialogTitle>
            </DialogHeader>
            <form class="space-y-4" @submit.prevent="submit">
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

                <DialogFooter>
                    <Button type="button" variant="outline" @click="onOpenChange(false)">Отмена</Button>
                    <Button type="submit" :disabled="form.processing">Создать</Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
