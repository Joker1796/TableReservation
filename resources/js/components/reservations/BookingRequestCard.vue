<script setup lang="ts">
import { CalendarDays, Table2, Users } from 'lucide-vue-next';
import RequestStatusBadge from '@/components/RequestStatusBadge.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import UserPopover from '@/components/UserPopover.vue';
import { getInitials } from '@/composables/useInitials';
import type { BookingRequest } from '@/types/reservation';

const props = defineProps<{
    request: BookingRequest;
    authUserId: number;
}>();

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
</script>

<template>
    <Card>
        <CardHeader class="pb-3">
            <div class="flex items-start justify-between gap-2">
                <div class="min-w-0">
                    <CardTitle class="text-base">
                        {{ request.table ? request.table.name : 'Стол не выбран' }}
                    </CardTitle>
                    <CardDescription class="mt-1 flex items-center gap-1">
                        <CalendarDays class="h-3.5 w-3.5" />
                        {{ formatDate(request.date) }}
                    </CardDescription>
                </div>
                <div class="flex flex-col items-end gap-1">
                    <RequestStatusBadge :status="(request.status as 0 | 1 | 2)" />
                    <Badge variant="secondary" class="text-xs">
                        {{ request.author_id === authUserId ? 'Автор' : 'Участник' }}
                    </Badge>
                </div>
            </div>
        </CardHeader>
        <CardContent class="space-y-3">
            <div v-if="request.table" class="flex items-center gap-2 text-sm text-muted-foreground">
                <Table2 class="h-3.5 w-3.5" />
                {{ request.table.description || request.table.name }}
            </div>
            <p v-if="request.comment" class="line-clamp-2 text-sm text-muted-foreground">
                {{ request.comment }}
            </p>
            <div v-if="request.users.length > 0" class="pt-1">
                <p class="mb-2 flex items-center gap-1 text-xs text-muted-foreground">
                    <Users class="h-3.5 w-3.5" />
                    Участники ({{ request.users.length }})
                </p>
                <div class="flex flex-wrap gap-1.5">
                    <UserPopover
                        v-for="user in request.users"
                        :key="user.id"
                        :user="user"
                        :is-current-user="user.id === authUserId"
                    >
                        <button type="button" class="user-tag px-2.5">
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
                </div>
            </div>
        </CardContent>
    </Card>
</template>
