<script setup lang="ts">
import EventModal from '@/components/feed/EventModal.vue';
import type { Event } from '@/types/event';

defineProps<{ upcomingEvents: Event[]; recentEvents: Event[] }>();
</script>

<template>
    <div
        v-if="upcomingEvents.length === 0 && recentEvents.length === 0"
        class="rounded-lg border border-border bg-card p-4 text-sm text-muted-foreground"
    >
        Событий пока нет
    </div>

    <template v-else>
        <div v-if="upcomingEvents.length > 0" class="flex flex-col gap-2">
            <EventModal v-for="event in upcomingEvents" :key="event.id" :event="event" />
        </div>

        <template v-if="recentEvents.length > 0">
            <p class="text-xs font-medium uppercase tracking-wide text-muted-foreground/60">Прошедшие</p>
            <div class="flex flex-col gap-2">
                <div v-for="event in recentEvents" :key="event.id" class="opacity-60">
                    <EventModal :event="event" />
                </div>
            </div>
        </template>
    </template>
</template>
