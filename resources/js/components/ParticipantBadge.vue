<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';

defineProps<{
    participant: { id: number; name?: string; email?: string | null; avatar?: string | null; phone?: string | null; contacts?: string | null };
}>();
</script>

<template>
    <Popover>
        <PopoverTrigger as-child>
            <button class="inline-block cursor-pointer rounded bg-muted px-1.5 py-0.5 text-xs text-muted-foreground hover:bg-muted/70">
                {{ participant.name }}
            </button>
        </PopoverTrigger>
        <PopoverContent class="w-56 p-3 text-sm">
            <div class="mb-2 flex items-center gap-2.5">
                <Avatar class="h-8 w-8">
                    <AvatarImage v-if="participant.avatar" :src="participant.avatar" :alt="participant.name" />
                    <AvatarFallback class="text-xs">{{ participant.name?.charAt(0).toUpperCase() ?? '?' }}</AvatarFallback>
                </Avatar>
                <div class="min-w-0">
                    <p class="truncate font-medium">{{ participant.name }}</p>
                    <p v-if="participant.email" class="truncate text-xs text-muted-foreground">{{ participant.email }}</p>
                </div>
            </div>
            <div v-if="participant.phone || participant.contacts" class="space-y-1 border-t pt-2">
                <p v-if="participant.phone" class="text-muted-foreground">{{ participant.phone }}</p>
                <p v-if="participant.contacts" class="text-muted-foreground">{{ participant.contacts }}</p>
            </div>
            <p v-else class="border-t pt-2 text-muted-foreground">Контакты не указаны</p>
        </PopoverContent>
    </Popover>
</template>
