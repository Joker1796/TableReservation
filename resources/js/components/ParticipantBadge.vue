<script setup lang="ts">
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';

defineProps<{
    participant: { id: number; name?: string; phone?: string | null; contacts?: string | null };
}>();
</script>

<template>
    <Popover>
        <PopoverTrigger as-child>
            <button class="inline-block cursor-pointer rounded bg-muted px-1.5 py-0.5 text-xs text-muted-foreground hover:bg-muted/70">
                {{ participant.name }}
            </button>
        </PopoverTrigger>
        <PopoverContent class="w-56 space-y-1.5 p-3 text-sm">
            <p class="font-medium">{{ participant.name }}</p>
            <p v-if="participant.phone" class="text-muted-foreground">{{ participant.phone }}</p>
            <p v-if="participant.contacts" class="text-muted-foreground">{{ participant.contacts }}</p>
            <p v-if="!participant.phone && !participant.contacts" class="text-muted-foreground">Контакты не указаны</p>
        </PopoverContent>
    </Popover>
</template>
