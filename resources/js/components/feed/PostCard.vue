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
    <article class="rounded-lg border border-border bg-card p-5 shadow-sm">
        <div class="mb-3 flex items-center gap-2 text-sm text-muted-foreground">
            <span class="font-medium text-foreground">{{ item.author.name }}</span>
            <span>·</span>
            <time :datetime="item.published_at">{{ formatDate(item.published_at) }}</time>
        </div>
        <h2 class="mb-3 text-lg font-semibold leading-snug">{{ item.title }}</h2>
        <div class="prose prose-sm max-w-none text-muted-foreground" v-html="item.content" />
    </article>
</template>
