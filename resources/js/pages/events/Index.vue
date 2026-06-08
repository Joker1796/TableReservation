<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import EventsList from '@/components/feed/EventsList.vue';
import { home } from '@/routes';
import type { Event } from '@/types/event';

defineProps<{
    upcomingEvents: Event[];
    recentEvents: Event[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Главная', href: home() },
            { title: 'События', href: '/events' },
        ],
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
