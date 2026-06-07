<script setup lang="ts">
import type { PostFeedItem } from '@/types/feed';

defineProps<{ item: PostFeedItem }>();

function formatDate(iso: string): string {
    return new Date(iso).toLocaleDateString('ru-RU', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
}
</script>

<template>
    <article class="border border-border bg-card p-4">
        <div class="mb-4 flex items-center gap-2 text-sm text-muted-foreground">
            <span class="font-medium text-foreground">{{ item.author.name }}</span>
            <span>·</span>
            <time :datetime="item.published_at">{{ formatDate(item.published_at) }}</time>
        </div>
        <h2 class="mb-4 text-xl font-semibold leading-snug">{{ item.title }}</h2>
        <div class="prose max-w-none text-base text-muted-foreground" v-html="item.content" />
    </article>
</template>
