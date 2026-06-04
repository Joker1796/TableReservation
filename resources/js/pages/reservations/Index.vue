<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import { CalendarDays, Plus, Table2, UserPlus, Users, X } from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';
import BookingRequestModal from '@/components/BookingRequestModal.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Spinner } from '@/components/ui/spinner';
import { home } from '@/routes';
import type { Auth } from '@/types/auth';
import type { Reservation, ReservationUser } from '@/types/reservation';

type Props = {
    reservations: Reservation[];
    authUserId: number;
    users: ReservationUser[];
    myReservationDates: string[];
    myRequestDates: string[];
    myInviteDates: string[];
};

const props = defineProps<Props>();

const loading = ref(false);

const page = usePage<{ auth: Auth }>();
const isAdmin = computed(() => page.props.auth?.user?.is_admin === true);

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Главная', href: home() },
            { title: 'Резервирования', href: '/reservations' },
        ],
    },
});

// --- Date strip ---

const today = new Date();
today.setHours(0, 0, 0, 0);

function toDateString(d: Date): string {
    return d.toISOString().substring(0, 10);
}

const todayStr = toDateString(today);
const activeDate = ref(todayStr);

const stripDates = computed<string[]>(() => {
    const dates: string[] = [];

    for (let i = -14; i <= 21; i++) {
        const d = new Date(today);
        d.setDate(today.getDate() + i);
        dates.push(toDateString(d));
    }

    return dates;
});

function dayName(dateStr: string): string {
    return new Date(dateStr).toLocaleDateString('ru-RU', { weekday: 'short' });
}

function dayNumber(dateStr: string): number {
    return new Date(dateStr).getDate();
}

function monthName(dateStr: string): string {
    return new Date(dateStr).toLocaleDateString('ru-RU', { month: 'short' });
}

function showMonth(dateStr: string, index: number): boolean {
    if (index === 0) {
return true;
}

    const prev = stripDates.value[index - 1];

    return new Date(dateStr).getMonth() !== new Date(prev).getMonth();
}

const chipRefs = ref<Record<string, HTMLElement | null>>({});

onMounted(() => {
    chipRefs.value[activeDate.value]?.scrollIntoView({ behavior: 'instant', block: 'nearest', inline: 'center' });
});

function selectDate(date: string): void {
    if (date === activeDate.value) {
        return;
    }

    activeDate.value = date;
    loading.value = true;
    router.get(
        '/reservations',
        { date },
        {
            preserveState: true,
            preserveScroll: true,
            only: ['reservations'],
            onFinish: () => {
 loading.value = false;
},
        },
    );
}

// --- Reservations ---

const addOpen = ref<Record<number, boolean>>({});
const selectedUserId = ref<Record<number, string>>({});

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('ru-RU', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    });
}

function getInitial(name: string): string {
    return name.charAt(0).toUpperCase();
}

function isMine(reservation: Reservation): boolean {
    return reservation.users.some((u) => u.id === props.authUserId);
}

function availableUsers(reservation: Reservation): ReservationUser[] {
    const ids = new Set(reservation.users.map((u) => u.id));

    return props.users.filter((u) => !ids.has(u.id));
}

function addParticipant(reservation: Reservation): void {
    const userId = selectedUserId.value[reservation.id];

    if (!userId) {
        return;
    }

    useForm({}).put(`/reservations/${reservation.id}/user/${userId}`, {
        onSuccess: () => {
            selectedUserId.value[reservation.id] = '';
            addOpen.value[reservation.id] = false;
        },
    });
}

function removeParticipant(reservationId: number, userId: number): void {
    useForm({}).delete(`/reservations/${reservationId}/user/${userId}`);
}

function deleteReservation(id: number): void {
    if (confirm('Удалить резервирование?')) {
        useForm({}).delete(`/reservations/${id}`);
    }
}
</script>

