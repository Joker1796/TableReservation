<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import EventModal from '@/components/feed/EventModal.vue';
import SuggestEventModal from '@/components/feed/SuggestEventModal.vue';
import { Button } from '@/components/ui/button';
import type { Auth } from '@/types/auth';
import type { Event } from '@/types/event';

withDefaults(defineProps<{ upcomingEvents: Event[]; recentEvents: Event[]; showSuggest?: boolean }>(), {
    showSuggest: true,
});

const page = usePage<{ auth: Auth }>();
const canCreate = page.props.auth.user.is_admin || page.props.auth.user.is_editor;

const suggestOpen = ref(false);
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

    <template v-if="!canCreate && showSuggest">
        <Button variant="outline" size="sm" class="mt-2 w-full" @click="suggestOpen = true">
            Предложить событие
        </Button>
        <SuggestEventModal :open="suggestOpen" @update:open="suggestOpen = $event" />
    </template>
</template>
