<script setup lang="ts">
import { useForm, usePage } from '@inertiajs/vue3';
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
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import UserPicker from '@/components/UserPicker.vue';
import { store } from '@/routes/booking-requests';
import type { ReservationTable, ReservationUser } from '@/types/reservation';

type BookingFormData = {
    tables: ReservationTable[];
    users: ReservationUser[];
} | null;

const page = usePage<{ bookingFormData: BookingFormData }>();
const open = ref(false);

const form = useForm({
    date: '',
    comment: '' as string | null,
    table_id: null as number | null,
    user_ids: [] as number[],
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
                    Укажите дату и дополнительные пожелания. Администратор подтвердит заявку.
                </DialogDescription>
            </DialogHeader>
            <form class="space-y-4" @submit.prevent="submit">
                <!-- Date -->
                <div class="grid gap-2">
                    <Label for="modal-date">Дата <span class="text-destructive">*</span></Label>
                    <DateInput id="modal-date" v-model="form.date" required />
                    <InputError :message="form.errors.date" />
                </div>

                <!-- Table -->
                <div v-if="page.props.bookingFormData?.tables?.length" class="grid gap-2">
                    <Label>Стол</Label>
                    <Select
                        :model-value="form.table_id ? String(form.table_id) : undefined"
                        @update:model-value="(v) => (form.table_id = v ? Number(v) : null)"
                    >
                        <SelectTrigger>
                            <SelectValue placeholder="Не выбран" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="table in page.props.bookingFormData.tables"
                                :key="table.id"
                                :value="String(table.id)"
                            >
                                {{ table.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.table_id" />
                </div>

                <!-- Participants -->
                <div v-if="page.props.bookingFormData?.users?.length" class="grid gap-2">
                    <Label>Участники</Label>
                    <UserPicker
                        :users="page.props.bookingFormData.users"
                        :model-value="form.user_ids"
                        @update:model-value="form.user_ids = $event"
                    />
                    <InputError :message="form.errors.user_ids" />
                </div>

                <!-- Comment -->
                <div class="grid gap-2">
                    <Label for="modal-comment">Комментарий</Label>
                    <Textarea
                        id="modal-comment"
                        v-model="form.comment"
                        rows="3"
                        placeholder="Дополнительные пожелания..."
                        class="min-h-[72px]"
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
