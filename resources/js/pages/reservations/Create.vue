<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { onClickOutside } from '@vueuse/core';
import { Check, ChevronDown, Users, X } from 'lucide-vue-next';
import { ref } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { DateInput, Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import type { ReservationTable, ReservationUser } from '@/types/reservation';

type Props = {
    tables: ReservationTable[];
    users: ReservationUser[];
};

defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Резервирования', href: '/reservations' },
            { title: 'Создать', href: '/reservations/create' },
        ],
    },
});

const form = useForm({
    date: '',
    comment: '' as string | null,
    table_id: null as number | null,
    user_ids: [] as number[],
});

const userPickerOpen = ref(false);
const userPickerRef = ref<HTMLElement | null>(null);
onClickOutside(userPickerRef, () => {
 userPickerOpen.value = false; 
});

const userSearch = ref('');

function toggleUser(id: number): void {
    if (form.user_ids.includes(id)) {
        form.user_ids = form.user_ids.filter((uid) => uid !== id);
    } else {
        form.user_ids = [...form.user_ids, id];
    }
}

function submit(): void {
    form.post('/reservations');
}
</script>

<template>
    <Head title="Создать резервирование" />

    <div class="space-y-6 p-4">
        <Heading
            variant="small"
            title="Новое резервирование"
            description="Заполните форму для бронирования стола"
        />

        <form class="max-w-lg space-y-5" @submit.prevent="submit">
            <div class="grid gap-2">
                <Label for="date">Дата <span class="text-destructive">*</span></Label>
                <DateInput id="date" v-model="form.date" required />
                <InputError :message="form.errors.date" />
            </div>

            <div class="grid gap-2">
                <Label for="table_id">Стол</Label>
                <Select
                    :model-value="form.table_id ? String(form.table_id) : undefined"
                    @update:model-value="(v) => form.table_id = v ? Number(v) : null"
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

            <div v-if="users.length > 0" class="grid gap-2">
                <Label>Участники</Label>
                <div ref="userPickerRef" class="relative">
                    <button
                        type="button"
                        class="flex w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
                        @click="userPickerOpen = !userPickerOpen"
                    >
                        <span class="flex items-center gap-2 text-muted-foreground">
                            <Users class="h-4 w-4 shrink-0" />
                            {{ form.user_ids.length === 0 ? 'Добавить участников' : `Выбрано: ${form.user_ids.length}` }}
                        </span>
                        <ChevronDown class="h-4 w-4 shrink-0 text-muted-foreground" :class="{ 'rotate-180': userPickerOpen }" />
                    </button>
                    <div
                        v-if="userPickerOpen"
                        class="absolute z-50 mt-1 w-full rounded-md border bg-popover shadow-md"
                    >
                        <div class="p-2">
                            <Input v-model="userSearch" placeholder="Поиск..." class="h-8 text-sm" />
                        </div>
                        <div class="max-h-48 overflow-y-auto">
                            <button
                                v-for="user in users.filter(u => u.name.toLowerCase().includes(userSearch.toLowerCase()) || u.email.toLowerCase().includes(userSearch.toLowerCase()))"
                                :key="user.id"
                                type="button"
                                class="flex w-full items-center gap-2 px-3 py-2 text-sm hover:bg-accent"
                                @click="toggleUser(user.id)"
                            >
                                <Check
                                    class="h-4 w-4 shrink-0"
                                    :class="form.user_ids.includes(user.id) ? 'text-primary' : 'text-transparent'"
                                />
                                <span class="font-medium">{{ user.name }}</span>
                                <span class="ml-auto text-xs text-muted-foreground">{{ user.email }}</span>
                            </button>
                            <p v-if="!users.some(u => u.name.toLowerCase().includes(userSearch.toLowerCase()) || u.email.toLowerCase().includes(userSearch.toLowerCase()))" class="px-3 py-3 text-center text-sm text-muted-foreground">
                                Ничего не найдено
                            </p>
                        </div>
                    </div>
                </div>
                <div v-if="form.user_ids.length > 0" class="flex flex-wrap gap-1.5">
                    <span
                        v-for="user in users.filter(u => form.user_ids.includes(u.id))"
                        :key="user.id"
                        class="flex items-center gap-1 rounded-full border bg-muted/50 py-0.5 pl-0.5 pr-2 text-xs"
                    >
                        <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-primary text-[10px] font-semibold text-primary-foreground">
                            {{ user.name.charAt(0).toUpperCase() }}
                        </span>
                        <span class="font-medium">{{ user.name }}</span>
                        <button
                            type="button"
                            class="ml-0.5 rounded-full text-muted-foreground hover:text-foreground"
                            @click="toggleUser(user.id)"
                        >
                            <X class="h-3 w-3" />
                        </button>
                    </span>
                </div>
                <InputError :message="form.errors.user_ids" />
            </div>

            <div class="grid gap-2">
                <Label for="comment">Комментарий</Label>
                <Textarea
                    id="comment"
                    v-model="form.comment"
                    rows="3"
                    placeholder="Дополнительная информация..."
                />
                <InputError :message="form.errors.comment" />
            </div>

            <div class="flex items-center gap-3">
                <Button type="submit" :disabled="form.processing">Создать резервирование</Button>
                <Button variant="outline" type="button" as="a" href="/reservations">Отмена</Button>
            </div>
        </form>
    </div>
</template>
