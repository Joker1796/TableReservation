<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Loader2 } from 'lucide-vue-next';
import EventModal from '@/components/feed/EventModal.vue';
import PostCard from '@/components/feed/PostCard.vue';
import { useInfiniteScroll } from '@/composables/useInfiniteScroll';
import { home } from '@/routes';
import type { Event } from '@/types/event';
import type { FeedItem } from '@/types/feed';

type Props = {
    items: FeedItem[];
    nextCursor: string | null;
    upcomingEvents: Event[];
    recentEvents: Event[];
};

const props = defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Главная', href: home() },
            { title: 'Лента', href: '/feed' },
        ],
    },
});

const { items, loading, hasMore, sentinel } = useInfiniteScroll(props.items, props.nextCursor, '/api/V1/feed');
</script>

<template>
    <Head title="Лента" />

    <div class="flex items-start gap-6 p-4">
        <section class="min-w-0 flex-1">
            <h1 class="mb-4 text-2xl font-semibold">Лента</h1>

            <div v-if="items.length === 0 && !loading" class="py-12 text-center text-muted-foreground">Публикаций пока нет</div>

            <div class="flex flex-col gap-2">
                <PostCard v-for="item in items" :key="item.id" :item="item" />
            </div>

            <div ref="sentinel" class="flex justify-center py-4">
                <Loader2 v-if="loading" class="h-5 w-5 animate-spin text-muted-foreground" />
                <p v-else-if="!hasMore && items.length > 0" class="text-sm text-muted-foreground">Больше публикаций нет</p>
            </div>
        </section>

        <aside class="sticky top-4 w-56 shrink-0 space-y-3">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-muted-foreground">Ближайшие события</h2>

            <div v-if="upcomingEvents.length === 0 && recentEvents.length === 0" class="rounded-lg border border-border bg-card p-4 text-sm text-muted-foreground">
                События появятся здесь
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
        </aside>
    </div>
</template>
