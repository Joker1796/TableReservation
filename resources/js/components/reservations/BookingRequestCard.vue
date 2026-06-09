<script setup lang="ts">
import { CalendarDays, Table2, Users } from 'lucide-vue-next';
import RequestStatusBadge from '@/components/RequestStatusBadge.vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
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
                    <Popover v-for="user in request.users" :key="user.id">
                        <PopoverTrigger as-child>
                            <button type="button" class="user-tag px-2.5">
                                <Avatar class="h-5 w-5">
                                    <AvatarImage v-if="user.avatar" :src="user.avatar" :alt="user.name" />
                                    <AvatarFallback class="text-[10px] font-semibold" :class="user.id === authUserId ? 'bg-primary text-primary-foreground' : 'bg-muted-foreground/20 text-foreground'">{{ getInitial(user.name) }}</AvatarFallback>
                                </Avatar>
                                <span class="max-w-[90px] truncate font-medium">{{ user.name }}</span>
                            </button>
                        </PopoverTrigger>
                        <PopoverContent class="w-56 p-3 text-sm">
                            <div class="flex items-center gap-2.5">
                                <div
                                    class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-sm font-semibold"
                                    :class="user.id === authUserId ? 'bg-primary text-primary-foreground' : 'bg-muted-foreground/20 text-foreground'"
                                >
                                    {{ getInitial(user.name) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="truncate font-medium">{{ user.name }}</p>
                                    <p class="truncate text-xs text-muted-foreground">{{ user.email }}</p>
                                </div>
                            </div>
                        </PopoverContent>
                    </Popover>
                </div>
            </div>
        </CardContent>
    </Card>
</template>
