<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Loader2 } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { appBreadcrumbs } from '@/breadcrumbs/app';
import EventsList from '@/components/feed/EventsList.vue';
import FeedCreateMenu from '@/components/feed/FeedCreateMenu.vue';
import PollCard from '@/components/feed/PollCard.vue';
import PostCard from '@/components/feed/PostCard.vue';
import { Button } from '@/components/ui/button';
import type { Event } from '@/types/event';
import type { FeedItem, PollFeedItem, PostFeedItem } from '@/types/feed';

type Props = {
    items: FeedItem[];
    nextCursor: string | null;
    upcomingEvents: Event[];
    recentEvents: Event[];
};

const props = defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: appBreadcrumbs.feed,
    },
});

const items = ref<FeedItem[]>([...props.items]);
const cursor = ref<string | null>(props.nextCursor);
const loading = ref(false);
const hasMore = ref(props.nextCursor !== null);

watch(
    () => props.items,
    (newItems) => {
        items.value = [...newItems];
        cursor.value = props.nextCursor;
        hasMore.value = props.nextCursor !== null;
    },
);

async function loadMore(): Promise<void> {
    if (loading.value || !hasMore.value || !cursor.value) {
        return;
    }

    loading.value = true;

    try {
        const url = `/feed?cursor=${encodeURIComponent(cursor.value)}`;
        const response = await fetch(url, {
            credentials: 'same-origin',
            headers: { Accept: 'application/json' },
        });

        if (!response.ok) {
            return;
        }

        const data = await response.json();
        items.value.push(...(data.data as FeedItem[]));
        cursor.value = data.next_cursor ?? null;
        hasMore.value = data.next_cursor !== null;
    } catch {
        // stop on error
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <Head title="Лента" />

    <div class="flex flex-col gap-4 p-4 lg:flex-row lg:items-start lg:gap-6">
        <section class="min-w-0 w-full flex-1">
            <div class="mb-4 flex items-center justify-between">
                <h1 class="text-2xl font-semibold">Лента</h1>
                <FeedCreateMenu />
            </div>

            <div v-if="items.length === 0 && !loading" class="py-12 text-center text-muted-foreground">Публикаций пока нет</div>

            <div class="flex flex-col gap-2">
                <template v-for="item in items" :key="`${item.type}-${item.id}`">
                    <PollCard v-if="item.type === 'poll'" :item="(item as PollFeedItem)" />
                    <PostCard v-else :item="(item as PostFeedItem)" />
                </template>
            </div>

            <div class="flex justify-center py-4">
                <Loader2 v-if="loading" class="h-5 w-5 animate-spin text-muted-foreground" />
                <Button v-else-if="hasMore" variant="outline" @click="loadMore">Подгрузить ещё</Button>
                <p v-else-if="items.length > 0" class="text-sm text-muted-foreground">Больше публикаций нет</p>
            </div>
        </section>

        <aside class="hidden lg:block lg:sticky lg:top-4 lg:w-56 lg:shrink-0 space-y-3">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-muted-foreground">Ближайшие события</h2>
            <EventsList :upcoming-events="upcomingEvents" :recent-events="recentEvents" :show-suggest="false" />
        </aside>
    </div>
</template>