<template>
    <Head title="Резервирования" />

    <div class="flex h-full flex-1 flex-col gap-4 p-4">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold">Резервирования</h1>
                <p class="text-sm text-muted-foreground">Бронирования столов по датам</p>
            </div>
            <div class="flex items-center gap-2">
                <BookingRequestModal>
                    <Button variant="outline">Забронировать стол</Button>
                </BookingRequestModal>
                <Button v-if="isAdmin" as-child>
                    <Link href="/reservations/create">
                        <Plus class="h-4 w-4" />
                        Создать
                    </Link>
                </Button>
            </div>
        </div>

        <!-- Date strip -->
        <div class="flex gap-1 overflow-x-auto pb-1" style="scrollbar-width: none;">
            <button
                v-for="(date, i) in stripDates"
                :key="date"
                :ref="(el) => { if (el) chipRefs[date] = el as HTMLElement }"
                class="flex min-w-[48px] flex-col items-center rounded-xl px-2.5 py-2 text-center transition-colors focus:outline-none"
                :class="date === activeDate
                    ? 'bg-foreground text-background'
                    : 'text-muted-foreground hover:bg-muted hover:text-foreground'"
                @click="selectDate(date)"
            >
                <span class="text-[10px] font-medium uppercase leading-none">
                    {{ showMonth(date, i) ? monthName(date) : dayName(date) }}
                </span>
                <span class="mt-1 text-base font-bold leading-none">{{ dayNumber(date) }}</span>
                <div class="mt-1 flex min-h-[4px] items-center justify-center gap-0.5">
                    <span
                        v-if="date === todayStr"
                        class="h-1 w-1 rounded-full"
                        :class="date === activeDate ? 'bg-background' : 'bg-primary'"
                    />
                    <span
                        v-if="myReservationDates.includes(date)"
                        class="h-1 w-1 rounded-full"
                        :class="date === activeDate ? 'bg-background/60' : 'bg-green-500'"
                    />
                    <span
                        v-if="myRequestDates.includes(date)"
                        class="h-1 w-1 rounded-full"
                        :class="date === activeDate ? 'bg-background/60' : 'bg-blue-500'"
                    />
                    <span
                        v-if="myInviteDates.includes(date)"
                        class="h-1 w-1 rounded-full"
                        :class="date === activeDate ? 'bg-background/60' : 'bg-amber-500'"
                    />
                </div>
            </button>
        </div>

        <!-- Loading state -->
        <div v-if="loading" class="flex flex-col items-center justify-center py-20">
            <Spinner class="h-8 w-8 text-muted-foreground" />
        </div>

        <template v-else>
        <!-- Empty state -->
        <div
            v-if="reservations.length === 0"
            class="flex flex-col items-center justify-center rounded-xl border border-dashed border-sidebar-border/70 py-20 text-center dark:border-sidebar-border"
        >
            <CalendarDays class="mb-4 h-12 w-12 text-muted-foreground/50" />
            <p class="text-lg font-medium text-muted-foreground">Нет резервирований</p>
            <p class="mt-1 text-sm text-muted-foreground">На выбранную дату бронирований нет</p>
            <Button v-if="isAdmin" class="mt-4" as-child>
                <Link href="/reservations/create">Создать резервирование</Link>
            </Button>
        </div>

        <!-- Reservations grid -->
        <div v-else class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            <Card v-for="reservation in reservations" :key="reservation.id">
                <CardHeader class="pb-3">
                    <div class="flex items-start justify-between">
                        <div>
                            <CardTitle class="text-base">
                                {{ reservation.table ? reservation.table.name : 'Стол не выбран' }}
                            </CardTitle>
                            <CardDescription class="mt-1 flex items-center gap-1">
                                <CalendarDays class="h-3.5 w-3.5" />
                                {{ formatDate(reservation.date) }}
                            </CardDescription>
                        </div>
                        <Badge v-if="reservation.table" variant="outline">
                            {{ reservation.table.status === 'ready' ? 'Готов' : 'Не готов' }}
                        </Badge>
                    </div>
                </CardHeader>
                <CardContent class="space-y-3">
                    <div v-if="reservation.table" class="flex items-center gap-2 text-sm text-muted-foreground">
                        <Table2 class="h-3.5 w-3.5" />
                        {{ reservation.table.description || reservation.table.name }}
                    </div>
                    <p v-if="reservation.comment" class="line-clamp-2 text-sm text-muted-foreground">
                        {{ reservation.comment }}
                    </p>

                    <!-- Participants -->
                    <div class="pt-1">
                        <div class="mb-2 flex items-center justify-between">
                            <p class="flex items-center gap-1 text-xs text-muted-foreground">
                                <Users class="h-3.5 w-3.5" />
                                Участники ({{ reservation.users.length }})
                            </p>
                            <button
                                v-if="isMine(reservation) && availableUsers(reservation).length > 0"
                                type="button"
                                class="flex items-center gap-1 rounded text-xs text-muted-foreground hover:text-foreground"
                                @click="addOpen[reservation.id] = !addOpen[reservation.id]"
                            >
                                <UserPlus class="h-3.5 w-3.5" />
                                Добавить
                            </button>
                        </div>

                        <div class="flex flex-wrap gap-1.5">
                            <div
                                v-for="user in reservation.users"
                                :key="user.id"
                                class="flex items-center gap-1 rounded-full border bg-muted/50 py-0.5 pl-0.5 text-xs"
                                :class="isMine(reservation) ? 'pr-1' : 'pr-2.5'"
                                :title="user.email"
                            >
                                <div
                                    class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full text-[10px] font-semibold"
                                    :class="user.id === authUserId ? 'bg-primary text-primary-foreground' : 'bg-muted-foreground/20 text-foreground'"
                                >
                                    {{ getInitial(user.name) }}
                                </div>
                                <span class="max-w-[90px] truncate font-medium">{{ user.name }}</span>
                                <button
                                    v-if="isMine(reservation)"
                                    type="button"
                                    class="ml-0.5 rounded-full p-0.5 text-muted-foreground hover:bg-destructive/10 hover:text-destructive"
                                    :title="`Убрать ${user.name}`"
                                    @click="removeParticipant(reservation.id, user.id)"
                                >
                                    <X class="h-3 w-3" />
                                </button>
                            </div>
                        </div>

                        <!-- Add participant form -->
                        <div v-if="isMine(reservation) && addOpen[reservation.id]" class="mt-2 flex gap-1.5">
                            <select
                                v-model="selectedUserId[reservation.id]"
                                class="flex-1 rounded-md border border-input bg-background px-2 py-1 text-xs ring-offset-background focus:outline-none focus:ring-2 focus:ring-ring"
                            >
                                <option value="" disabled selected>Выбрать участника</option>
                                <option
                                    v-for="user in availableUsers(reservation)"
                                    :key="user.id"
                                    :value="String(user.id)"
                                >
                                    {{ user.name }}
                                </option>
                            </select>
                            <Button
                                size="sm"
                                class="h-7 px-2 text-xs"
                                :disabled="!selectedUserId[reservation.id]"
                                @click="addParticipant(reservation)"
                            >
                                Добавить
                            </Button>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <Button variant="outline" size="sm" as-child class="flex-1">
                            <Link :href="`/reservations/${reservation.id}`">Подробнее</Link>
                        </Button>
                        <template v-if="isAdmin">
                            <Button variant="outline" size="sm" as-child>
                                <Link :href="`/reservations/${reservation.id}/edit`">Изменить</Link>
                            </Button>
                            <Button
                                variant="outline"
                                size="sm"
                                class="text-destructive hover:bg-destructive hover:text-destructive-foreground"
                                @click="deleteReservation(reservation.id)"
                            >
                                Удалить
                            </Button>
                        </template>
                    </div>
                </CardContent>
            </Card>
        </div>
        </template>
    </div>
</template>
