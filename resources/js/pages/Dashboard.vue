<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { CalendarDays, Clock, Plus, Table2 } from 'lucide-vue-next';
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { DateInput } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import UserPicker from '@/components/UserPicker.vue';
import { dashboard, home } from '@/routes';
import type { BookingRequest, ReservationTable, ReservationUser } from '@/types/reservation';

type Props = {
    tables: ReservationTable[];
    myRequests: BookingRequest[];
    users: ReservationUser[];
};

defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Главная', href: home() }, { title: 'Панель управления', href: dashboard() }],
    },
});

const statusLabel: Record<number, string> = { 0: 'Новая', 1: 'Одобрена', 2: 'Отклонена' };
const statusVariant: Record<number, 'default' | 'secondary' | 'destructive' | 'outline'> = {
    0: 'secondary',
    1: 'default',
    2: 'destructive',
};

const showForm = ref(false);

const form = useForm({
    date: '',
    comment: '' as string | null,
    table_id: null as number | null,
    user_ids: [] as number[],
});

function submit(): void {
    form.post('/booking-requests', {
        onSuccess: () => {
            showForm.value = false;
            form.reset();
        },
    });
}

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('ru-RU', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    });
}
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex flex-col gap-8 p-4">
        <!-- New request -->
        <div>
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold">Мои заявки</h2>
                <Button size="sm" @click="showForm = !showForm">
                    <Plus class="h-4 w-4" />
                    Новая заявка
                </Button>
            </div>

            <Card v-if="showForm" class="mb-4">
                <CardHeader class="pb-4">
                    <CardTitle class="text-base">Заявка на бронирование</CardTitle>
                    <CardDescription>Укажите дату, продолжительность и предпочтительный стол</CardDescription>
                </CardHeader>
                <CardContent>
                    <form class="space-y-4" @submit.prevent="submit">
                        <div class="grid gap-2">
                            <Label for="date">Дата <span class="text-destructive">*</span></Label>
                            <DateInput id="date" v-model="form.date" required />
                            <InputError :message="form.errors.date" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="table_id">Предпочтительный стол</Label>
                            <Select
                                :model-value="form.table_id ? String(form.table_id) : undefined"
                                @update:model-value="(v) => form.table_id = v ? Number(v) : null"
                            >
                                <SelectTrigger id="table_id">
                                    <SelectValue placeholder="Любой доступный стол" />
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
                            <UserPicker :users="users" :model-value="form.user_ids" @update:model-value="form.user_ids = $event" />
                            <InputError :message="form.errors.user_ids" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="comment">Комментарий</Label>
                            <textarea
                                id="comment"
                                v-model="form.comment"
                                rows="2"
                                placeholder="Дополнительные пожелания..."
                                class="flex min-h-[60px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                            />
                            <InputError :message="form.errors.comment" />
                        </div>
                        <div class="flex items-center gap-3">
                            <Button type="submit" :disabled="form.processing">Отправить заявку</Button>
                            <Button type="button" variant="outline" @click="showForm = false">Отмена</Button>
                        </div>
                    </form>
                </CardContent>
            </Card>

            <div v-if="myRequests.length === 0 && !showForm" class="flex flex-col items-center justify-center rounded-xl border border-dashed border-sidebar-border/70 py-14 text-center dark:border-sidebar-border">
                <CalendarDays class="mb-3 h-10 w-10 text-muted-foreground/50" />
                <p class="text-muted-foreground">Заявок пока нет</p>
                <p class="mt-1 text-sm text-muted-foreground">Нажмите «Новая заявка», чтобы забронировать стол</p>
            </div>

            <div v-else-if="myRequests.length > 0" class="grid gap-3 md:grid-cols-2 lg:grid-cols-3">
                <Card v-for="req in myRequests" :key="req.id">
                    <CardContent class="pt-4">
                        <div class="mb-3 flex items-start justify-between">
                            <div class="space-y-1">
                                <div class="flex items-center gap-1.5 text-sm font-medium">
                                    <CalendarDays class="h-3.5 w-3.5 text-muted-foreground" />
                                    {{ formatDate(req.date) }}
                                </div>
                                <div v-if="req.hours" class="flex items-center gap-1.5 text-sm text-muted-foreground">
                                    <Clock class="h-3.5 w-3.5" />
                                    {{ req.hours }} ч.
                                </div>
                                <div v-if="req.table" class="flex items-center gap-1.5 text-sm text-muted-foreground">
                                    <Table2 class="h-3.5 w-3.5" />
                                    {{ req.table.name }}
                                </div>
                            </div>
                            <Badge :variant="statusVariant[req.status]">
                                {{ statusLabel[req.status] }}
                            </Badge>
                        </div>
                        <p v-if="req.comment" class="line-clamp-2 text-xs text-muted-foreground">
                            {{ req.comment }}
                        </p>
                        <div v-if="req.users.length > 0" class="mt-3 flex flex-wrap gap-1">
                            <span
                                v-for="user in req.users"
                                :key="user.id"
                                class="inline-flex items-center rounded-full bg-muted px-2 py-0.5 text-xs text-muted-foreground"
                            >
                                {{ user.name }}
                            </span>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>
