<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import { CalendarDays, Table2, UserPlus, Users, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import UserPopover from '@/components/UserPopover.vue';
import { getInitials } from '@/composables/useInitials';
import type { Reservation, ReservationUser } from '@/types/reservation';

const props = defineProps<{
    reservation: Reservation;
    authUserId: number;
    allUsers: ReservationUser[];
    isAdmin: boolean;
}>();

const addOpen = ref(false);
const selectedUserId = ref('');

const isMine = computed(() => props.reservation.users.some((u) => u.id === props.authUserId));

const availableUsers = computed(() => {
    const ids = new Set(props.reservation.users.map((u) => u.id));

    return props.allUsers.filter((u) => !ids.has(u.id));
});

function formatDate(date: string): string {
    const normalized = date.replace(' ', 'T');
    const timePart = normalized.substring(11, 16);
    const dateStr = new Date(normalized).toLocaleDateString('ru-RU', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
    });

    return timePart && timePart !== '00:00' ? `${dateStr}, ${timePart}` : dateStr;
}

function triggerFallbackClass(userId: number): string {
    return userId === props.authUserId ? 'bg-primary text-primary-foreground' : 'bg-muted-foreground/20 text-foreground';
}

function addParticipant(): void {
    if (!selectedUserId.value) {
return;
}

    useForm({}).put(`/reservations/${props.reservation.id}/user/${selectedUserId.value}`, {
        onSuccess: () => {
            selectedUserId.value = '';
            addOpen.value = false;
        },
    });
}

function removeParticipant(userId: number): void {
    useForm({}).delete(`/reservations/${props.reservation.id}/user/${userId}`);
}

function deleteReservation(): void {
    if (confirm('Удалить резервирование?')) {
        useForm({}).delete(`/reservations/${props.reservation.id}`);
    }
}
</script>

<template>
    <Card>
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

            <!-- Участники -->
            <div class="pt-1">
                <div class="mb-2 flex items-center justify-between">
                    <p class="flex items-center gap-1 text-xs text-muted-foreground">
                        <Users class="h-3.5 w-3.5" />
                        Участники ({{ reservation.users.length }})
                    </p>
                    <button
                        v-if="isMine && availableUsers.length > 0"
                        type="button"
                        class="flex items-center gap-1 rounded text-xs text-muted-foreground hover:text-foreground"
                        @click="addOpen = !addOpen"
                    >
                        <UserPlus class="h-3.5 w-3.5" />
                        Добавить
                    </button>
                </div>

                <div class="flex flex-wrap gap-1.5">
                    <div
                        v-for="user in reservation.users"
                        :key="user.id"
                        class="user-tag pl-0.5"
                        :class="isMine ? 'pr-1' : 'pr-2.5'"
                    >
                        <UserPopover :user="user" :is-current-user="user.id === authUserId">
                            <button type="button" class="flex items-center gap-1">
                                <Avatar class="h-5 w-5">
                                    <AvatarImage v-if="user.avatar" :src="user.avatar" :alt="user.name" />
                                    <AvatarFallback
                                        class="text-[10px] font-semibold"
                                        :class="triggerFallbackClass(user.id)"
                                    >
                                        {{ getInitials(user.name) }}
                                    </AvatarFallback>
                                </Avatar>
                                <span class="max-w-[90px] truncate font-medium">{{ user.name }}</span>
                            </button>
                        </UserPopover>
                        <button
                            v-if="isMine"
                            type="button"
                            class="ml-0.5 rounded-full p-0.5 text-muted-foreground hover:bg-destructive/10 hover:text-destructive"
                            :title="`Убрать ${user.name}`"
                            @click="removeParticipant(user.id)"
                        >
                            <X class="h-3 w-3" />
                        </button>
                    </div>
                </div>

                <!-- Форма добавления участника -->
                <div v-if="isMine && addOpen" class="mt-2 flex gap-1.5">
                    <select v-model="selectedUserId" class="select-input">
                        <option value="" disabled selected>Выбрать участника</option>
                        <option v-for="user in availableUsers" :key="user.id" :value="String(user.id)">
                            {{ user.name }}
                        </option>
                    </select>
                    <Button size="sm" class="h-7 px-2 text-xs" :disabled="!selectedUserId" @click="addParticipant">
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
                    <Button variant="outline" size="sm" class="btn-danger" @click="deleteReservation">
                        Удалить
                    </Button>
                </template>
            </div>
        </CardContent>
    </Card>
</template>
