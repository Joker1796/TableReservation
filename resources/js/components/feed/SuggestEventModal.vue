<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
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
import { Textarea } from '@/components/ui/textarea';

withDefaults(defineProps<{ open?: boolean }>(), { open: undefined });
const emit = defineEmits<{ 'update:open': [value: boolean] }>();

const form = useForm({
    title: '',
    description: '',
});

function submit(): void {
    form.post('/feed/events/suggest', {
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
        <DialogContent class="sm:max-w-lg">
            <DialogHeader>
                <DialogTitle>Предложить событие</DialogTitle>
            </DialogHeader>
            <form class="space-y-4" @submit.prevent="submit">
                <div class="grid gap-2">
                    <Label for="suggest-event-title">Название <span class="text-destructive">*</span></Label>
                    <Input
                        id="suggest-event-title"
                        v-model="form.title"
                        placeholder="Название события"
                        required
                        maxlength="255"
                    />
                    <InputError :message="form.errors.title" />
                </div>

                <div class="grid gap-2">
                    <Label for="suggest-event-desc">Описание <span class="text-destructive">*</span></Label>
                    <Textarea
                        id="suggest-event-desc"
                        v-model="form.description"
                        placeholder="Расскажите подробнее о событии..."
                        rows="5"
                        required
                        maxlength="5000"
                        class="resize-none"
                    />
                    <InputError :message="form.errors.description" />
                </div>

                <DialogFooter>
                    <Button type="button" variant="outline" @click="onOpenChange(false)">Отмена</Button>
                    <Button type="submit" :disabled="form.processing">Отправить</Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
