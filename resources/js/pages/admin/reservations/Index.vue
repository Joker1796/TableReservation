<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { Trash2, UserPlus, Users } from 'lucide-vue-next';
import { ref } from 'vue';
import Pagination from '@/components/Pagination.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import UserPicker from '@/components/UserPicker.vue';
import type { Paginated } from '@/types/pagination';
import type { Reservation, ReservationUser } from '@/types/reservation';

type Props = {
    reservations: Paginated<Reservation>;
    users: ReservationUser[];
};

defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Админ', href: '/admin/tables' },
            { title: 'Резервирования', href: '/admin/reservations' },
        ],
    },
});

const inviteDialogOpen = ref(false);
const inviteReservationId = ref<number | null>(null);

const inviteForm = useForm({
    user_ids: [] as number[],
});

function openInviteDialog(reservationId: number): void {
    inviteReservationId.value = reservationId;
    inviteForm.reset();
    inviteDialogOpen.value = true;
}

function sendInvite(): void {
    if (!inviteReservationId.value) {
        return;
    }

    inviteForm.post(`/admin/reservations/${inviteReservationId.value}/invite`, {
        onSuccess: () => {
            inviteDialogOpen.value = false;
        },
    });
}

function formatDate(date: string): string {
    return new Date(date).toLocaleDateString('ru-RU', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
}

function deleteReservation(id: number): void {
    if (confirm('Удалить резервирование?')) {
        router.delete(`/admin/reservations/${id}`);
    }
}
</script>

<template>
    <Head title="Резервирования" />

    <div class="flex flex-col gap-6 p-4">
        <div>
            <h1 class="text-2xl font-semibold">Резервирования</h1>
            <p class="text-sm text-muted-foreground">Все подтверждённые бронирования</p>
        </div>

        <div v-if="reservations.data.length === 0" class="empty-state">
            <p class="text-muted-foreground">Резервирований пока нет</p>
        </div>

        <div v-else class="flex flex-col gap-4">
            <div class="panel-table">
                <table>
                    <thead>
                        <tr>
                            <th class="col-th text-left">Дата</th>
                            <th class="col-th text-left">Стол</th>
                            <th class="col-th text-left">Участники</th>
                            <th class="col-th text-left">Комментарий</th>
                            <th class="col-th text-right">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="res in reservations.data" :key="res.id">
                            <td class="col-td font-medium">
                                {{ formatDate(res.date) }}
                            </td>
                            <td class="col-td">
                                <span v-if="res.table">
                                    <Badge variant="outline">{{ res.table.name }}</Badge>
                                </span>
                                <span v-else class="text-muted-foreground">—</span>
                            </td>
                            <td class="col-td">
                                <div class="flex items-center gap-1 text-muted-foreground">
                                    <Users class="h-3.5 w-3.5" />
                                    {{ res.users.length }}
                                </div>
                            </td>
                            <td class="col-td max-w-xs text-muted-foreground">
                                <span class="line-clamp-1">{{ res.comment || '—' }}</span>
                            </td>
                            <td class="col-td">
                                <div class="flex justify-end gap-2">
                                    <Button
                                        variant="outline"
                                        size="icon"
                                        title="Пригласить пользователя"
                                        @click="openInviteDialog(res.id)"
                                    >
                                        <UserPlus class="h-4 w-4" />
                                    </Button>
                                    <Button
                                        variant="outline"
                                        size="icon"
                                        class="btn-danger"
                                        @click="deleteReservation(res.id)"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </Button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <Pagination :links="reservations.links" />
        </div>
    </div>

    <!-- Invite dialog -->
    <Dialog v-model:open="inviteDialogOpen">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>Пригласить пользователя</DialogTitle>
                <DialogDescription>Выберите пользователя, которому будет отправлено приглашение</DialogDescription>
            </DialogHeader>
            <div class="grid gap-4 py-2">
                <div class="grid gap-2">
                    <Label>Участники</Label>
                    <UserPicker
                        :users="users"
                        :model-value="inviteForm.user_ids"
                        @update:model-value="(v) => (inviteForm.user_ids = v)"
                    />
                </div>
            </div>
            <DialogFooter>
                <Button variant="outline" @click="inviteDialogOpen = false">Отмена</Button>
                <Button :disabled="inviteForm.user_ids.length === 0 || inviteForm.processing" @click="sendInvite">
                    Отправить приглашение
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
