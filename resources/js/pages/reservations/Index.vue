<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import { CalendarDays, ClipboardList, Plus } from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';
import { reservationBreadcrumbs } from '@/breadcrumbs/reservations';
import BookingRequestModal from '@/components/BookingRequestModal.vue';
import BookingRequestCard from '@/components/reservations/BookingRequestCard.vue';
import ReservationCard from '@/components/reservations/ReservationCard.vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { Tooltip, TooltipContent, TooltipProvider, TooltipTrigger } from '@/components/ui/tooltip';
import { useHasContacts } from '@/composables/useHasContacts';
import type { Auth } from '@/types/auth';
import type { BookingRequest, Reservation, ReservationUser } from '@/types/reservation';

type Props = {
    reservations: Reservation[];
    authUserId: number;
    users: ReservationUser[];
    myReservationDates: string[];
    myRequestDates: string[];
    myInviteDates: string[];
    myBookingRequests: BookingRequest[];
    bookingRequestsToday: number;
};

const props = defineProps<Props>();

const loading = ref(false);

const page = usePage<{ auth: Auth }>();
const isAdmin = computed(() => page.props.auth?.user?.is_admin === true);
const hasContacts = useHasContacts();
const bookingLimitReached = computed(() => !isAdmin.value && props.bookingRequestsToday >= 3);
const isBookingDisabled = computed(() => !hasContacts.value || bookingLimitReached.value);
const bookingDisabledReason = computed(() => {
    if (!hasContacts.value) {
return 'Заполните контактные данные в профиле';
}

    if (bookingLimitReached.value) {
return 'Лимит заявок на сегодня исчерпан (максимум 3)';
}

    return null;
});

defineOptions({
    layout: {
        breadcrumbs: reservationBreadcrumbs.index,
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
    chipRefs.value[activeDate.value]?.scrollIntoView({
        behavior: 'instant',
        block: 'nearest',
        inline: 'center',
    });
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
            only: ['reservations', 'myBookingRequests'],
            onFinish: () => {
                loading.value = false;
            },
        },
    );
}
</script>

<template>
    <Head title="Резервирования" />

    <div class="flex h-full flex-1 flex-col gap-4 p-4">
        <!-- Header -->
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold">Резервирования</h1>
                <p class="text-sm text-muted-foreground">Бронирования столов по датам</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <TooltipProvider>
                    <Tooltip>
                        <TooltipTrigger as-child>
                            <span>
                                <BookingRequestModal>
                                    <Button variant="outline" :disabled="isBookingDisabled">Забронировать стол</Button>
                                </BookingRequestModal>
                            </span>
                        </TooltipTrigger>
                        <TooltipContent v-if="bookingDisabledReason">{{ bookingDisabledReason }}</TooltipContent>
                    </Tooltip>
                </TooltipProvider>
                <Button v-if="isAdmin" as-child>
                    <Link href="/reservations/create">
                        <Plus class="h-4 w-4" />
                        Создать
                    </Link>
                </Button>
            </div>
        </div>

        <!-- Date strip -->
        <div class="flex gap-1 overflow-x-auto pb-1" style="scrollbar-width: none">
            <button
                v-for="(date, i) in stripDates"
                :key="date"
                :ref="(el) => { if (el) chipRefs[date] = el as HTMLElement; }"
                class="flex min-w-[48px] flex-col items-center rounded-xl px-2.5 py-2 text-center transition-colors focus:outline-none"
                :class="date === activeDate ? 'bg-foreground text-background' : 'text-muted-foreground hover:bg-muted hover:text-foreground'"
                @click="selectDate(date)"
            >
                <span class="text-[10px] leading-none font-medium uppercase">
                    {{ showMonth(date, i) ? monthName(date) : dayName(date) }}
                </span>
                <span class="mt-1 text-base leading-none font-bold">{{ dayNumber(date) }}</span>
                <div class="mt-1 flex min-h-[4px] items-center justify-center gap-0.5">
                    <span v-if="date === todayStr" class="h-1 w-1 rounded-full" :class="date === activeDate ? 'bg-background' : 'bg-primary'" />
                    <span v-if="myReservationDates.includes(date)" class="h-1 w-1 rounded-full" :class="date === activeDate ? 'bg-background/60' : 'bg-green-500'" />
                    <span v-if="myRequestDates.includes(date)" class="h-1 w-1 rounded-full" :class="date === activeDate ? 'bg-background/60' : 'bg-blue-500'" />
                    <span v-if="myInviteDates.includes(date)" class="h-1 w-1 rounded-full" :class="date === activeDate ? 'bg-background/60' : 'bg-amber-500'" />
                </div>
            </button>
        </div>

        <!-- Loading state -->
        <div v-if="loading" class="flex flex-col items-center justify-center py-20">
            <Spinner class="h-8 w-8 text-muted-foreground" />
        </div>

        <template v-else>
            <!-- My booking requests -->
            <div v-if="myBookingRequests.length > 0" class="space-y-3">
                <h2 class="flex items-center gap-2 text-base font-semibold">
                    <ClipboardList class="h-4 w-4 text-muted-foreground" />
                    Мои заявки
                </h2>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <BookingRequestCard
                        v-for="request in myBookingRequests"
                        :key="request.id"
                        :request="request"
                        :auth-user-id="authUserId"
                    />
                </div>
            </div>

            <!-- Empty state -->
            <div v-if="reservations.length === 0" class="empty-state">
                <CalendarDays class="mb-4 h-12 w-12 text-muted-foreground/50" />
                <p class="text-lg font-medium text-muted-foreground">Нет резервирований</p>
                <p class="mt-1 text-sm text-muted-foreground">На выбранную дату бронирований нет</p>
                <Button v-if="isAdmin" class="mt-4" as-child>
                    <Link href="/reservations/create">Создать резервирование</Link>
                </Button>
            </div>

            <!-- Reservations grid -->
            <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <ReservationCard
                    v-for="reservation in reservations"
                    :key="reservation.id"
                    :reservation="reservation"
                    :auth-user-id="authUserId"
                    :all-users="users"
                    :is-admin="isAdmin"
                />
            </div>
        </template>
    </div>
</template>
