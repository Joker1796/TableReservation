<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { DateInput } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { store } from '@/routes/booking-requests';

const open = ref(false);

const form = useForm({
    date: '',
    comment: '' as string | null,
});

function submit(): void {
    form.post(store().url, {
        onSuccess: () => {
            open.value = false;
            form.reset();
        },
    });
}

function onOpenChange(value: boolean): void {
    open.value = value;
    if (!value) {
        form.reset();
        form.clearErrors();
    }
}
</script>

<template>
    <Dialog :open="open" @update:open="onOpenChange">
        <DialogTrigger as-child>
            <slot />
        </DialogTrigger>
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>Заявка на бронирование стола</DialogTitle>
                <DialogDescription>
                    Укажите дату и продолжительность. Администратор назначит стол и подтвердит заявку.
                </DialogDescription>
            </DialogHeader>
            <form class="space-y-4" @submit.prevent="submit">
                <div class="grid gap-2">
                    <Label for="modal-date">Дата <span class="text-destructive">*</span></Label>
                    <DateInput id="modal-date" v-model="form.date" required />
                    <InputError :message="form.errors.date" />
                </div>
                <div class="grid gap-2">
                    <Label for="modal-comment">Комментарий</Label>
                    <textarea
                        id="modal-comment"
                        v-model="form.comment"
                        rows="3"
                        placeholder="Дополнительные пожелания..."
                        class="flex min-h-[72px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    />
                    <InputError :message="form.errors.comment" />
                </div>
                <DialogFooter>
                    <Button type="button" variant="outline" @click="onOpenChange(false)">Отмена</Button>
                    <Button type="submit" :disabled="form.processing">Отправить заявку</Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
