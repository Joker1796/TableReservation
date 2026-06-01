<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { Trash2, UserPlus, Users } from 'lucide-vue-next';
import { ref } from 'vue';
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
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import type { Reservation, ReservationUser } from '@/types/reservation';

type Props = {
    reservations: Reservation[];
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
    user_id: null as number | null,
});

function openInviteDialog(reservationId: number): void {
    inviteReservationId.value = reservationId;
    inviteForm.reset();
    inviteDialogOpen.value = true;
}

function sendInvite(): void {
    if (!inviteReservationId.value) return;
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

        <div v-if="reservations.length === 0" class="flex items-center justify-center rounded-xl border border-dashed border-sidebar-border/70 py-20 text-center dark:border-sidebar-border">
            <p class="text-muted-foreground">Резервирований пока нет</p>
        </div>

        <div v-else class="overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
            <table class="w-full text-sm">
                <thead class="border-b border-sidebar-border/70 bg-muted/40 dark:border-sidebar-border">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-muted-foreground">Дата</th>
                        <th class="px-4 py-3 text-left font-medium text-muted-foreground">Часы</th>
                        <th class="px-4 py-3 text-left font-medium text-muted-foreground">Стол</th>
                        <th class="px-4 py-3 text-left font-medium text-muted-foreground">Участники</th>
                        <th class="px-4 py-3 text-left font-medium text-muted-foreground">Комментарий</th>
                        <th class="px-4 py-3 text-right font-medium text-muted-foreground">Действия</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sidebar-border/70 dark:divide-sidebar-border">
                    <tr v-for="res in reservations" :key="res.id" class="hover:bg-muted/20">
                        <td class="px-4 py-3 font-medium">{{ formatDate(res.date) }}</td>
                        <td class="px-4 py-3 text-muted-foreground">
                            {{ res.hours ? `${res.hours} ч.` : '—' }}
                        </td>
                        <td class="px-4 py-3">
                            <span v-if="res.table">
                                <Badge variant="outline">{{ res.table.name }}</Badge>
                            </span>
                            <span v-else class="text-muted-foreground">—</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1 text-muted-foreground">
                                <Users class="h-3.5 w-3.5" />
                                {{ res.users.length }}
                            </div>
                        </td>
                        <td class="max-w-xs px-4 py-3 text-muted-foreground">
                            <span class="line-clamp-1">{{ res.comment || '—' }}</span>
                        </td>
                        <td class="px-4 py-3">
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
                                    class="text-destructive hover:bg-destructive hover:text-destructive-foreground"
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
                    <Label for="invite-user">Пользователь</Label>
                    <Select
                        :model-value="inviteForm.user_id != null ? String(inviteForm.user_id) : undefined"
                        @update:model-value="(v) => inviteForm.user_id = v != null ? Number(v) : null"
                    >
                        <SelectTrigger id="invite-user">
                            <SelectValue placeholder="Выберите пользователя" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="user in users" :key="user.id" :value="String(user.id)">
                                {{ user.name }} ({{ user.email }})
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>
            </div>
            <DialogFooter>
                <Button variant="outline" @click="inviteDialogOpen = false">Отмена</Button>
                <Button :disabled="inviteForm.user_id == null || inviteForm.processing" @click="sendInvite">
                    Отправить приглашение
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
