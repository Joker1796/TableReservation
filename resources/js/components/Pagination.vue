<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import type { PaginationLink } from '@/types/pagination';

defineProps<{ links: PaginationLink[] }>();

function decodeLabel(label: string): string {
    return label
        .replace('&laquo;', '«')
        .replace('&raquo;', '»');
}
</script>

<template>
    <div class="flex items-center justify-center gap-1">
        <template v-for="link in links" :key="link.label">
            <Button
                v-if="link.url"
                :variant="link.active ? 'default' : 'outline'"
                size="sm"
                class="h-8 min-w-8 px-2 text-xs"
                as-child
            >
                <Link :href="link.url" preserve-scroll>{{ decodeLabel(link.label) }}</Link>
            </Button>
            <Button
                v-else
                variant="outline"
                size="sm"
                class="h-8 min-w-8 px-2 text-xs"
                disabled
            >
                {{ decodeLabel(link.label) }}
            </Button>
        </template>
    </div>
</template>
