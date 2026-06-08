<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import EventsList from '@/components/feed/EventsList.vue';
import { appBreadcrumbs } from '@/breadcrumbs/app';
import type { Event } from '@/types/event';

defineProps<{
    upcomingEvents: Event[];
    recentEvents: Event[];
}>();

defineOptions({
    layout: {
        breadcrumbs: appBreadcrumbs.events,
    },
});
</script>

<template>
    <Head title="События" />

    <div class="flex flex-col gap-6 p-4">
        <div>
            <h1 class="text-2xl font-semibold">События</h1>
            <p class="text-sm text-muted-foreground">Предстоящие и прошедшие мероприятия клуба</p>
        </div>

        <EventsList :upcoming-events="upcomingEvents" :recent-events="recentEvents" />
    </div>
</template>
