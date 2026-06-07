<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Loader2 } from 'lucide-vue-next';
import PostCard from '@/components/feed/PostCard.vue';
import { useInfiniteScroll } from '@/composables/useInfiniteScroll';
import type { FeedItem } from '@/types/feed';

type Props = {
    items: FeedItem[];
    nextCursor: string | null;
};

const props = defineProps<Props>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Лента', href: '/feed' }],
    },
});

const { items, loading, hasMore, sentinel } = useInfiniteScroll(props.items, props.nextCursor, '/api/V1/feed');
</script>

<template>
    <Head title="Лента" />

    <div class="mx-auto max-w-2xl space-y-6 p-4">
        <h1 class="text-2xl font-semibold">Лента</h1>

        <div v-if="items.length === 0 && !loading" class="py-12 text-center text-muted-foreground">Публикаций пока нет</div>

        <div class="flex flex-col gap-4">
            <PostCard v-for="item in items" :key="item.id" :item="item" />
        </div>

        <div ref="sentinel" class="flex justify-center py-4">
            <Loader2 v-if="loading" class="h-5 w-5 animate-spin text-muted-foreground" />
            <p v-else-if="!hasMore && items.length > 0" class="text-sm text-muted-foreground">Больше публикаций нет</p>
        </div>
    </div>
</template>
