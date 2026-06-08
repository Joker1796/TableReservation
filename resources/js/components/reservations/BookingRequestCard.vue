<script setup lang="ts">
import { CalendarDays, Table2, Users } from 'lucide-vue-next';
import RequestStatusBadge from '@/components/RequestStatusBadge.vue';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import type { BookingRequest } from '@/types/reservation';

defineProps<{
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

function getInitial(name: string): string {
    return name.charAt(0).toUpperCase();
}
</script>

<template>
    <Card>
        <CardHeader class="pb-3">
            <div class="flex items-start justify-between">
                <div>
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
                    <div
                        v-for="user in request.users"
                        :key="user.id"
                        class="user-tag px-2.5"
                        :title="user.email"
                    >
                        <div
                            class="user-avatar-sm"
                            :class="
                                user.id === authUserId
                                    ? 'bg-primary text-primary-foreground'
                                    : 'bg-muted-foreground/20 text-foreground'
                            "
                        >
                            {{ getInitial(user.name) }}
                        </div>
                        <span class="max-w-[90px] truncate font-medium">{{ user.name }}</span>
                    </div>
                </div>
            </div>
        </CardContent>
    </Card>
</template>
